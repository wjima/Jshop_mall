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
use think\facade\Cache;


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

        $this->assign('linkType', json_encode($linkType, 320));
        $this->assign('page_code', $page_code);
        //取出页面配置信息
        $pageModel  = new \app\common\model\Pages();
        $result     = $pageModel->getInfo($page_code);
        //重新组装数据
        $pageConfig = [];
        if ($result['data'] && $result['data']['items']) {
            foreach ($result['data']['items'] as $key => $value) {
                $pageConfig[$key]['type']  = $value['widget_code'];
                $pageConfig[$key]['value'] = json_decode($value['params'],true);
            }
        }
        $pageConfig = json_encode($pageConfig, 320);
        $this->assign('page_config', $pageConfig);
        //取出所有品牌
        $brandModel = new Brand();
        $brandList  = $brandModel->getAllBrand();
        $this->assign('brandList', json_encode($brandList, 320));
        //取出所有分类
        $goodsCatModel = new GoodsCat();
        $catList       = $goodsCatModel->getAllCat();
        $this->assign('catList', json_encode($catList, 320));
        //文章分类
        $articleTypeModel = new articleTypeModel();
        $this->assign('articleTypeList', json_encode($articleTypeModel->getTree(), 320));
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
        $data            = input('post.data/s', '');
        $code            = input('post.pageCode/s', 'mobile_home');
        $pagesItemsModel = new PagesItems();
        $res             = $pagesItemsModel->saveItems($data, $code);
        if (!$res['status']) {
            return $res;
        }
        Cache::clear();
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

    /**
     * 设置首页
     * @return array|bool
     * @throws \Exception
     */
    public function setHome()
    {
        $result     = [
            'status' => true,
            'msg'    => '设置成功',
            'data'   => [],
        ];
        $id         = input('id', '');
        $pagesModel = new pagesModel();
        $res        = $pagesModel->where([['id', '=', $id]])->update(['is_main' => $pagesModel::MAIN_YES]);
        $pagesModel->where([['id', 'neq', $id]])->update(['is_main' => $pagesModel::MAIN_NO]);
        if (!$res) {
            return error_code(10081);
        }
        Cache::clear();
        return $result;
    }
}
