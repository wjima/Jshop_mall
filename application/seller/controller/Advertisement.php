<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/26 0026
 * Time: 11:42
 */
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\AdvertPosition as advertPositionModel;
use app\common\model\Advertisement as advertisementModel;
use app\common\model\Article;
use app\common\model\Goods;
use think\facade\Request;

class Advertisement extends Seller
{

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $advertisement = new advertisementModel();
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return  $advertisement->tableData($data);
        }
        $position = new advertPositionModel();
        $positionList = $position->getList($this->sellerId);
        return $this->fetch('',['positionList'=>$positionList]);
    }


    /**
     *  添加广告信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $advertisement = new advertisementModel();
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $advertisement->addData($data);
        }
        $position = new advertPositionModel();
        $positionList = $position->getList($this->sellerId);
        return $this->fetch('add',['positionList'=>$positionList,'type'=>config('params.advertType')['type']]);
    }


    /**
     *  编辑广告信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $advertisement = new advertisementModel();
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $advertisement->saveData($data);
        }
        $position = new advertPositionModel();
        $positionList = $position->getList($this->sellerId);
        $info = $advertisement->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();
        if (!$info) {
            return error_code(10002);
        }
        return $this->fetch('edit',['positionList'=>$positionList,'info'=>$info,'type'=>config('params.advertType')['type']]);
    }


    /**
     *
     *  广告删除
     * @return array
     */
    public function del()
    {
        $advertisement = new advertisementModel();
        $result = ['status'=>false,'msg'=>'删除失败','data'=>''];
        if ($advertisement->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->delete())
        {
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }
        return $result;
    }



    /**
     *  加载商品列表模板
     * User:tianyu
     * @return mixed
     */
    public function getGoods()
    {
        $this->view->engine->layout(false);
        return $this->fetch('getGoods');
    }


    /**
     *  加载文章列表模板
     * User:tianyu
     * @return mixed
     */
    public function getArticle()
    {
        $this->view->engine->layout(false);
        return $this->fetch('getArticle');
    }

    public function getArticleType()
    {
        $this->view->engine->layout(false);
        return $this->fetch('getArticleType');
    }


    /**
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleInfo()
    {
        $articleModel = new Article();
        return $articleModel->field('id,title')->where('id',input('param.id'))->find();
    }


    /**
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsInfo()
    {
        $goodsModel = new Goods();
        return $goodsModel->field('id,name')->where('id',input('param.id'))->find();
    }


    /**
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleTypeInfo()
    {
        $articleType = new \app\common\model\ArticleType();
        return $articleType->field('id,type_name')->where('id',input('param.id'))->find();
    }
}