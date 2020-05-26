<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class Notice extends Common
{
    use SoftDelete;
    protected $deleteTime   = 'isdel';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';


    protected $rule =   [
        'title'         =>  'require',
        'content'       =>  'require',
        'sort'          =>  'number|max:5',
    ];

    protected $msg  =   [
        'title.require'     =>  '请输入标题名称',
        'content.require'   =>  '请输入公告内容',
        'sort.number'       =>  '排序只能是数字',
        'sort.max'          =>  '排序长度超出限制'
    ];


    /**
     * @param $post
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }


    /**
     *  添加公告
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function addData($data = [])
    {
        $result = ['status' => true, 'msg' => '保存成功' , 'data' => ''];

        $validate = new Validate($this->rule,$this->msg);
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data)) {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        return $result;
    }

    /**
     *  修改公告
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function saveData($data = [])
    {
        $result = ['status' => true, 'msg' => '保存成功', 'data' => ''];

        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if ($this->allowField(true)->save($data, ['id' => $data['id']]) === false) {
               return error_code(10004);
            }
        }
        return $result;
    }


    /**
     *  WHERE 搜索条件
     * User:tianyu
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['title']) && $post['title'] != ""){
            $where[] = ['title', 'like', '%'.$post['title'].'%'];
        }
        if(isset($post['ctime']) && $post['ctime'] != "") {
            $date_array = explode('~',$post['ctime']);
            $stime = strtotime($date_array[0].'00:00:00',time());   //从当天0点开始
            $etime = strtotime($date_array[1].'23:59:59',time());   //当天最后时间
            $where[] = ['ctime',['EGT',$stime],['ELT',$etime],'and'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id'=>'desc','sort'=>'ASC'];
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $val)
        {
            $val['ctime'] = getTime($val['ctime']);
        }
        return $list;
    }


    /**
     * @param $type
     * @param $order
     * @param $orderType
     * @param $page
     * @param $pageSize
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNoticeList($type = 1, $order = 'id', $orderType = 'desc', $page = 1, $pageSize = 10)
    {
        return $this->field('id,title,ctime')->where('type', $type)->order($order, $orderType)->page($page, $pageSize)->select();
    }


    public function getNoticeInfo ($id)
    {
        $info = $this->field('id,title,content,ctime')->where('id', $id)->find();
        if ($info) {
            $info['ctime'] = time_ago($info['ctime']);
        }

        return $info;
    }

}