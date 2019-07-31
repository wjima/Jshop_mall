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
use app\common\model\CarouselSeat;
use app\common\model\Carousel as CarouselModel;
use think\facade\Request;

/**
 * 广告
 * Class Carousel
 * @package app\api\controller
 */
class Carousel extends Api
{
    /**
     * 获取广告位列表
     * User:tianyu
     * @return array
     */
    public function carouselSeatList()
    {
        $carouselSeatModel = new CarouselSeat();
        return $carouselSeatModel->getOptionsList(input('page/d',1), input('limit/d',5));
    }


    /**
     * 获取广告列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $code = input('code/s', '');
        if (!$code) return error_code(10051);
        $carouselModel = new CarouselModel();
        return $carouselModel->getCarouselList($code, input('page/d',1), input('limit/d',5));
    }


    /**
     * 获取多个广告位广告
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCarouselLists()
    {
        $codes = Request::param('codes', false);
        if(!$codes)
        {
            return error_code(10051);
        }
        $carouselModel = new CarouselModel();
        return $carouselModel->getCarouselLists($codes);
    }
}