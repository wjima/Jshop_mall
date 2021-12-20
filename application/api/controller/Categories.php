<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\GoodsCat;
use think\facade\Cache;
use think\facade\Request;


/**
 * 商品分类
 * Class Categories
 * @package app\api\controller
 * @author keinx
 */
class Categories extends Api
{
    //不需要登录的方法
    protected $noLoginAction = ['getTopCat', 'getChildCat', 'getAllCat'];


    /**
     * 获取顶级分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTopCat()
    {
        $return = array(
            'status' => false,
            'msg' => '',
            'data' => array(),
        );
        if (!Cache::has("jshop_categories_gettopcat")) {
            $model = new GoodsCat();
            $data = $model->getChildClass();
            if ($data) {
                $return['status'] = true;
                $return['data'] = $data;
            } else {
                // $return['msg'] = error_code(12001,true);
                return error_code((12001));
            }
            Cache::set("jshop_categories_gettopcat", $return, 3600 * 5);
        } else {
            $return = Cache::get("jshop_categories_gettopcat");
        }
        return $return;
    }


    /**
     * 获取顶级分类下的子分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChildCat()
    {
        $return = array(
            'status' => false,
            'msg' => '',
            'data' => array(),
        );
        $parent_id = input('parent_id');
        if (!Cache::has("jshop_categories_getchildcat" . "_" . $parent_id)) {
            $model = new GoodsCat();
            $data = $model->getChildClass($parent_id);
            if ($data) {
                $return['status'] = true;
                $return['data'] = $data;
            } else {
                return error_code((12001));
            }
            Cache::set("jshop_categories_getchildcat" . "_" . $parent_id, $return, 3600 * 5);
        } else {
            $return = Cache::get("jshop_categories_getchildcat" . "_" . $parent_id);
        }
        return $return;
    }


    /**
     * 获取全部分类树状形式
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllCat()
    {
        $return = array(
            'status' => false,
            'msg' => '',
            'data' => array(),
        );
        $backstage = input('backstage', '2');
        if (!Cache::has("jshop_categories_getallcat" . '_' . $backstage)) {
            $show = ($backstage == '2') ? false : true;
            $model = new GoodsCat();
            $data = $model->getAllCat(false, $show);
            if ($data) {
                $return['status'] = true;
                $return['data'] = $data;
            } else {
                return error_code((12001));
            }
            Cache::set("jshop_categories_getallcat" . '_' . $backstage, $return, 3600 * 5);
        } else {
            $return = Cache::get("jshop_categories_getallcat" . '_' . $backstage);
        }
        return $return;
    }


    /**
     * 获取分类名称
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getName()
    {
        $model = new GoodsCat();
        $id = Request::param('id');
        if (!Cache::has("jshop_categories_getname" . '_' . $id)) {
            Cache::set("jshop_categories_getname" . '_' . $id, $model->getNameById($id), 3600 * 5);
        }
        return Cache::get("jshop_categories_getname" . '_' . $id);
    }
}