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
use app\common\model\WeixinMediaMessage;
use think\facade\Cache;
use think\facade\Request;


/**
 * 文章
 * Class Articles
 * @package app\api\controller
 */
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
        if (!Cache::has("jshop_article_getarticletype")) {
            Cache::set("jshop_article_getarticletype", $articleType->articleTypeList(), 3600 * 5);
        }
        return Cache::get("jshop_article_getarticletype");
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
        $type_id = Request::param('type_id', 0);
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        if (!Cache::has("jshop_article_getarticlelist" .'_'. $limit . "_" . $page . "_" . $type_id)) {
            $article = new Article();
            Cache::set("jshop_article_getarticlelist". "_" . $limit . "_" . $page . "_" . $type_id, $article->articleList($type_id, $page, $limit), 3600 * 5);
        }
        return Cache::get("jshop_article_getarticlelist" .'_'. $limit . "_" . $page . "_" . $type_id);
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
        if (!$article_id) return error_code(10051);
        $article = new Article();
        if (!Cache::has("jshop_article_getarticledetail" . "_" . $article_id)) {
            Cache::set("jshop_article_getarticledetail". "_" . $article_id, $article->articleDetail($article_id), 3600 * 5);
        }
        return Cache::get("jshop_article_getarticledetail". "_" . $article_id);
    }


    /**
     * 微信信息
     * @return array|mixed
     */
    public function getWeixinMessage()
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => []
        ];
        $msg_id = Request::param('id', 0);
        if(!$msg_id) return error_code(10051);
        $messageModel = new WeixinMediaMessage();
        $result['data'] = $messageModel->getInfo($msg_id);
        $result['data']['content'] = clearHtml($result['data']['content'], ['width', 'height']);//清除文章中宽高
        $result['data']['content'] = str_replace("<img", "<img style='max-width: 100%'", $result['data']['content']);
        $result['data']['ctime'] = time_ago($result['data']['ctime']);
        return $result;
    }

    //pc端文章列表页左侧，取当前兄弟文章分类和子分类，加上热门推荐文章
    public function leftArticleType(){
        $articleType = new ArticleType();
        $type_id = $articleType::TOP_CLASS_PARENT_ID;

        if(!input('?param.type_id')){
            if(input('?param.article_id')){
                $article = new Article();
                $info = $article->where(['id'=>input('param.article_id')])->find();
                if($info){
                    $type_id = $info['type_id'];
                }
            }
        }else{
            $type_id = input('param.type_id');
        }

        return $articleType->leftInfo($type_id);

    }

    //文章搜索，只搜索标题和描述
    public function search(){
        $page        = input('page/d', 1);
        $limit       = input('limit/d',10);
        if(!input('?param.search_name')){
            return error_code('10000');
        }
        $article = new Article();
        return $article->search(input('param.search_name'),$page,$limit);
    }

}