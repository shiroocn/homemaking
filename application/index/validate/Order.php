<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/23
 * Time: 10:33
 */

namespace app\index\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule=[
        'phone'=>['require','mobile'],
        'task_id'=>['require','number','>:0'],
        'province_id'=>['require','number','>:0'],
        'city_id'=>['require','number','>:0']
    ];
    protected $message=[
        'phone.require'=>'手机号码不能为空',
        'phone.mobile'=>'手机号码格式错误',
        'task_id'=>'您没有选择预约的服务',
        'province_id'=>'您没有选择您的所在地区',
        'city_id'=>'您没有选择您的所在地区'
    ];
    protected $scene=[
        'add'=>['phone','task_id','province_id','city_id']
    ];

}