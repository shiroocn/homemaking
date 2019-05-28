<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/14
 * Time: 17:43
 */

namespace app\index\model;


use think\Model;

class Order extends Model
{
    protected $pk='order_id';
    public function getOrderStateAttr($value){
        $state=[0=>'待分配',1=>'已分配'];
        return $state[$value];
    }
}