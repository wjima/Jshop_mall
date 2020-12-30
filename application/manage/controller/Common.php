<?php
namespace app\Manage\controller;

use app\common\model\Brand;
use app\common\model\Goods;
use Request;
use think\Container;
use app\common\controller\Base;
use app\common\model\Manage;
use app\common\model\UserLog;

class Common extends Base
{
    public function initialize(){
        parent::initialize();
        //此控制器不需要模板布局，所以屏蔽掉
        $this->view->engine->layout(false);

    }
    /**
     * 用户登陆页面
     * @author sin
     */
    public function login()
    {
        $shop_name = getSetting('shop_name');
        $this->assign('shop_name',$shop_name);
        if (session('?manage')) {
            $this->success('已经登录成功，跳转中...',redirect_url(url('Index/index')));
        }
        if(Request::isPost()){
            $manageModel = new Manage();
            $result = $manageModel->toLogin(input('param.'));
            if($result['status']){
                if(Request::isAjax()){
                    $result['data'] = redirect_url(url('Index/index'));
                    return $result;
                }else{
                    $this->redirect(redirect_url(url('Index/index')));
                }
            }else{
                return $result;
            }
        }else{
            return $this->fetch('login');
        }
    }
    /**
     * 用户退出
     * @author sin
     */
    public function logout()
    {
        //增加退出日志
        if(session('manage.id')){
            $userLogModel = new UserLog();
            $userLogModel->setLog(session('manage.id'),$userLogModel::USER_LOGOUT,[],$userLogModel::MANAGE_TYPE);
        }
        session('manage', null);
        $this->success('退出成功',url('Index/index'));
    }
}
