<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Carousel as CarouselModel;
use app\common\model\CarouselSeat;
use app\common\model\Article;
use app\common\model\Form;
use app\common\model\Goods;
use think\facade\Request;

class Carousel extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (Request::isAjax()) {
            $carouselModel = new CarouselModel();
            return  $carouselModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /**
     *  广告添加信息
     * User:tianyu
     * @return array|mixed
     */
    public function add()
    {
        $this->view->engine->layout(false);
        $carouselModel = new CarouselModel();
        if (Request::isPost()) {
            return $carouselModel->addData(input('param.'));
        }
        $carouselSeatModel = new CarouselSeat();
        $positionList = $carouselSeatModel->select();
        return $this->fetch('add', [
            'list' => $positionList,
            'type' => config('params.carousel')['type']
        ]);
    }


    /**
     *
     *  广告信息编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $carouselModel = new CarouselModel();
        if (Request::isPost()) {
            return $carouselModel->saveData(input('param.'));
        }
        $carouselModel = new CarouselModel();
        $info = $carouselModel->where('id', input('param.id/d'))->find();
        if (!$info) {
            return error_code(10002);
        }
        $carouselSeatModel = new CarouselSeat();
        $positionList = $carouselSeatModel->select();
        return $this->fetch('edit', [
            'positionList' => $positionList,
            'info' => $info,
            'type' => config('params.carousel')['type']
        ]);
    }


    /**
     *
     *  获取广告位列表
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function positionList()
    {
        $carouselSeatModel = new CarouselSeat();
        return $carouselSeatModel->field('id,code,name')->select();
    }


    /**
     *
     * 广告信息删除
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $carouselModel = new CarouselModel();
        $result = error_code(10023);
        if ($carouselModel->where('id', input('param.id/d'))->delete()) {
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
        $article_id_key = Request::param('article_id_key');
        $this->assign('article_id_key', $article_id_key);
        $article_name_key = Request::param('article_name_key');
        $this->assign('article_name_key', $article_name_key);
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
        return $articleModel->field('id,title')->where('id', input('param.id'))->find();
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
        return $goodsModel->field('id,name')->where('id', input('param.id'))->find();
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
        return $articleType->field('id,type_name')->where('id', input('param.id'))->find();
    }


    /**
     *  加载表单列表模板
     * User:mark
     * @return mixed
     */
    public function getForm()
    {
        $this->view->engine->layout(false);
        return $this->fetch('getForm');
    }

    public function formInfo()
    {
        $formModel = new Form();
        return $formModel->field('id,name')->where('id', input('param.id'))->find();
    }
}
