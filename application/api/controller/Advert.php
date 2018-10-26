<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\AdvertPosition;
use app\common\model\Advertisement;

class Advert extends Api
{
    /**
     *  获取广告位列表
     * User:tianyu
     * @return mixed
     */
    public function positionList()
    {
        $position = new AdvertPosition();
        $page = input('page/d',1);
        $limit = input('limit/d',5);
        return $position->getOptionsList($page,$limit);
    }


    /**
     *
     *  获取广告列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdvertList()
    {
        $code = input('code/s', '');
        if (!$code) return error_code(10051);
        $page = input('page/d',1);
        $limit = input('limit/d',5);
        $advert = new Advertisement();
        return $advert->getAdvertList($code,$page,$limit);
    }

}