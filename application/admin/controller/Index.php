<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/28
 * Time: 15:10
 */

namespace app\admin\controller;


class Index extends Base
{
    public function index(){
        return $this->fetch();
    }
}