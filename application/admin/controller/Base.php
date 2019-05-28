<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/28
 * Time: 15:11
 */

namespace app\admin\controller;


use think\Controller;

class Base extends Controller
{
    public function initialize(){
        define('IS_POST',$this->request->isPost()?:false);
        define('ACTION_NAME',$this->request->action()?:'');//名称全小写
        define('CONTROLLER_NAME',$this->request->controller()?:'');//与实际名称一样

        //过滤不需要登陆的行为
        if(!in_array(ACTION_NAME,['login'])){
            //如果访问的action不在这个数组里面，则需要登录才能继续。
            if(!$this->isLogin()){
                //没有登录的话是不允许访问，跳转登录页面
                $this->error('请先登录','admin/admin/login');
            }
        }else{
            if($this->isLogin()){
                $this->redirect('admin/index/index');
            }
        }
    }
    protected function isLogin(){
        $uid = (int)session('user.user_id');
        if (session('?user') && $uid > 0) {
            return true;
        } else {
            return false;
        }
    }

}