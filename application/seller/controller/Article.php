<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/19 0019
 * Time: 11:18
 */
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Article as articleModel;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class Article extends Seller
{


    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $article = new articleModel();
        if(Request::isAjax())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $article->tableData($data);
        }
        //获取分类
        $articleType = new articleTypeModel();
        $typeList = $articleType->getList($this->sellerId);
        return $this->fetch('',['typeList'=>$typeList]);
    }


    /**
     *
     *  文章添加
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            $article = new articleModel();
            return $article->addData($data);
        }
        $articleType = new articleTypeModel();
        $typeList = $articleType->getList($this->sellerId);
        return $this->fetch('add',['typeList'=>$typeList]);
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
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $article->saveData($data);
        }
        $info = $article->with('articleType')->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();
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
        $result = ['status'=>true, 'msg'=>'删除成功', 'data' => ''];
        $article = new articleModel();
        if (!$article::destroy(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])) {
            $result['status'] = false;
            $result['msg'] = '删除失败';
        }
        return $result;
    }

}