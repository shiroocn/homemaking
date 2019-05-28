<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/14
 * Time: 16:40
 */

namespace app\index\controller;

use app\index\model\Order as OrderModel;
use app\index\model\Province as ProvinceModel;
use app\index\model\Task as TaskModel;
use think\Db;

class Order extends Base
{
    public function add(){
        if(IS_POST){
            $paramData=$this->request->param();
            //验证数据有效性
            $validate=$this->validate($paramData,'app\index\validate\Order.add');
            if($validate!==true){
                return json_shiroo(101,$validate);
            }

            $phone=$paramData['phone'];
            $taskID=$paramData['task_id'];
            $provinceID=$paramData['province_id'];
            $cityID=$paramData['city_id'];

            $createTime=date('Y-m-d H:i:s');
            $token=sha1($createTime.$phone.rand().'shiroo'.$taskID.$provinceID.$cityID);
            $orderID=date('YmdHis').rand(100,999);

            $where=[
                ['order_phone','=',$phone],
                ['order_task_id','=',$taskID],
                ['order_state','=',0]
            ];

            $or=OrderModel::where($where)->findOrEmpty();
            if(!$or->isEmpty()){
                return json_shiroo(102,'您已预约过，系统正在处理');
            }

            $data=[
                'order_id'=>$orderID,
                'order_phone'=>$phone,
                'order_create_time'=>$createTime,
                'order_task_id'=>$taskID,
                'order_token'=>$token,
                'order_province_id'=>$provinceID,
                'order_city_id'=>$cityID,
                'order_state'=>0
            ];
            try{
                $order=OrderModel::create($data);
            }catch (\Exception $exception){
                return json_shiroo(100,'预约出现异常，请重试',0,$exception);
            }
            if(!$order->isEmpty()){
                //新增数据成功
                return json_shiroo(0,'预约成功，稍候我们会联系您',0);
            }else{
                //新增数据失败
                return json_shiroo(100,'预约失败，请重试',0);
            }
        }else{
            $tasks=TaskModel::select();
            $this->assign('tasks',$tasks);
            return $this->fetch();
        }
    }
    public function show(){
        $paramDate=$this->request->param();
        //验证数据有效性

        $orderToken=$paramDate['token'];

        $where=[
            ['order_token','=',$orderToken]
        ];
        $order=OrderModel::where($where)
            ->join('province','province_id=order_province_id')
            ->join('city','city_id=order_city_id')
            ->join('task','task_id=order_task_id')
            ->find();
        dump($order);
        $this->assign('order',$order);
        return $this->fetch();
    }
    public function province(){
        $paramData=$this->request->param();

        $taskID=$paramData['task_id'];

        try{
            $result=Db::name('taskarea')
                ->field('province_id,province_name')
                ->join('province','province_id=ta_province_id')
                ->join('city','city_id=ta_city_id')
                ->where('ta_task_id',$taskID)
                ->group('ta_province_id')
                ->select();
        }catch (\Exception $exception){
            $result=[];
        }
        return json_shiroo(0,'',count($result),['data'=>$result]);
    }
    public function city(){
        $paramData=$this->request->param();

        $provinceID=$paramData['province_id'];
        try{
            $result=Db::name('city')->where('city_province_id',$provinceID)->select();
        }catch (\Exception $exception){
            $result=[];
        }
        return json_shiroo(0,'',count($result),['data'=>$result]);
    }
}