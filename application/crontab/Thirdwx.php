<?php

namespace app\crontab;

use taskphp\Utils;
use app\common\model\WeixinAuthor;
use org\ThirdWx as ThirdwxLib;


class Thirdwx
{
    /**
     * 刷新token
     */
    public static function run()
    {
        return true;
        /*
        Utils::log('刷新token开始时间：' . date('Y-m-d H:i:s') . PHP_EOL);

        $weixinAuthorModel = new WeixinAuthor();
        $filter            = [];
        $exp               = time() + 10 * 60;//提前10分钟刷新token
        $filter[]          = ['expires_in', 'lt', $exp];
        $filter[]          = ['bind_type', 'eq', '1'];
        $authors           = $weixinAuthorModel->field('id,seller_id,appid,authorizer_access_token,authorizer_refresh_token')->where($filter)->select();
        Utils::log("刷新token数据：" . json_encode($authors));
        $thirdWx = new ThirdwxLib();
        foreach ($authors as $key => $val) {
            if ($val['authorizer_refresh_token']) {
                $refresh                 = $val['authorizer_refresh_token'];
                $authorizer_access_token = $thirdWx->refreshAuthorToken($val['appid'], $refresh);
                if ($authorizer_access_token) {
                    $uData['authorizer_access_token']  = $authorizer_access_token['authorizer_access_token'];
                    $uData['authorizer_refresh_token'] = $authorizer_access_token['authorizer_refresh_token'];
                    $uData['expires_in']               = time() + $authorizer_access_token['expires_in'];//100分钟后过期
                    $weixinAuthorModel->save($uData, ['id' => $val['id']]);
                }
            }
        }
        Utils::log('刷新token结束时间：' . date('Y-m-d H:i:s') . PHP_EOL);*/
    }
}
