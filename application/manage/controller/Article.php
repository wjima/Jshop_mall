<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/19 0019
 * Time: 11:18
 */
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
        if(Request::isAjax())
        {
            return $article->tableData(input('param.'));
        }
        return $this->fetch('',['sellerList'=>getSellerList()]);
    }


    /**
     *  文章添加
     * User:tianyu
     * @return array|mixed
     */
    public function add()
    {
        if(Request::isPost())
        {
            $article = new articleModel();
            return $article->addData(input('param.'));
        }
        return $this->fetch('add',['sellerList'=>getSellerList()]);
    }


    /**
     *
     *  文章编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $article = new articleModel();
        if(Request::isPost())
        {
            return $article->saveData(input('param.'));
        }
        $info = $article->with('sellerInfo,articleType')->where('id',input('param.id/d'))->find();
        if (!$info)
        {
            return error_code(10002);
        }
        return $this->fetch('edit',['info'=>$info]);
    }


    /**
     *
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $article = new articleModel();
        $result = ['status' => true,'msg' => '删除成功','data'=>''];
        if (!$article::destroy('id',input('param.id/d'))) {
            $result['status'] = false;
            $result['msg'] = '删除失败';
        }
        return $result;
    }

}