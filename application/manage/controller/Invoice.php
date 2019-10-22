<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Invoice as InvoiceModel;
use think\facade\Request;

/**
 * 发票模块
 * Class Invoice
 * @package app\Manage\controller
 */
class Invoice extends Manage
{
    public function index()
    {
        $invoiceModel = new InvoiceModel();
        if (Request::isAjax()) {
            return $invoiceModel->tableData(input('param.'));
        }
        return $this->fetch();
    }
}