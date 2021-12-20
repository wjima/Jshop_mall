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
use think\facade\Cache;
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
        $page = input('page/d',1);
        $limit = input('limit/d',5);
        if(!Cache::has("jshop_carouselseatlist".'_'.$page.'_'.$limit)){
            Cache::set("jshop_carouselseatlist".'_'.$page.'_'.$limit,$carouselSeatModel->getOptionsList($page,$limit),3600*5);
        }
        return Cache::get("jshop_carouselseatlist".'_'.$page.'_'.$limit);
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
        $page = input('page/d',1);
        $limit = input('limit/d',5);
        if (!$code) return error_code(10051);
        if(!Cache::has("jshop_carousel_getlist".'_'.$code.'_'.$page.'_'.$limit)){
            $carouselModel = new CarouselModel();
            Cache::set("jshop_carousel_getlist".'_'.$code.'_'.$page.'_'.$limit,$carouselModel->getCarouselList($code, $page, $limit),3600*5);
        }
        return Cache::get("jshop_carousel_getlist".'_'.$code.'_'.$page.'_'.$limit);

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
        if(!Cache::has("jshop_getcarousellists".'_'.$codes)){
            $carouselModel = new CarouselModel();
            Cache::set("jshop_getcarousellists".'_'.$codes,$carouselModel->getCarouselLists($codes),3600*5);
        }
        return Cache::get("jshop_getcarousellists".'_'.$codes);

    }
}