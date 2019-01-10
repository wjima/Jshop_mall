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

class Articles extends Api
{

    /**
     *  获取全部文章分类列表
     * User:tianyu
     * @return array
     */
    public function getArticleType()
    {
        $articleType = new ArticleType();
        return $articleType->articleTypeList();
    }


    /**
     *
     *  获取文章列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleList()
    {
        $article = new Article();
        return $article->articleList(input('type_id/d', 0), input('page/d',1), input('limit/d',10));
    }

    /**
     *  获取单个文章的详细信息
     * User:tianyu
     * @return array
     */
    public function getArticleDetail()
    {
        $article_id = input('article_id/d', 0);
        if(!$article_id) return error_code(10051);
        $article = new Article();
        return $article->articleDetail($article_id);
    }
}