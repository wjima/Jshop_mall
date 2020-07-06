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

class PagesItems extends Common
{

    /**
     * 保存明细
     * @param $data
     * @param $code
     * @return bool
     * @throws \Exception
     */
    public function saveItems($data, $code)
    {
        $result = error_code(10004);
        Db::startTrans();
        $this->where([['page_code', '=', $code]])->delete();//先删除
        $iData = [];
        foreach ($data as $key => $value) {
            /*if (!$value['value']) {
                $result['msg'] = '组件：' . $value['name'] . '值不能为空';
                Db::rollback();
                return $result;
            }*/
            $iData[] = [
                'widget_code' => $value['type'],
                'page_code'   => $code,
                'position_id' => $key,
                'sort'        => $key + 1,
                'params'      => json_encode($value['value'],297),
            ];
        }
        if (!$this->saveAll($iData)) {
            Db::rollback();
            // $result['msg'] = error_code(10004,true);
            return error_code(10004);
        }
        Db::commit();
        $result['status'] = true;
        $result['msg']    = '保存成功';
        return $result;
    }


}