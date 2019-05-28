<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/15
 * Time: 15:38
 */

namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Model;

class User extends Base
{
    public function add(){
        if(IS_POST){
            $paramData=$this->request->param();

            $userPhone=$paramData['phone'];
            $userProvinceID=$paramData['province_id'];
            $userCityID=$paramData['city_id'];
            $userTaskID=$paramData['task_id'];

            $data=[
                'user_phone'=>$userPhone,
                'user_province_id'=>$userProvinceID,
                'user_city_id'=>$userCityID,
                'user_account_balance'=>0,
                'user_frozen_funds'=>0,
                'user_task_id'=>$userTaskID
            ];
            try{
                $user=UserModel::create($data);
            }catch (\Exception $exception){
                return json_shiroo(101,'新增数据失败',0,
                    [
                        'error'=>[
                            'message'=>$exception->getMessage()
                        ]
                    ]);
            }
            if(!$user->isEmpty()){
                return json_shiroo(0,'新增用户成功');
            }else{
                return json_shiroo(100,'新增用户失败');
            }
        }else{
            return $this->fetch();
        }
    }
}