<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/19 0019
 * Time: 13:31
 */

namespace app\common\model;
use think\Validate;

class ArticleType extends Common
{

    protected $rule =   [
        'type_name'  => 'require|max:25',
    ];

    protected $msg  =   [
        'type_name.require' => '分类名称必须',
        'type_name.max'     => '名称最多不能超过25个字符',
    ];


    /**
     *  后台分类 树形列表
     * @param int $seller_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($seller_id = 0)
    {
        $res = $this->with('sellerInfo')->where('seller_id',$seller_id)->select();
        return $this->getTree($res);
    }


    /**
     *  分类添加
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status' => true,'msg' => '保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data))
            {
                $result['status'] = false;
                $result['msg'] = '保存失败';
            }
        }
        return $result;

    }


    /**
     *  分类修改
     * User:tianyu
     * @param $data
     * @return array
     */
    public function editData($data)
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status' => true,'msg' => '保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data,['id'=>$data['id']]))
            {
                $result['status'] = false;
                $result['msg'] = '保存失败';
            }
        }
        return $result;
    }



    /**
     *  递归遍历表格输出
     * User:tianyu
     * @param $arr  要输出的数组
     * @param $pid   父id
     * @param $step 节点替换次数
     * @return array
     */
    private function getTree($arr,$pid=0,$step=0){
        global $tree;
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                $flg = str_repeat('└―',$step);
                $val['type_name'] = $flg.$val['type_name'];
                $tree[] = $val;
                $this->getTree($arr , $val['id'] ,$step+1);
            }
        }
        return $tree;
    }


    /**
     *
     *  获取文章分类列表
     * @param $seller_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleTypeList($seller_id)
    {
        $list = $this->where('seller_id',$seller_id)->select();
        if(!$list->isEmpty())
        {
            $result = [
                'status' =>  true,
                'msg'    =>  '获取成功',
                'data'   =>  [
                    'list' => $list,
                    'count' => count($list)
                ],
            ];
        }else{
            $result = [
                'status' =>  false,
                'msg'    =>  '获取失败',
                'data'   =>  ''
            ];
        }
        return $result;
    }



    /**
     *  文章分类 与 文章 一对多关联
     * User:tianyu
     * @return \think\model\relation\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Article');
    }


    /**
     *
     *  关联seller表
     * @return \think\model\relation\HasOne
     */
    public function sellerInfo()
    {
        return $this->hasOne('Seller','id','seller_id')->bind(['seller_name']);
    }

}
