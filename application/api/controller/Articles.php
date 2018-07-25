<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/31 0031
 * Time: 09:52
 */

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\ArticleType;
use app\common\model\Article;

class Articles extends Api
{

    /**
     *  获取对应商户全部文章分类列表
     * User:tianyu
     * @return array
     */
    public function getArticleType()
    {
        $articleType = new ArticleType();
        return $articleType->articleTypeList($this->sellerId);
    }


    /**
     *  获取指定分类下的文章列表
     * User:tianyu
     * @return array
     */
    public function getArticleList()
    {
        $type_id = input('type_id/d');
        $page = input('page/d',1);
        $limit = input('limit/d',10);
        if(!$type_id)
        {
            return $result = ['status'=>false,'msg'=>'缺少参数type_id'];
        }
        $article = new Article();
        return $article->articleList($this->sellerId,$type_id,$page,$limit);
    }

    /**
     *  获取单个文章的详细信息
     * User:tianyu
     * @return array
     */
    public function getArticleDetail()
    {
        $article_id = input('article_id/d');
        if(!$article_id)
        {
            return $result = ['status' => false,'mag' => '缺少参数article_id'];
        }
        $article = new Article();
        return $article->articleDetail($article_id);
    }
}