<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/28
 * Time: 17:21
 */

namespace app\admin\controller;


use think\Db;

class Task extends Base
{
    public function area(){
        $param=$this->request->param();

        $provinceID=$param['province_id'];
        $cityID=$param['city_id'];
        $taskID=$param['task_id'];
        $userID=$param['user_id'];

        $data=[
            'ta_task_id'=>$taskID,
            'ta_province_id'=>$provinceID,
            'ta_city_id'=>$cityID,
            'ta_user_id'=>$userID
        ];
        $where=[
            'ta_task_id'=>$taskID,
            'ta_province_id'=>$provinceID,
            'ta_city_id'=>$cityID,
        ];
        //先查询是否存在相同地区相同的服务，（一个地区同种服务，只能有一个人）
        $area=Db::name('taskarea')->where($where)->find();
        if(empty($area)){
            //如果不存在就直接插入
            $count=Db::name('taskarea')->insert($data);
        }else{
            //存在，
            Db::name('taskarea')->where($where)->update($data);
        }
        return json_shiroo(0,'编辑更新完成');
    }
}