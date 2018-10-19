<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;
use app\common\model\Template;
use app\common\model\Setting;




class Wechat extends Manage
{

    private $author = [];//小程序授权信息
    private $authorType = 'b2c';//授权类型


    /**
     * 自助绑定小程序
     */
    public function edit()
    {

        $settingModel = new Setting();
        $data = $settingModel->getAll();
        $this->assign('data', $data);
        $wechat = config('thirdwx.');
        $this->assign('wechat', $wechat);
        return $this->fetch('edit');
    }

    public function doEdit()
    {
        $result            = [
            'status' => false,
            'data'   => '',
            'msg'    => '保存失败',
        ];
        $settingModel = new Setting();

        if(Request::isAjax()){
            foreach(input('param.') as $k => $v) {
                $result = $settingModel->setValue($k, $v);
                //如果出错，就返回，如果是没有此参数，就默认跳过
                if (!$result['status'] && $result['data'] != 10008) {
                    return $result;
                }
            }
            $result['status'] = true;
            $result['msg']    = '保存成功';
            return $result;
        }
        return $result;
    }


    /**
     *展示授权信息
     */
    public function info()
    {
        $settingModel = new Setting();
        $data = $settingModel->getAll();
        $this->assign('data', $data);
        $wechat = config('thirdwx.');
        $this->assign('wechat', $wechat);

        $host = \request()->host();
        $this->assign('weixin_host',$host);
        return $this->fetch('edit');
    }

    /**
     * 获取模板信息
     */
    public function template()
    {
        $templateModel = new Template();
        $data          = $templateModel->getAllTemplate($templateModel::TYPE_MINI);
        $this->assign('data', $data);

        return $this->fetch('template');
    }

}
