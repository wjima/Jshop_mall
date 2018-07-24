<?php
namespace app\seller\controller;

use app\common\model\Brand;
use app\common\model\Goods;
use app\common\model\Seller;
use app\common\model\SellerManage;
use Request;
use think\Container;
use app\common\controller\Base;

class Common extends Base
{
    public function initialize(){
        parent::initialize();
        //没有登陆，请先登录
        if (!session('?user')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('b2c/common/login');
        }
        //此控制器不需要模板布局，所以屏蔽掉
        $this->view->engine->layout(false);

    }
    /**
     * 卖家注册页面  todo  未完成
     * @author sin
     */
    public function reg()
    {
        $sellerModel = new Seller();
        $sellerInfo = $sellerModel->where(array('user_id'=>session('user.id')))->find();

        if($sellerInfo && $sellerInfo['status'] == $sellerModel::STATUS_NORMAL){
            //如果审核通过了，就登陆商户，然后跳转到后台首页
            $sellerModel = new Seller();
            $sellerModel->login(session('user.id'), [], 1);
            $this->success('已经审核通过，没有必要重新审核',url('seller/index/index'));
        }

        if(Request::isPost()){
            return $sellerModel->toReg(input('param.'),session('user'));
        }else{
            if($sellerInfo){
                $this->assign('seller',$sellerInfo);
                if($sellerInfo['status'] == $sellerModel::STATUS_DEFAULT){
                    return $this->fetch('reg');
                }elseif($sellerInfo['status'] == $sellerModel::STATUS_WAIT_AUDIT){
                    return $this->fetch('regInfo');
                }elseif($sellerInfo['status'] == $sellerModel::STATUS_AUDIT_FAIL){
                    return $this->fetch('regInfo');
                }
            }else{
                return $this->fetch('reg');
            }
        }
    }

    //选择店铺登陆，如果有多个店铺的话，来此选择并进行登录
    public function choose()
    {
        if(input('?param.id')){
            //判断用户是否是卖家，并且仅有一个店铺，如果有多个店铺的话，让用户选择登陆哪个店铺
            $sellerModel = new \app\common\model\Seller();
            $re = $sellerModel->login(session('user')['id'],input('param.id'));
            if(!$re['status']){
                //登陆失败，做特殊处理

                //没有店铺，需要先注册或者绑定店铺
                if($re['data'] == 11086){
                    $this->redirect('seller/common/reg');
                }

                //如果多个店铺，就去选择登陆哪一个店铺
                if($re['data'] == 11087){
                    $this->redirect('seller/common/choose');
                }

                //店铺状态不对，没有审核通过或者是到期了
                if($re['data'] == 11501){
                    $this->error($re['msg'],url('seller/common/reg'));
                }

                $this->error($re['msg']);
            }
            $this->redirect('seller/index/index');
        }else{
            //看看用户都绑定了哪些店铺
            $sellerManageModel = new SellerManage();
            $list = $sellerManageModel->getSellerManage(session('user')['id']);
            $this->assign('list',$list);
            return $this->fetch();
        }

    }


    /**
     * 申请失败后，从新申请
     */
    public function reReg()
    {
        $sellerModel = new Seller();
        $sellerModel->save(array('status'=>$sellerModel::STATUS_DEFAULT),array('status'=>$sellerModel::STATUS_AUDIT_FAIL,'user_id'=>session('user.id')));
        $this->redirect('reg');
    }
}
