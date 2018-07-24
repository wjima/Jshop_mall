<?php
namespace app\b2c\controller;


use app\b2c\model\Business;
use app\common\model\Cart;
use app\common\model\MessageCenter;
use app\common\model\Products;
use app\common\controller\Base;
use think\facade\Request;

class Index extends Base
{
    public function index()
    {

        if (Request::instance()->isMobile())
        {
            $businessModel = new Business();
            $count = $businessModel->dataCount() + 19;
            return $this->fetch('index',['pid'=>input('uuu'),'user_count'=>$count]);
        }
        return $this->fetch('index1');

    }

    /**
     *  用户报名信息提交
     * @return array
     */
    public function business()
    {
        $data = input('param.');
        $data['ip'] = get_client_ip();
        $data['ctime'] = time();

        $validate = new \app\common\validate\Business();
        if(!$validate->check($data)){
            $res['status'] = false;
            $res['msg']  = $validate->getError();
            return $res;
        }
        $business =new Business();
        return $business->addData($data);

    }

    //二维码生成
    function qrcodedemo()
    {
        $url = 'http://jshop.jihainet.com/';
        $qrcode = new \org\Demoqrcode();
        $qrcode->demo($url);
    }
    public function test()
    {
//        $mc = new MessageCenter();
//        $mc->sendSellerMessage(1,1,'order_payed',[]);

    }

}
