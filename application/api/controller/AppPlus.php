<?php

namespace app\api\controller;

use app\common\controller\Api;

class AppPlus extends Api
{

    public function checkVersion ()
    {

        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $params = [
            'platform' => $this->request->param('platform', 'android'),
            'version' => $this->request->param('version', '')
        ];


        if (checkAddons('appupdate'))
        {
            $result['data'] = hook('appupdate', $params);
        }

        return $result;

    }


}