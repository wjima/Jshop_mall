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
use app\common\model\Article as articleModel;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class Article extends Manage
{


    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $article = new articleModel();
        if (Request::isAjax()) {
            return $article->tableData(input('param.'));
        }
        $articleTypeModel = new articleTypeModel();
        $list             = $articleTypeModel->select();
        return $this->fetch('', ['list' => $list]);
    }


    /**
     *  文章添加
     * User:tianyu
     *
     * @return array|mixed
     */
    public function add()
    {
        if (Request::isPost()) {
            $article = new articleModel();
            return $article->addData(input('param.','','safe_filter'));
        }
        $articleTypeModel = new articleTypeModel();
        return $this->fetch('add', ['list' => $articleTypeModel->getTree()]);
    }


    /**
     *
     *  文章编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $articleModel = new articleModel();
        if (Request::isPost()) {
            $data = [
                'id'      => input('post.id'),
                'type_id' => input('post.type_id'),
                'title'   => input('post.title'),
                'brief'   => input('post.brief'),
                'cover'   => input('post.cover'),
                'content' => input('post.content', '', 'safe_filter'),
                'is_pub'  => input('post.is_pub'),
                'sort'    => input('post.sort'),
            ];
            return $articleModel->saveData($data);
        }
        $info = $articleModel->with('articleType')->where('id', input('param.id/d'))->find();
        if (!$info) {
            return error_code(10002);
        }
        $articleTypeModel = new articleTypeModel();
        return $this->fetch('edit', ['info' => $info, 'list' => $articleTypeModel->getTree()]);
    }


    /**
     *
     * User:tianyu
     *
     * @return array
     */
    public function del()
    {
        $article = new articleModel();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$article->destroy(input('param.id/d'))) {
            return error_code(10023);
        }
        return $result;
    }

}
