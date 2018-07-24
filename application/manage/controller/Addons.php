<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;
use app\common\model\Addons as addonsModel;
use app\common\model\SellerAddons;

/**
 * 商品类型
 * Class GoodsType
 * @package app\seller\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:07
 */
class Addons extends Manage
{
    /**
     * 插件列表
     * @return mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $addonsModel           = new addonsModel();
            $filter                = input('request.');
            $addons = $addonsModel->tableData($filter);
            $addons['data'] = $addonsModel->listData($addons['data'],$this->sellerId);
            return $addons;
        }
        return $this->fetch('index');
    }

    /**
     * 订购页面
     */
    public function buy()
    {
        $result = [
            'status' => false,
            'msg'    => '订购失败',
            'data'   => '',
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return $result;
        }
        $sellerAddonsModel = new SellerAddons();
        if ($sellerAddonsModel->addAddons($id, $this->sellerId)) {
            $result['status'] = true;
            $result['msg']    = '订购成功';
        }
        return $result;
    }

    public function setting()
    {
        $result = [
            'status' => false,
            'msg'    => '获取配置信息失败',
            'data'   => '',
            'dialog' => [],
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return $result;
        }
        $this->view->engine->layout(false);

        $addonsModel = new addonsModel();

        $addon      = $addonsModel->getAddonInfo($id);
        $sellerAddonsModel = new SellerAddons();
        $sellerAddons = $sellerAddonsModel->getSetting($id,$this->sellerId);
        $class_name = '\\addons\\' . $addon['name'] . '\\' . ucfirst($addon['name']);
        if (!class_exists($class_name)) {
            $result['status'] = false;
            $result['msg']    = '插件不存在';
            return $result;
        }
        $addonObject      = new $class_name();
        $result['status'] = true;
        $result['data']   = $addonObject->config($sellerAddons);
        $result['dialog'] = $addonObject->getDialog();
        return $result;
    }

    /**
     * 保存配置信息
     * @return array
     */
    public function doSetting()
    {
        $result = [
            'status' => false,
            'msg'    => '配置信息保存失败',
            'data'   => '',
        ];
        $data = input('post.');
        $sellerAddonsModel = new SellerAddons();
        if($sellerAddonsModel->doSetting($data,$this->sellerId)){
            $result['status'] = true;
            $result['msg'] = '配置信息保存成功';
        }
        return $result;
    }

    /**
     * 停用插件
     * @return array
     */
    public function changeStatus(){
        $result = [
            'status' => false,
            'msg'    => '操作失败',
            'data'   => '',
        ];
        $id = input('post.id/d','0');
        $sellerAddonsModel = new SellerAddons();
        if($sellerAddonsModel->changeStatus($id,$this->sellerId)){
            $result['status'] = true;
            $result['msg'] = '操作成功';
        }
        return $result;
    }
}
