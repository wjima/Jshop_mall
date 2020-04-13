<?php

namespace addons\Pc\model;

use think\Model;

class PcSettings extends Model
{

    public $skeys = [
        'welcomes'  => [
            'name' => '欢迎语',
            'val'  => '欢迎语'
        ],
        'phone'     => [
            'name' => '客服电话',
            'val'  => '平台描述会展示在前台及微信分享店铺描述'
        ],
        'business'  => [
            'name' => '工作时间',
            'val'  => '09:00:00 - 18:00:00'
        ],
        'app_image' => [
            'name' => 'APP二维码',
            'val'  => ''
        ],
    ];

    public function settings($data)
    {
        $return = [
            'status' => false,
            'msg'    => '设置失败',
            'data'   => [],
        ];
        foreach ($data as $k => $v) {
            $rel = $this->where('k', 'eq', $k)->update(['val' => $v]);
            if (!$rel && $rel !== 0) {
                return $return;
            }
        }
        $return = [
            'status' => true,
            'msg'    => '设置成功',
            'data'   => [],
        ];
        return $return;
    }

    //取得全部参数
    public function getAll()
    {
        $list = $this->select();
        foreach ($this->skeys as $k => $v) {
            foreach ($list as $info) {
                if ($info['k'] == $k) {
                    if (isjson($info['val'])) {
                        $info['val'] = json_decode($info['val'], true);
                    }
                    $this->skeys[$k]['val'] = $info['val'];
                    break;
                }
            }
        }
        return $this->skeys;
    }

    //设置参数
    public function setValue($skey, $value)
    {

        $info = $this->where(array('k' => $skey))->find();
        if ($info) {
            $info->val = $value;
            $info->save();
        } else {
            $model = new $this;
            $model->save([
                'k'   => $skey,
                'val' => $value
            ]);
        }
        $result['status'] = true;
        return $result;

    }
}
