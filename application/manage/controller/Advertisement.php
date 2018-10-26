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
use app\common\model\AdvertPosition as advertPositionModel;
use app\common\model\Advertisement as advertisementModel;
use app\common\model\Article;
use app\common\model\Goods;
use think\facade\Request;

class Advertisement extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $advertisement = new advertisementModel();
            return  $advertisement->tableData(input('param.'));
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
        $advertisement = new advertisementModel();
        if(Request::isPost())
        {
            return $advertisement->addData(input('param.'));
        }
        $positionModel = new advertPositionModel();
        $positionList = $positionModel->select();
        return $this->fetch('add',[
            'list' => $positionList,
            'type'=>config('params.advertType')['type']
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
        $advertisement = new advertisementModel();
        if(Request::isPost())
        {
            return $advertisement->saveData(input('param.'));
        }
        $advertisementModel = new advertisementModel();
        $info = $advertisementModel->where('id',input('param.id/d'))->find();
        if (!$info)
        {
            return error_code(10002);
        }
        $positionModel = new advertPositionModel();
        $position = $positionModel->where('id',$info['position_id'])->find();
        return $this->fetch('edit',[
                'position'=>$position,
                'info'=>$info,
                'type'=>config('params.advertType')['type']
            ]);
    }


    /**
     *
     *  获取对应商户的广告位列表
     *
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function positionList()
    {
        $positionModel = new advertisementModel();
        return $positionModel->field('id,code,name')->select();
    }


    /**
     *
     * 广告信息删除
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $advertisement = new advertisementModel();
        $result = ['status'=>false,'msg'=>'删除失败','data'=>''];
        if ($advertisement->where('id',input('param.id/d'))->delete())
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