<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\ArticleType;
use app\common\model\Article;
use think\facade\Request;

class Articles extends Api
{

    /**
     * 获取全部文章分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleType()
    {
        $articleType = new ArticleType();
        return $articleType->articleTypeList();
    }


    /**
     * 获取文章列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleList()
    {
        $article = new Article();
        $type_id = Request::param('type_id', false);
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);

        return $article->articleList($type_id, $page, $limit);
    }


    /**
     * 获取单个文章的详细信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleDetail()
    {
        $article_id = Request::param('article_id', 0);
        if(!$article_id) return error_code(10051);
        $article = new Article();
        return $article->articleDetail($article_id);
    }
}