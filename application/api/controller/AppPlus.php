<?php

namespace app\api\controller;

use app\common\controller\Api;

class AppPlus extends Api
{

    public function checkVersion ()
    {

        $result = [
            'status' => true,
            'msg' => error_code(10024,true),
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