<?php
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\UserToken;

/**
 * 接口路由
 * Class Index
 * @package app\api\controller
 */
class Index extends Api
{
    protected $loginAction = [];
    protected $action = [];

    public function index()
    {

        //解决跨域问题
/*      header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式*/

        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
            exit;
        }
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');

        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $api = config('api.');
        $method = explode('.',input('param.method'));
        if(count($method) != 2 && count($method) != 3){
            return error_code(14002);
        }
        //如果有第三个参数，就是插件里的方法，走插件流程
        if(count($method) == 3){
            return $this->addons($method[2], $method[0], $method[1]);
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
    private function addons($addons,$c,$a){
        //判断插件是否安装
        if(!get_addons_status($addons)){
            return error_code(17001);
        }
        //取接口配置
        $api_conf = getAddonsConfigVal($addons,'api');

        if(!$api_conf){
            return error_code(10000);       //如果需要请求接口，一定要在 /addons/插件名称/config.php里配置api
        }

        //判断控制器是否存在
        if(!isset($api_conf[$c])){
            return error_code(17002);
        }
        $class_name = get_addon_class($addons,'controller',$api_conf[$c]['code']);
        if(!class_exists($class_name)){
            return error_code(17002);
        }
        $model = new $class_name();
        if ($model === false) {
            return error_code(10000);
        }
        //判断方法是否存在
        if(!isset($api_conf[$c]['method'][$a])){
            return error_code(17003);
        }
        //判断是否需要登陆
        if($api_conf[$c]['method'][$a]['is_login']){
            if(!input('?param.token')){
                return error_code(14006);
            }
            $userTokenModel = new UserToken();
            $result = $userTokenModel->checkToken(input('param.token'));
            if(!$result['status']){
                return error_code(14007);
            }else{
                $model->setInit($result['data']['user_id']);
            }
        }
        if(!method_exists($model,$api_conf[$c]['method'][$a]['code'])){
            return error_code(17003);
        }
        return call_user_func_array([$model, $api_conf[$c]['method'][$a]['code']], []);
    }
}
