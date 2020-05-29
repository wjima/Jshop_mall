<?php
namespace app\api\controller;
use app\common\controller\Api;
use org\Wx;

/**
 * 微信JS-SDK
 * Class WeiXinShare
 * @package app\api\controller
 */
class WeiXinShare extends Api
{


    public function share ()
    {
        if(!input('?param.url')){
            return error_code(10067);
        }
        $wx = new Wx();
        return $wx->jssdk(input('param.url'));
    }

}