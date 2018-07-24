<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\Seller;
use Request;
use app\common\model\GoodsCat;

/**
 * 商品分类
 * Class Categories
 * @package app\seller\controller
 * @author keinx
 */
class Categories extends Manage
{
    /**
     * 商品分类列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(!Request::isAjax())
        {
            //所属商户
            $seller = new Seller();
            $seller_list = $seller->getAllSellerList();
            $this->assign('seller_list', $seller_list);

            //打开主页
            return $this->fetch('index');
        }
        else
        {
            $seller_id = input('seller_id', false);
            if($seller_id)
            {
                $data = model('common/GoodsCat')->getList($seller_id);
                if(count($data) > 0)
                {
                    $return_data = array(
                        'status' => 1,
                        'msg' => "数据获取成功",
                        'count' => count($data),
                        'data' => $data
                    );
                }
                else
                {
                    $return_data = array(
                        'status' => 0,
                        'msg' => "没有分类快去添加一个吧",
                        'count' => count($data),
                        'data' => $data
                    );
                }
            }
            else
            {
                $return_data = array(
                    'status' => 0,
                    'msg' => "请先选择所属商户",
                    'count' => 0,
                    'data' => []
                );
            }

            return $return_data;
        }
    }


    /**
     * 添加商品分类
     * @param int $parent_id
     * @param int $seller_id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add($parent_id = GoodsCat::TOP_CLASS_PARENT_ID, $seller_id = 0)
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //获取添加页面
            //父级ID
            $this->assign('parent_id', $parent_id);
            $this->assign('seller_id', $seller_id);

            //顶级分类
            $parent = model('common/GoodsCat')->getAllCat($seller_id);
            $this->assign('parent', $parent);

            //类型
            $type = model('common/GoodsType')->getList($seller_id);
            $this->assign('type', $type['data']);

            //所属商户
            $seller = new Seller();
            $seller_list = $seller->getAllSellerList();
            $this->assign('seller_list', $seller_list);

            return $this->fetch('add');
        }
        else
        {
            //存储添加内容
            $data = array(
                'parent_id' => input('parent_id'),
                'type_id' => input('type_id'),
                'name' => input('name'),
                'image_id' => input('image_id'),
                'sort' => input('sort'),
                'seller_id' => input('seller_id', 0)
            );
            $result = model('common/GoodsCat')->add($data);
            if($result !== false)
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '添加成功',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '添加失败',
                    'data' => $result
                );
            }
            return $return_data;
        }
    }


    /**
     * 编辑商品分类
     * @param $id
     * @param int $seller_id
     * @return mixed
     */
    public function edit($id, $seller_id = 0)
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //获取编辑页面
            $parent = model('common/GoodsCat')->getAllCat($seller_id, $id);
            $this->assign('parent', $parent); //父级分类
            $type = model('common/GoodsType')->getList($seller_id);
            $this->assign('type', $type['data']);
            $data = model('common/GoodsCat')->getCatInfo($id);
            $this->assign('data', $data); //分类信息
            return $this->fetch('edit');
        }
        else
        {
            //存储编辑内容
            $data = array(
                'id' => input('id'),
                'parent_id' => input('parent_id'),
                'type_id' => input('type_id'),
                'name' => input('name'),
                'image_id' => input('image_id'),
                'sort' => input('sort')
            );
            $result = model('common/GoodsCat')->edit($data);
            return $result;
        }
    }


    /**
     * 删除商品分类
     * @param $id
     * @return array
     */
    public function del($id)
    {
        if(!Request::isPost())
        {
            //查询是否可以删除
            $result = model('common/GoodsCat')->getIsDel($id, $this->sellerId);
            if($result['is'])
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '可以删除',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '该分类下存在子分类无法删除，请先删除子分类',
                    'data' => $result
                );
            }
            return $return_data;
        }
        else
        {
            //删除
            $result = model('common/GoodsCat')->del($id, $this->sellerId);
            if($result)
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '删除成功',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '删除失败',
                    'data' => $result
                );
            }
            return $return_data;
        }
    }
}