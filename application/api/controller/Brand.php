<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\model\Brand as BrandModel;
use app\common\controller\Api;

/**
 * 品牌
 * Class Brand
 * @package app\api\controller
 */
class Brand extends Api
{
    /**
     * 获取品牌列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function brandList()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        $field = 'id,name,logo,sort';
        $order = input('param.order', 'sort asc');
        $page = input('param.page', 1);
        $limit = input('param.limit', 10);
        $brandModel = new BrandModel;
        $list = $brandModel->field($field)->order($order)->page($page, $limit)->select();
        $count  = $brandModel->field($field)->count();
        if(!$list->isEmpty())
        {
            foreach((array)$list as &$v)
            {
                $v['logo'] = _sImage($v['logo']);
            }
        }
        $result['data'] = [
            'list' => $list,
            'count' => $count
        ];
        return $result;
    }
}