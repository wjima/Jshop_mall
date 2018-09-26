<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/29 0029
 * Time: 19:14
 */

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