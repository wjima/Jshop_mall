<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

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
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tableData( $post )
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = 50; // 后台列表分页数量默认50条
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }


    /**
     *  分类添加
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data' => []
        ];

        $validate = new Validate($this->rule,$this->msg);
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
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => []
        ];

        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if ($this->allowField(true)->save($data, ['id' => $data['id']]) === false) {
                $result['status'] = false;
                $result['msg']    = '保存失败';
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
    public function getTree($arr,$pid=0,$step=0){
        global $tree;
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                $flg = str_repeat('└─',$step);
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
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleTypeList()
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];

        $list = $this->field('id,type_name')->select();

        $result['data']['list'] = $list;

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

}
