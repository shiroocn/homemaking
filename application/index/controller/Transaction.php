<?php
/**
 * Created by PhpStorm.
 * User: xkq72
 * Date: 2019/5/15
 * Time: 22:05
 */

namespace app\index\controller;

use app\index\model\Transaction as TransactionModel;
use app\index\model\User as UserModel;

class Transaction extends Base
{
    public function add()
    {
        //添加一条交易记录。如果失败，提示交易失败。终止往下执行。

        //如果添加交易记录成功，
        //按照选择的支付方式进行扣款，
        //如果扣款成功，更新交易记录状态为交易成功，否则更新交易记录状态为失败。
        $uid = 5;
        $transactionAmount = -110;//交易的金额
        $transactionTypeID = 1;//交易类型；充值，消费，
        $transactionModeID = 1;//扣款方式；余额，银行卡，支付宝，微信

        $user = UserModel::get($uid);
        if (!is_null($user)) {
            $userAccountBalance = $user->user_account_balance;//用户余额

            //创建交易记录
            $data = [
                'transaction_number' => '',
                'transaction_user_id' => $uid,
                'transaction_type_id' => $transactionTypeID,
                'transaction_money' => $transactionAmount,
                'transaction_balance_old' => $userAccountBalance,
                'transaction_balance_new' => $userAccountBalance,
                'transaction_create_time' => date('Y-m-d H:i:s'),
                'transaction_mode_id' => $transactionModeID,
                'transaction_state' => 0,
                'transaction_note' => ''
            ];
            try {
                $transaction = TransactionModel::create($data);
            } catch (\Exception $exception) {
                return json_shiroo(101, '创建交易记录失败', 0, ['error' => ['message' => $exception->getMessage()]]);
            }
            if (!$transaction->isEmpty()) {
                //创建交易记录成功
                switch ($transactionTypeID) {
                    case 1://使用余额交易
                        $newUserAccountBalance = $userAccountBalance + $transactionAmount;//交易后的金额
                        if ($newUserAccountBalance >= 0) {//判断变更后的金额必须大于等于0
                            $user->user_account_balance = $newUserAccountBalance;
                            if ($user->save()) {
                                $transaction->transaction_balance_new = $newUserAccountBalance;
                                $transaction->transaction_state = 1;
                                if ($transaction->save()) {
                                    return json_shiroo(0, '交易完成');
                                } else {
                                    return json_shiroo(103, '交易记录状态更新失败');
                                }
                            }else{
                                return json_shiroo(105,'余额交易失败');
                            }
                        } else {
                            //余额不足，
                            return json_shiroo(102, '交易失败，可能余额不足');
                        }
                        break;
                    case 2://使用银行卡交易
                        //银行卡交易接口，
                        $result = true;
                        if ($result) {
                            $transaction->transaction_state = 1;
                            //因为使用银行卡扣款，并非余额扣款，
                            if ($transaction->save()) {
                                return json_shiroo(0, '交易完成');
                            } else {
                                return json_shiroo(103, '交易记录状态更新失败');
                            }
                        } else {
                            //银行卡扣款失败，
                            return json_shiroo(102, '交易失败，可能余额不足');
                        }
                        break;
                    default:
                        return json_shiroo(104,'没有选择交易方式');
                }
            } else {
                return json_shiroo(100, '创建交易记录失败');
            }
        } else {
            return json_shiroo(100, '用户不存在，此次交易终止');
        }
    }
}