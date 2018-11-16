<?php
namespace app\api\controller;
use app\common\controller\Api;
use Request;

/**
 * 商品分类
 * Class Categories
 * @package app\seller\controller
 * @author keinx
 */
class Categories extends Api
{
    //不需要登录的方法
    protected $noLoginAction = ['getTopCat', 'getChildCat', 'getAllCat'];

    /**
     * 获取顶级分类
     * @return array
     */
    public function getTopCat()
    {
        $data = model('common/GoodsCat')->getChildClass();
        $return = array(
            'status' => false,
            'msg' => '',
            'data' => array(),
        );
        $return['cate_style'] = getSetting('cate_style'); //新增分类样式
        if($data)
        {
            $return['status'] = true;
            $return['data'] = $data;
        }
        else
        {
            $return['msg'] = '获取顶级分类失败';
        }

        return $return;
    }


    /**
     * 获取顶级分类下的子分类
     * @return array
     */
    public function getChildCat()
    {
        $parent_id = input('parent_id');
        $data = model('common/GoodsCat')->getChildClass($parent_id);
        $return = array(
            'status' => false,
            'msg' => '',
            'data' => array(),
        );

        if($data)
        {
            $return['status'] = true;
            $return['data'] = $data;
        }
        else
        {
            $return['msg'] = '获取顶级分类失败';
        }

        return $return;
    }


    /**
     * 获取全部分类树状形式
     * @return array
     */
    public function getAllCat()
    {
        $data = model('common/GoodsCat')->getAllCat();
        $return = array(
            'status' => false,
            'msg' => '',
            'data' => array(),
        );

        if($data)
        {
            $return['status'] = true;
            $return['data'] = $data;
        }
        else
        {
            $return['msg'] = '获取顶级分类失败';
        }

        return $return;
    }
}