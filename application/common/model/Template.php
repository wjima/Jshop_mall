<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use org\Curl;

class Template extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    const TYPE_MINI = 1; //小程序类型

    /***
     * 根据类型获取所有模板列表
     * @param int $type
     * @param int $page
     * @param int $limit
     * @param string $keywords
     * @return mixed|string
     */
    public function getAllTemplate($type = self::TYPE_MINI, $page = 1, $limit = 10, $keywords = '')
    {
        /***
         * 获取远程模板
         */
        $product = config('jshop.product');
        $version = config('jshop.version');
        $url     = config('jshop.authorization_url') . '/b2c/Wechat/getTemplateList';
        $domain  = $_SERVER['SERVER_NAME'];
        $curl    = new Curl();
        $params  = [
            'domain'   => $domain,
            'product'  => $product,
            'version'  => $version,
            'page'     => $page,
            'limit'    => $limit,
            'keywords' => $keywords,
            'time'     => time(),
        ];
        $data = $curl::post($url, $params);

        $data = json_decode($data, true);

        return $data;
    }



}