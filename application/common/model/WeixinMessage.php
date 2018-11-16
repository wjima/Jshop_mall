<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Db;

class WeixinMessage extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    const TYPE_TEXT = 1;        //文本消息
    const TYPE_IMAGE = 2;       //图文消息
    const TYPE_IMAGE_TEXT = 3;       //图文消息
    const ATTENTION_YSE = 1;    //是关注自动回复
    const ATTENTION_NO = 2;     //不是关注自动回复
    const DEFAULT_YES = 1;  //默认回复，无关键词时回复
    const DEFAULT_NO = 2;   //不是默认回复
    /**
     * 保存消息
     * @param array $params
     * @return array
     */
    public function addData($params = [])
    {
        $result           = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => [],
        ];
        $id               = 0;
        $params['params'] = json_encode($params['params']);
        Db::startTrans();
        if ($params['id']) {
            if ($this->save($params, ['id' => $params['id']]) === false) {
                $result['status'] = false;
                $result['msg']    = '保存失败';
                Db::rollback();
            }
            $id = $params['id'];
        } else {
            if (!$this->save($params)) {
                $result['status'] = false;
                $result['msg']    = '保存失败';
                Db::rollback();
            }
            $id = $this->getLastInsID();
        }
        if ($params['is_attention']) {
            $res = $this->where([['id','neq',$id]])->update(['is_attention' => self::ATTENTION_NO]);
            if($res === false){
                Db::rollback();
            }
        }
        Db::commit();
        return $result;
    }



    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id desc'];
        return $result;
    }


    /**
     * 数据转换
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as &$val){
            switch ($val['type'])
            {
                case self::TYPE_TEXT:
                    $val['type'] = '文本消息';
                    break;
                case self::TYPE_IMAGE:
                    $val['type'] = '图片消息';
                    break;
                case self::TYPE_IMAGE_TEXT:
                    $val['type'] = '图文消息';
                    break;
                default:
                    $val['type'] = '文本消息';
            }
            $val['utime'] = getTime($val['utime']);
            if($val['is_attention'] == self::ATTENTION_YSE){
                $val['is_attention'] = '是';
            }else{
                $val['is_attention'] = '否';
            }
            if($val['is_default'] == self::DEFAULT_YES){
                $val['is_default'] = '是';
            }else{
                $val['is_default'] = '否';
            }
        }
        return $list;
    }


}
