<?php

namespace app\api\controller;

use app\common\model\Brand as BrandModel;
use app\common\controller\Api;

class Brand extends Api
{


    //列表, 默认每页 15条
    public function brandList()
    {
        $brandModel = new BrandModel;
        $data       = [
            'orderType' => input('param.type','asc'),
            'order'     => input('param.order','sort'),
            'page'      => input('param.page',1),
            'pageSize'  => input('param.pageSize',15)
        ];

        $result = $brandModel->brandList($data);
        $count  = count($result);
        if($result) {
            $rel = [
                'status'    => true,
                'msg'       => '获取成功',
                'data'      => $result,
                'order'     => $data['order'],
                'orderType' => $data['orderType'],
                'page'      => $data['page'],
                'size'      => $data['pageSize'],
                'count'     => $count
            ];
        }else {
            $rel = [
                'status' => false,
                'msg'    => '获取失败',
                'data'   => $result
            ];
        }

        return $rel;


    }
}


