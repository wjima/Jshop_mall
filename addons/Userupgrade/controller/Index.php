<?php

namespace addons\Userupgrade\controller;

use app\common\model\User;
use app\common\model\UserGrade;
use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Session;

class Index extends AddonController
{

    protected function initialize()
    {
        parent::initialize();

        //想实现判断后台的登陆判断很简单，写如下代码即可
        Session::init([
            'prefix'         => 'manage',
            'type'           => '',
            'auto_start'     => true,
        ]);

        if (!session('?manage')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('manage/common/login');
        }
    }

    public function index()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!input('?param.id') || !input('?param.money')){
            return error_code(10000);
        }


        $pattern = '/^(([1-9][0-9]*)|0|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/';
        preg_match($pattern,input('param.money'),$match);
        if(!$match){
            $result['msg'] = "请输入正确金额";
            return $result;
        }
        $userGradeModel = new UserGrade();
        $re = $userGradeModel->save([
            'money' => input('param.money')
        ],[
            'id' => input('param.id')
        ]);
        if($re){
            $result['status'] = true;
        }else{
            $result['msg'] = "更新失败";
        }
        return $result;
    }
}