<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/18 0018
 * Time: 10:49
 */

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\OperationLog as LogModel;
use Request;

class OperationLog extends Manage
{
    public function index()
    {
        $logModel = new LogModel();
        if(Request::isAjax())
        {
            $request = input('param.');
            $request['seller_id'] = $this->sellerId;
            return $logModel->tableData($request);
        }
        return $this->fetch();
    }

    public function getLastLog(){
        $logModel = new LogModel();
        $request['limit'] = 10;//最近10条数据
        $request['seller_id'] = $this->sellerId;
        return $logModel->tableData($request);
    }

}