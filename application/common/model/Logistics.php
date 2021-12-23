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
use think\Db;

/**
 * 物流公司表
 * Class Logistics
 * @package app\common\model
 * @author keinx
 */
class Logistics extends Common
{
    public function add($data)
    {
        return $this->insert($data);
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name']){
            $where[] = ['logi_name', 'like', '%'.$post['name'].'%'];
        }
        if(isset($post['code']) && $post['code']){
            $where[] = ['logi_code','=',$post['code']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }

    public function getInfo($id = 0){
        return $this->where(['id'=>$id])->find();
    }

    /**
     * 保存物流公司
     * @param array $data
     * @return array
     */
    public function saveData($data = [])
    {
        $result = error_code(10004);
        if ($data['id']) {
            $res = $this->save($data, ['id' => $data['id']]);
            if ($res !== false) {
                $result['status'] = true;
                $result['msg']    = '保存成功';
            }
        }
        return $result;
    }
    /**
     * 获取全部物流公司
     * @return array|\PDOStatement|string|\think\Collection
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 11:25
     */
    public function getAll()
    {
        return $this->where([])
            ->order('sort asc')
            ->select();
    }


    public function getNameByCode($code)
    {
        if($code)
        {
            $where[] = ['logi_code', 'eq', $code];
            $info = $this->field('logi_name')->where($where)->find();
        }
        else
        {
            $info['logi_name'] = '';
        }

        return $info['logi_name']?$info['logi_name']:'';
    }
}