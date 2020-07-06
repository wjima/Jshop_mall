<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
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
    /**
     * 发票管理
     * @return mixed
     */
    public function index()
    {
        $invoiceModel = new InvoiceModel();
        if (Request::isAjax()) {
            return $invoiceModel->tableData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     * 发票修改
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $return =  error_code(10037);

        $this->view->engine->layout(false);
        $invoiceModel = new InvoiceModel();
        $id = Request::param('id');

        if (Request::isPost()) {
            $data = [
                'type' => Request::param('type'),
                'title' => Request::param('title'),
                'amount' => Request::param('amount'),
                'tax_number' => Request::param('tax_number'),
                'status' => Request::param('status'),
                'remarks' => Request::param('remarks')
            ];

            return $invoiceModel->process($id, $data);
        }

        $info = $invoiceModel->getInfo($id);
        if ($info['status']) {
            $this->assign('info', $info['data']);
            $return['data'] = $this->fetch();
            $return['status'] = true;
            $return['msg'] = '成功';
        } else {
            $return['msg'] = $info['msg'];
        }

        return $return;
    }


    /**
     * 获取信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function show()
    {
        $return =  error_code(10037);

        $this->view->engine->layout(false);
        $invoiceModel = new InvoiceModel();
        $id = Request::param('id');
        $info = $invoiceModel->getInfo($id);
        if ($info['status']) {
            $this->assign('info', $info['data']);
            $return['data'] = $this->fetch();
            $return['status'] = true;
            $return['msg'] = '成功';
        } else {
            $return['msg'] = $info['msg'];
        }

        return $return;
    }


    /**
     * 删除发票
     * @return array
     * @throws \Exception
     */
    public function del()
    {
        $invoiceModel = new InvoiceModel();
        $id = Request::param('id');
        return $invoiceModel->del($id);
    }
}