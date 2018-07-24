<?php

namespace app\seller\controller;

use app\common\controller\Seller;

use app\common\model\Worksheet as WorksheetModel;
use app\common\model\Wsdetail;

class Worksheet extends Seller
{

    //工单列表
    function worklist()
    {

        return $this->fetch();
    }

    //全部
    function sheetlist()
    {
        $list      = new WorksheetModel();
        $seller_id = $this->sellerId;
        $page      = input("page");
        $limit     = input("limit");
        $rel       = $list->sheetlist($seller_id, $page, $limit);

        foreach ($rel['data'] as $k => $v) {
            $rel['data'][$k]['create_time'] = date("Y-m-d H:i:s", $rel['data'][$k]['create_time']);
        }
        $rels = [
            'code'  => 0,
            'msg'   => '',
            'count' => $rel['count'],
            'data'  => $rel['data'],
        ];
        $this->assign('rel', $rels);
        return $rels;
    }

    //未处理
    function sheetlist1()
    {
        $list      = new WorksheetModel();
        $seller_id = $this->sellerId;
        $sta       = input("status");
        $page      = input("page");
        $limit     = input("limit");
        $rel       = $list->sheetlist1($seller_id, $sta, $page, $limit);

        foreach ($rel['data'] as $k => $v) {
            $rel['data'][$k]['create_time'] = date("Y-m-d H:i:s", $rel['data'][$k]['create_time']);
        }
        $rels = [
            'code'  => 0,
            'msg'   => '',
            'count' => $rel['count'],
            'data'  => $rel['data'],
        ];

        return $rels;
    }

    //处理中
    function sheetlist2()
    {
        $list      = new WorksheetModel();
        $seller_id = $this->sellerId;
        $sta       = input("status");
        $page      = input("page");
        $limit     = input("limit");
        $rel       = $list->sheetlist2($seller_id, $sta, $page, $limit);

        foreach ($rel['data'] as $k => $v) {
            $rel['data'][$k]['create_time'] = date("Y-m-d H:i:s", $rel['data'][$k]['create_time']);
        }
        $rels = [
            'code'  => 0,
            'msg'   => '',
            'count' => $rel['count'],
            'data'  => $rel['data'],
        ];
        return $rels;
    }

    //已处理
    function sheetlist3()
    {
        $list      = new WorksheetModel();
        $seller_id = $this->sellerId;
        $sta       = input("status");
        $page      = input("page");
        $limit     = input("limit");
        $rel       = $list->sheetlist3($seller_id, $sta, $page, $limit);

        foreach ($rel['data'] as $k => $v) {
            $rel['data'][$k]['create_time'] = date("Y-m-d H:i:s", $rel['data'][$k]['create_time']);
        }
        $rels = [
            'code'  => 0,
            'msg'   => '',
            'count' => $rel['count'],
            'data'  => $rel['data'],
        ];
        return $rels;
    }

    //提交工单
    function add()
    {
        return $this->fetch();
    }

    function addwork()
    {
        $result = ['status' => true,'msg' => '提交成功','data' => ''];

        $work   = new WorksheetModel();
        $detail = new Wsdetail();
        $w      = strtoupper(chr(rand(97, 122)) . chr(rand(97, 122)) . ceil(time() * 3 / 4 + 10086));

        $data = [
            'seller_id'   => $this->sellerId,
            'job_num'     => $w,
            'create_time' => time(),
            'type'        => $_POST['type'],
            'content'     => $_POST['content'],
            'title'       => $_POST['title'],
            'phone'       => $_POST['phone'],

        ];

        $record = [
            'create_time' => time(),
            'seller_id'   => $this->sellerId,
            'event'       => $_POST['content'],
            'job_num'     => $w,
            'pic_path'    => _sImage($_POST['pic']),
        ];

        $res = $work->add($data);
        $detail->adddetail($record);
        if (!$res) {
            $result['status'] = false;
            $result['msg']    = '提交失败';
        }
        return $result;

    }

    //工单详情页
    function wsdetail()
    {

        $detail    = new WorksheetModel();
        $id        = input('num_id');
        $seller_id = $this->sellerId;
        $rel       = $detail->wsdetail($id, $seller_id);
        if ($rel != '') {
            $rel['create_time'] = date("Y-m-d H:i:s", $rel['create_time']);
        }

        $info = new Wsdetail();
        $a    = 0;
        $rels = $info->getinfo($seller_id, $id);
        foreach ($rels as $k => $v) {
            $rels[$k]['create_time'] = date("Y-m-d H:i:s", $rels[$k]['create_time']);


            $a++;
            $rels[$k]['pic_id'] = "p" . $a;

            if ($rels[$k]['seller_id'] == 0) {
                $rels[$k]['event'] = "管理员：" . $rels[$k]['event'];
            }
            if ($rels[$k]['seller_id'] == $this->sellerId) {
                $rels[$k]['event'] = "我：" . $rels[$k]['event'];
            }
        }
        $rel['data'] = $rels;
        $this->assign('rel', $rel);


        return $this->fetch();
    }

    //工单详情页添加提问
    function adddetail()
    {

        $record = [
            'create_time' => time(),
            'seller_id'   => $this->sellerId,
            'event'       => $_POST['content'],
            'job_num'     => $_POST['job_num']
            , 'pic_path'  => _sImage($_POST['pic']),
        ];
        $detail = new Wsdetail();
        $rel    = $detail->adddetail($record);
        return $rel;
    }

    //更改工单状态
    function updata()
    {
        $job_num = input("param.job_num");
        $data    = input("param.data");
        if ($data == 1) {
            $updata = new WorksheetModel();
            $record = [
                'create_time' => time(),
                'seller_id'   => $this->sellerId,
                'event'       => "工单已完成",
                'job_num'     => $job_num,
            ];

            $detail = new Wsdetail();
            $detail->adddetail($record);
            $rel = $updata->updata($job_num);
            return $rel;
        }

    }

    //删除工单
    function del()
    {
        $data = input('param.data');
        $id   = input('param.id');
        if ($data == 1) {
            $del = new WorksheetModel();
            $del->del($id);
        }
    }

    function inquiries()
    {
        $this->view->engine->layout(false);
        return $this->fetch();
    }
}