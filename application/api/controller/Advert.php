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
        $seller_id = $this->sellerId;
        $page = input('page/d',1);
        $limit = input('limit/d',5);
        return $position->getOptionsList($seller_id,$page,$limit);
    }


    /**
     *  获取广告列表
     * User:tianyu
     * @return array
     */
    public function getAdvertList()
    {
        $seller_id = $this->sellerId;
        $code = input('code/s');
        if(!$code)
        {
            $result = [
                'status' => false,
                'msg'   =>  '缺少必须参数code'
            ];
            return $result;
        }
        $page = input('page/d',1);
        $limit = input('limit/d',5);
        $advert = new Advertisement();
        return $advert->getAdvertList($seller_id,$code,$page,$limit);
    }

}