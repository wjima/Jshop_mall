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

class WeixinMediaMessage extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    /**
     * 保存消息
     * @param array $params
     * @return array
     */
    public function addData($params = [])
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => [],
        ];
        $ids    = [];
        Db::startTrans();
        foreach ($params as $key => $val) {
            if (isset($val['id']) && $val['id'] != '') {
                $this->save($val, ['id' => $val['id']]);
                $ids[] = $val['id'];
            } else {
                unset($val['id']);
                $id    = $this->insertGetId($val);
                $ids[] = $id;
            }
        }
        Db::commit();
        $result['data'] = $ids;
        return $result;
    }

    /**
     * 根据id获取图文消息内容
     * @param $id
     * @return null|static
     */
    public function getInfo($id)
    {
        $data = $this->get($id);
        if ($data) {
            $data              = $data->toArray();
            $data['image_url'] = _sImage($data['image']);
        }
        return $data;
    }
}
