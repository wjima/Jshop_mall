<?php
/**
 * 此控制器不用api校验，不需要token登陆，不需要seller_id就是单纯的标准的接口数据,用于取一些通用的信息
 */
namespace app\api\controller;

use app\common\model\Area;

use app\common\controller\Base;

use app\common\model\Seller;

class Common extends Base
{

    protected function initialize()
    {
        parent::initialize();
        //解决跨域问题
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式
    }

    /**
     * 取省市区的详细信息
     * @return array
     */
    public function area()
    {
        $areaModel = new Area();
        return $areaModel->getArea(input('param.id'));
    }

    /**
     * 取地区的下级列表
     * @return array
     */
    public function areaChildren()
    {
        $areaModel = new Area();
        return $areaModel->getAllChild(input('param.id'));
    }

    /**
     * 获取店铺列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreInfo()
    {
        $token = input('token/a', '');
        $sellerModel = new Seller();
        $res = $sellerModel->getStoreInfo($token);

        $operating_mode = config('app.operating_mode');
        if($operating_mode == 'review')
        {
            //审核模式
            $res['mode'] = '7881f454af469aa8';
        }
        else
        {
            //正式模式
            $res['mode'] = '31e70f53929daa89';
        }
        return $res;
    }

    /**
     * 获取验证码，如果登陆失败次数太多，就需要验证码了
     * @return string
     */
    public function verify()
    {
        if(input('?param.id')){
            $id = input('param.id');
        }else {
            $id = "";
        }
        return captcha_src($id);
    }
}