<?php
/**
 * 接口路由
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\UserToken;

class Index extends Api
{
    protected $loginAction = [];
    protected $action = [];

    public function index()
    {
        //解决跨域问题
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $api = config('api.');
        $method = explode('.',input('param.method'));
        if(count($method) != 2){
            return error_code(14002);
        }
        if(isset($api[$method[0]])){
            $cname = $api[$method[0]]['code'];
        }else{
            return error_code(14003);
        }

        if(isset($api[$method[0]]['method'][$method[1]])){
            $aname = $api[$method[0]]['method'][$method[1]]['code'];
        }else{
            return error_code(14004);
        }

        //判断是否需要登陆
        if($api[$method[0]]['method'][$method[1]]['is_login']){
            if(!input('?param.token')){
                return error_code(14006);
            }
            $userTokenModel = new UserToken();
            $result = $userTokenModel->checkToken(input('param.token'));
            if(!$result['status']){
                return error_code(14007);
            }else{
                $this->userId = $result['data']['user_id'];
            }
        }else{
            $this->userId = 0;
        }

        try {
            $c = controller($cname);
            $c->setInit($this->userId);
        } catch (\Exception $e) {
            return error_code(14008);
        }
        if(!method_exists($c, $aname)){
            return error_code(14009);
        }
        trace(input('param.'),'api');
        $data = $c->$aname();
        trace($data,'api');
        return $data;
    }
}