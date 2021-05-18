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

        $pageconfig = json_decode($data,true);
        foreach ($pageconfig as $key => $value) {
            if (isset($value['value']) && $value['value']) {
                $iData[] = [
                    'widget_code' => $value['type'],
                    'page_code'   => $code,
                    'position_id' => $key,
                    'sort'        => $key + 1,
                    'params'      => json_encode($value['value'],320),
                ];
            }
        }
        if (!$this->saveAll($iData)) {
            Db::rollback();
            return error_code(10004);
        }
        Db::commit();
        $result['status'] = true;
        $result['msg']    = '保存成功';
        return $result;
    }
}