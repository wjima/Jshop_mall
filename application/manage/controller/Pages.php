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
use app\common\model\PagesMenu;
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
        $data            = input('post.data/s', '','safe_filter');
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

    /***
     * 底部菜单
     * @return mixed
     */
    public function setMenu(){
        $pageMenu = new PagesMenu();
        $menu       = $pageMenu->getMenu();
        $this->assign('weixin_menu', $menu);
        return $this->fetch();
    }


    /**
     * 编辑菜单项
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editMenu()
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => '', //参数错误
        ];
        $this->view->engine->layout(false);
        $id  = input('id/d');
        $pid = input('pid/d');
        if (!$id) {
            return error_code(10051);
        }
        $PageMenu = new PagesMenu();
        $menu     = $PageMenu->where(['menu_id' => $id, 'pid' => $pid])->find();
        if ($menu) {
            $menu['params'] = json_decode($menu['params'], true);
        }

        $this->assign('id', $id);
        $this->assign('pid', $pid);//父级菜单ID
        $this->assign('menu', $menu);
        $result['status'] = true;
        $result['msg']    = '成功';
        $result['data']   = $this->fetch('edit_menu');
        return $result;
    }


    /**
     * 保存菜单
     * @return array
     */
    public function doEditMenu()
    {
        $result   = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $data     = input('param.');
        $validate = new \app\common\validate\PagesMenu();
        if (!$validate->check($data)) {
            $result['msg'] = $validate->getError();
            return $result;
        }
        $PageMenu = new PagesMenu();
        $res      = $PageMenu->toSave($data);
        return $res;
    }


    /**
     * 删除菜单
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function deleteMenu()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',//参数错误
        ];
        $id     = input('id/d', 0);
        $pid    = input('pid/d', 0);
        if (!$id) {
            return error_code(10051);
        }
        $pageMenu = new PagesMenu();
        $info     = $pageMenu->where(['pid' => $pid, 'menu_id' => $id])->find();
        //无此菜单时，前台直接删除
        if (!$info) {
            $result['status'] = true;
            $result['msg']    = '删除成功';
            return $result;
        }
        // 当删除父节点菜单时，检查是否有子节点
        if ($pid == 0) {
            $nums = $pageMenu->where(['pid' => $id])->count();
            if ($nums > 0) {
                $result['msg'] = error_code(11100, true);
                return $result;
            }
        }
        $res = $pageMenu->where(['pid' => $pid, 'menu_id' => $id])->delete();
        if (!$res) {
            $result['msg'] = error_code(10023, true);
            return $result;
        }
        Cache::clear(); //TODO 如果开启其他缓存，记得这里要配置缓存配置信息
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }

}
