<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;
use think\Validate;
use think\model\concern\SoftDelete;

class Brand extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';

    protected $rule = [
        'name'          => 'require|max:50',
        'sort'          => 'number',
    ];
    protected $msg = [
        'name.require'  => '请输入品牌名称',
        'name.max'      => '品牌名称长度最大50位',
        'sort'          => '排序必须为数字'
    ];


    /**
     * @param $post
     *
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
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }



    /**
     *  添加品牌
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data'=> [],
            'token'  => \think\facade\Request::token('__Jshop_Token__', 'sha1')
        ];
        $validate = new Validate($this->rule,$this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data)) {
                //                $result['status'] = false;
                //                $result['msg'] = '保存失败';
                return error_code(10004);
            }
        }
        return $result;
    }


    /**
     *  修改品牌
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data' => []
        ];
        $validate = new Validate($this->rule,$this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data, ['id' => $data['id']])) {
                //                $result['status'] = false;
                //                $result['msg'] = '保存失败';
                return error_code(10004);
            }
        }
        return $result;
    }


    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if(isset($post['utime']) && $post['utime'] != ""){
            $date_array = explode('到',$post['utime']);
            $sutime = strtotime($date_array[0].'00:00:00',time());
            $eutime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['utime', ['EGT',$sutime],['ELT',$eutime],'and'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as &$val){
            $val['logo'] = _sImage($val['logo']);
            $val['utime'] = getTime($val['utime']);
        }
        return $list;
    }


    /**
     * 获取全部品牌
     * @return array|\PDOStatement|string|\think\Collection
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 17:32
     */
    public function getAllBrand()
    {
        $filter = [];
        $data = $this->field('id,name')
            ->where($filter)
            ->order('sort asc')
            ->select();

        return $data;
    }

    /**
     * 根据名称获取品牌信息
     * @param string $name
     * @param bool $isForce 没有名称时，是否添加
     * @return int
     */
    public function getInfoByName($name = '',$isForce = false)
    {
        if (!$name) {
            return false;
        }
        $brand_id = 0;
        $brand = $this->field('id')->where([['name', 'like', '%' . $name . '%']])->find();
        if (!$brand && $isForce) {
            $this->save([
                'name' => $name,
            ]);
            $brand_id = $this->getLastInsID();
        } elseif ($brand) {
            $brand_id = $brand['id'];
        }
        return $brand_id;
    }
}