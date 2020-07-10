<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\model\Brand;
use app\common\model\GoodsCat;
use app\common\model\PagesItems;
use Request;
use app\common\controller\Manage;
use app\common\model\ArticleType as articleTypeModel;
use app\common\model\Pages as pagesModel;


class Pages extends Manage
{

    public function index()
    {
        $page = new pagesModel();
        if (Request::isAjax()) {
            $filter = input('request.');
            return $page->tableData($filter);
        }
        return $this->fetch('index');
    }

    /**
     * 页面编辑
     */
    public function custom()
    {
        $page_code = input('page_code/s', 'mobile_home');
        $linkType  = config('params.carousel')['type'];

        $this->assign('linkType', json_encode($linkType, JSON_UNESCAPED_UNICODE));
        $this->assign('page_code', $page_code);
        //取出页面配置信息
        $pageModel  = new \app\common\model\Pages();
        $result     = $pageModel->getDetails($page_code);
        $pageConfig = [];
        if ($result['data'] && $result['data']['items']) {
            foreach ($result['data']['items'] as $key => $value) {
                $pageConfig[$key]['type']  = $value['widget_code'];
                $pageConfig[$key]['value'] = $value['params'];
            }
        }
        $pageConfig = json_encode($pageConfig, 320);
        $pageConfig = str_replace(['"true','true"'], 'true', $pageConfig);
        $pageConfig = str_replace(['"false','false"'], 'false', $pageConfig);

        $this->assign('page_config', $pageConfig);

        //取出所有品牌
        $brandModel = new Brand();
        $brandList  = $brandModel->getAllBrand();
        $this->assign('brandList', json_encode($brandList, JSON_UNESCAPED_UNICODE));
        //取出所有分类
        $goodsCatModel = new GoodsCat();
        $catList       = $goodsCatModel->getAllCat();
        $this->assign('catList', json_encode($catList, JSON_UNESCAPED_UNICODE));
        //文章分类
        $articleTypeModel = new articleTypeModel();
        $this->assign('articleTypeList', json_encode($articleTypeModel->getTree(), JSON_UNESCAPED_UNICODE));
        return $this->fetch('custom');
    }

    /**
     * 页面配置
     */
    public function saveCustom()
    {
        $result          = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => [],
        ];
        $data            = input('post.data/a', []);
        $code            = input('post.pageCode/s', 'mobile_home');
        $pagesItemsModel = new PagesItems();
        $res             = $pagesItemsModel->saveItems($data, $code);
        if (!$res['status']) {
            return $res;
        }
        return $result;
    }


    public function add()
    {

        $this->view->engine->layout(false);

        if (\think\facade\Request::isPost()) {
            $pagesModel = new \app\common\model\Pages();

            return $pagesModel->addData(input('param.'));
        }

        $this->assign('list', config('pages.list'));
        return $this->fetch();
    }


    public function del()
    {

        $id = input('param.id/d', '');
        if (!$id) return error_code(10051);


        $info = \app\common\model\Pages::get($id, 'items');
        $info->together('items')->delete();

        return  [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => []
        ];
    }
}
