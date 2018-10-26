<?php

/**
 * 商户的资金管理（余额管理，取店铺创始人的余额）
 */

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Balance as BalanceModel;

use app\common\model\UserTocash;
use Request;


class Balance extends Manage
{
    public function index()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $balanceModel = new BalanceModel();
            return $balanceModel->tableData($data);
        }else{
            return $this->fetch('index');
        }
    }
    public function tocash()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $tocashModel = new UserTocash();
            return $tocashModel->tableData($data);
        }else{
            return $this->fetch('tocash');
        }
    }
    public function tocashexamine(){
        if(!input('param.type')){
            return error_code(10001);
        }
        if(!input('param.id')){
            return error_code(10002);
        }

        $tocashModel = new UserTocash();
        return $tocashModel->examine(input('param.id'),input('param.type'));
    }
}