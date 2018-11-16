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
        $result['where'] = [];
        $result['field'] = "*";
        $result['order'] = ['sort'=>'asc'];
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
    public function saveData($data = []){
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数丢失',
        ];
        if($data['id']){
            $res = $this->save($data,['id'=>$data['id']]);
            if($res){
                $result['status'] = true;
                $result['msg'] = '保存成功';
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
}