<?php
// 应用公共文件
use think\Cache;
use think\Config;
use think\Cookie;
use think\Db;
use think\Debug;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Lang;
use think\Loader;
use think\Log;
use think\Model;
use think\Request;
use think\Response;
use think\Session;
use think\Url;
use think\View;
use think\Container;
use app\common\model\Operation;
use app\common\model\Area;
use app\common\model\Payments;
use app\common\model\Logistics;

//毫秒数
//返回当前的毫秒时间戳
function msectime() {
    list($tmp1, $tmp2) = explode(' ', microtime());
    return sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 1000);
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

//判断前端浏览器类型
function get_client_broswer(){
    $ua = $_SERVER['HTTP_USER_AGENT'];

    //微信内置浏览器
    if (stripos($ua, 'MicroMessenger')) {
        //preg_match('/MicroMessenger\/([\d\.]+)/i', $ua, $match);
        return "weixin";
    }
    //支付宝内置浏览器
    if (stripos($ua, 'AlipayClient')) {
        //preg_match('/AlipayClient\/([\d\.]+)/i', $ua, $match);
        return "alipay";
    }
    return false;
}
//生成编号
function get_sn($type){
    switch ($type)
    {
        case 1:         //订单编号
            $str = $type.substr(msectime().rand(0,9),1);
            break;
        case 2:         //支付单编号
            $str = $type.substr(msectime().rand(0,9),1);
            break;
        case 3:         //商品编号
            $str = 'G'.substr(msectime().rand(0,5),1);
            break;
        case 4:         //货品编号
            $str = 'P'.substr(msectime().rand(0,5),1);
            break;
        case 5:         //售后单编号
            $str = $type.substr(msectime().rand(0,9),1);
            break;
        case 6:         //退款单编号
            $str = $type.substr(msectime().rand(0,9),1);
            break;
        case 7:         //退货单编号
            $str = $type.substr(msectime().rand(0,9),1);
            break;
        case 8:         //发货单编号
            $str = $type.substr(msectime().rand(0,9),1);
            break;
        case 9:         //提货单号
            $str = 'T'.$type.substr(msectime().rand(0,5), 1);
            break;
        default:
            $str = substr(msectime().rand(0,9),1);
    }
    return $str;
}


/**
 * 获取hash值
 * @return string
 */
function get_hash()
{
    $chars   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
    $random  = $chars[mt_rand(0,73)] . $chars[mt_rand(0,73)] . $chars[mt_rand(0,73)] . $chars[mt_rand(0,73)] . $chars[mt_rand(0,73)];
    $content = uniqid() . $random;
    return sha1($content);
}

/**
 * @param $filename
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 11:32
 */
function get_file_extension($filename)
{
    $pathinfo = pathinfo($filename);
    return strtolower($pathinfo['extension']);
}

/***
 * 获取HASH目录
 * @param $name
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 15:26
 */
function get_hash_dir($name='default')
{
    $ident = sha1(uniqid('',true) . $name . microtime());
    $dir   = DIRECTORY_SEPARATOR . $ident{0} . $ident{1} . DIRECTORY_SEPARATOR . $ident{2} . $ident{3} . DIRECTORY_SEPARATOR . $ident{4} . $ident{5} . DIRECTORY_SEPARATOR;
    return $dir;
}

/**
 *
 * +--------------------------------------------------------------------
 * Description 递归创建目录
 * +--------------------------------------------------------------------
 * @param  string $dir 需要创新的目录
 * +--------------------------------------------------------------------
 * @return 若目录存在,或创建成功则返回为TRUE
 * +--------------------------------------------------------------------
 * @author gongwen
 * +--------------------------------------------------------------------
 */
function mkdirs($dir,$mode = 0777)
{
    if(is_dir($dir) || mkdir($dir,$mode,true)) return true;
    if(!mkdirs(dirname($dir),$mode)) return false;
    return mkdir($dir,$mode,true);
}


/**
 * 返回图片地址
 * TODO 水印，裁剪，等操作
 * @param $image_id
 * @param $type
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 18:34
 */
function _sImage($image_id,$type='s')
{
    if(!$image_id){
        return config('jshop.default_image');//默认图片
    }
    $image_obj= new \app\common\model\Images();
    $image=$image_obj->where(array(
        'id'=>$image_id
    ))->field('url')->find();
    if($image){
        if(stripos($image['url'],'http')!==false||stripos($image['url'],'https')!==false) {
            return str_replace("\\","/",$image['url']);
        }else{
            return request()->domain().str_replace("\\","/",$image['url']);
        }
    }else{
        return request()->domain().'/'.config('jshop.default_image');//默认图片
    }
}

/**
 * 相对地址转换为绝对地址
 */
function getRealUrl($url='')
{
    if(stripos($url,'http')!==false||stripos($url,'https')!==false) {
        return $url;
    }else{
        $storage_params = getSetting('image_storage_params');
        if($storage_params['domain']){
            return $storage_params['domain'].$url;
        }
        if(config('jshop.image_storage.domain')){
            return config('jshop.image_storage.domain').$url;
        }
        return request()->domain().$url;
    }
}

/**
 * 格式化数据化手机号码
 */
function format_mobile($mobile)
{
    return substr($mobile,0,5)."****".substr($mobile,9,2);
}

//如果没有登陆的情况下，记录来源url，并跳转到登陆页面
function redirect_url()
{
    if(cookie('?redirect_url')){
        $str = cookie('redirect_url');
        cookie('redirect_url',null);
    }else{
        $str = '/';
    }
    return $str;
}
//返回用户信息
function get_user_info($user_id,$field = 'mobile')
{
    $user = app\common\model\User::get($user_id);
    if($user){
        if($field == 'nickname') {
            $nickname = $user['nickname'];
            if ($nickname == '') {
                $nickname = format_mobile($user['mobile']);
            }
            return $nickname;
        }else{
            return $user->$field;
        }
    }else{
        return "";
    }
}
//返回商品信息
function get_goods_info($goods_id,$field = 'name')
{
    $goodsModel = new \app\common\model\Goods();
    $info = $goodsModel->where(['id'=>$goods_id])->find();
    if($info){
        if($field == 'image_id'){
            return _sImage($info[$field]);
        }else{
            return $info[$field];
        }
    }else{
        return '';
    }
}
//返回用户信息
function get_user_id($mobile)
{
    $userModel = new app\common\model\User();
    $user = $userModel->where(array('mobile'=>$mobile))->find();
    if($user){
        return $user->id;
    }else{
        return false;
    }
}

/**
 * 根据operation_id 取得链接地址
 * @param $id
 * @return string|Url
 */
function get_operation_url($id)
{
    $operationModel = new Operation();
    $actionInfo = $operationModel->where(array('id' => $id))->find();
    if (!$actionInfo) {
        return "";
    }
    $controllerInfo = $operationModel->where(array('id' => $actionInfo['parent_id']))->find();
    if (!$controllerInfo) {
        return "";
    }

    $modueInfo = $operationModel->where(array('id' => $controllerInfo['parent_id']))->find();
    if (!$modueInfo) {
        return "";
    }
    return url($modueInfo['code'] . '/' . $controllerInfo['code'] . '/' . $actionInfo['code']);
}
/**
 * 获取转换后金额
 * @param int $money
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-02-01 15:32
 */
function getMoney($money = 0)
{
    return sprintf("%.2f", $money);
}

//根据支付方式编码取支付方式名称等
function get_payment_info($payment_code,$field = 'name')
{
    $paymentModel = new Payments();
    $paymentInfo = $paymentModel->where(['code'=>$payment_code])->find();
    if($paymentInfo){
        return $paymentInfo[$field];
    }else{
        return $payment_code;
    }
}

//根据物流编码取物流名称等信息
function get_logi_info($logi_code,$field='logi_name')
{
    $logisticsModel = new Logistics();
    $logiInfo = $logisticsModel->where(['logi_code'=>$logi_code])->find();
    if($logiInfo){
        return $logiInfo[$field];
    }else{
        return $logi_code;
    }
}

/**
 * 根据地区id取省市区的信息
 * @param $area_id
 * @return string
 */
function get_area($area_id)
{
    $areaModel = new Area();
    $data = $areaModel->getArea($area_id);
    $parse = "";
    foreach($data as $v){
        if(isset($v['info'])){
            $parse .= $v['info']['name']." ";
        }
    }
    return $parse;
}
function error_code($code,$mini = false)
{
    $result = [
        'status' => false,
        'data' => 10000,
        'msg' => config('error.10000')
    ];
    if(config('?error.'.$code)){
        $result['data'] = $code;
        $result['msg'] = config('error.'.$code);
    }
    if($mini){
        return $result['msg'];
    }else{
        return $result;
    }
}

/**
 * 删除数组中指定值
 * @param $arr
 * @param $value
 * @return mixed
 */
function unsetByValue($arr, $value){
    $keys = array_keys($arr, $value);
    if(!empty($keys)){
        foreach ($keys as $key) {
            unset($arr[$key]);
        }
    }
    return $arr;
}

/**
 * 删除图片
 * @param $image_id
 * @return bool
 */
function delImage($image_id){
    $image_obj= new \app\common\model\Images();
    $image = $image_obj->where(['id'=>$image_id])->find();
    if($image){
        //删除图片数据
        $res = $image_obj->where(['id'=>$image_id])->delete();
        if($image['type']=='local'){
            $dd = @unlink($image['path']);
        }
        if($res){
            return true;
        }
        //默认本地存储，返回本地域名图片地址
    }else{
        return false;
    }
}

/**
 * 查询标签
 * @param $ids
 * @return array
 */
function getLabel($ids)
{
    if(!$ids){
        return [];
    }
    $label_obj = new \app\common\model\Label();
    $labels = $label_obj->field('name,style')->where('id', 'in', $ids)->select();
    if (!$labels->isEmpty()) {
        return $labels->toArray();
    }
    return [];
}

function getLabelStyle($style){
    $label_style='';
    switch ($style) {
        case 'red':
            $label_style = "";
            break;
        case 'green':
            $label_style = "layui-bg-green";
            break;
        case 'orange':
            $label_style = "layui-bg-orange";
            break;
        case 'blue':
            $label_style = "layui-bg-blue";
            break;
        default :
            $label_style = '';
    }
    return $label_style;
}

/* 单位自动转换函数 */
function getRealSize($size)
{
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if($size < $kb)
    {
        return $size . 'B';
    }
    else if($size < $mb)
    {
        return round($size/$kb, 2) . 'KB';
    }
    else if($size < $gb)
    {
        return round($size/$mb, 2) . 'MB';
    }
    else if($size < $tb)
    {
        return round($size/$gb, 2) . 'GB';
    }
    else
    {
        return round($size/$tb, 2) . 'TB';
    }
}

/**
 * url参数转换为数组
 * @param $query
 * @return array
 */
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}

/**
 * bool型转义
 * @param string $value
 * @return mixed
 */
function getBool($value='1'){
    $bool = ['1'=>'是','2'=>'否'];
    return $bool[$value];
}

/**
 * 时间格式化
 * @param int $time
 * @return false|string
 */
function getTime($time = 0){
    return date('Y-m-d H:i:s',$time);
}

/**
 * 标签转换
 * @param array $labels
 * @return string
 */
function getExportLabel($labels = []){
    $labelString = '';
    foreach((array)$labels as $v){
        $labelString = $v['name'].',';
    }
    return substr($labelString,0,-1);
}

/**
 * 上下架状态转换
 * @param string $status
 * @return string
 */
function getMarketable($marketable='1'){
    $status = ['1'=>'上架','2'=>'下架'];
    return $status[$marketable];
}



/**
 * 数组转xml
 * @param $arr
 * @param string $root
 * @return string
 */
function arrayToXml($arr, $root = "root")
{
    $xml = "<" . $root . ">";
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        }
    }
    $xml .= "</" . $root . ">";
    return $xml;
}

/**
 * 在模板中，有时候，新增的时候，要设置默认值
 * @param $val
 * @param $default
 * @return mixed
 */
function setDefault($val,$default)
{
    return $val?$val:$default;
}


/**
 * xml转数组
 * @param $xml
 * @return mixed
 */
function xmlToArray($xml)
{
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}

/**
 * 判断url是否内网ip
 * @param string $url
 * @return bool
 */
function isIntranet($url = '')
{
    $params = parse_url($url);
    $host = gethostbynamel($params['host']);
    if (is_array($host)) {
        foreach ($host as $key => $val) {
            if (!filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return true;
            }
        }
    }
    return false;
}

/**
 * 获取微信操作对象（单例模式）
 * @staticvar array $wechat 静态对象缓存对象
 * @param type $type 接口名称 ( Card|Custom|Device|Extend|Media|Oauth|Pay|Receive|Script|User )
 * @return \Wehcat\WechatReceive 返回接口对接
 */
function & load_wechat($type = '') {

    static $wechat = array();
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        // 从数据库获取配置信息
        $options = array(
            'token'           => getSetting('wx_official_token'), // 填写你设定的key
            'appid'           => getSetting('wx_official_appid'), // 填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'       => getSetting('wx_official_app_secret'), // 填写高级调用功能的密钥
            'encodingaeskey'  => getSetting('wx_official_encodeaeskey'), // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
            'mch_id'          => '', // 微信支付，商户ID（可选）
            'partnerkey'      => '', // 微信支付，密钥（可选）
            'ssl_cer'         => '', // 微信支付，双向证书（可选，操作退款或打款时必需）
            'ssl_key'         => '', // 微信支付，双向证书（可选，操作退款或打款时必需）
            'cachepath'       => '', // 设置SDK缓存目录（可选，默认位置在Wechat/Cache下，请保证写权限）
        );
        \Wechat\Loader::config($options);
        $wechat[$index] = \Wechat\Loader::get($type);
    }
    return $wechat[$index];

}

/**
 * 获取最近天数的日期和数据
 * @param $day
 * @param $data
 * @return array
 */
function get_lately_days($day, $data)
{
    $day = $day-1;
    $days = [];
    $d = [];
    for($i = $day; $i >= 0; $i --)
    {
        $d[] = date('d', strtotime('-'.$i.' day')).'日';
        $days[date('Y-m-d', strtotime('-'.$i.' day'))] = 0;
    }
    foreach($data as $v)
    {
        $days[$v['day']] = $v['nums'];
    }
    $new = [];
    foreach ($days as $v)
    {
        $new[] = $v;
    }
    return ['day' => $d, 'data' => $new];
}

/**
 * 判断用户积分是否满足
 * @param $user_id
 * @param $seller_id
 * @param $num //正整数
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function is_user_point($user_id, $seller_id, $num)
{
    $sellerUser = new \app\common\model\SellerUser();
    $res = $sellerUser->isPointBalance($user_id, $seller_id, $num);
    return $res;
}

/**
 * 商家发送信息助手
 * @param $seller_id
 * @param $user_id
 * @param $code
 * @param $params
 * @return array
 */
function sendMessage($user_id, $code, $params)
{
    $messageCenter = new \app\common\model\MessageCenter();
    return $messageCenter->sendMessage($user_id, $code, $params);
}


/**
 * 根据商户id和用户id获取openid (废弃方法)
 * @param $seller_id
 * @param $user_id
 * @return bool|array
 */
function getUserWxInfo($user_id)
{
    $wxModel = new \app\common\model\UserWx();
    $filter[] = ['user_id','eq',$user_id];
    $wxInfo = $wxModel->field('id,user_id,openid,unionid,avatar,nickname')->where($filter)->find();
    if($wxInfo){
        return $wxInfo->toArray();
    }else{
        return false;
    }
}



/**
 * 判断用户是否有新消息，用于前端显示小红点
 * @param $user_id
 * @param int $seller_id
 */
function hasNewMessage($user_id)
{
    $messageModel = new \app\common\model\Message();
    $re = $messageModel->hasNew($user_id);
    return $re;
}

//格式化银行卡号，前四位和最后显示原样的，其他隐藏
function bankCardNoFormat($cardNo){
    $n = strlen($cardNo);
    //判断尾部几位显示原型
    if($n%4 == 0){
        $j = 4;
    }else{
        $j = $n%4;
    }
    $str = "";
    for($i=0;$i<$n;$i++){
        if($i <4 || $i> $n-$j-1){
            $str .= $cardNo[$i];
        }else{
            $str .= "*";
        }
        if($i%4 == 3){
            $str .=" ";
        }
    }
    return $str;
}

/**
 * 获取系统设置
 * @param string $key
 * @return array
 */
function getSetting($key = ''){
    $systemSettingModel = new \app\common\model\Setting();
    return $systemSettingModel->getValue($key);
}

/***
 * 获取插件配置信息
 * @param string $name 插件名称
 * @return array
 */
function getAddonsConfig($name = ''){
    if(!$name){
        return [];
    }
    $addonModel = new \app\common\model\Addons();
    return $addonModel->getSetting($name);
}
//货品上的多规格信息，自动拆分成二维数组
function getProductSpesDesc($str_spes_desc){
    if($str_spes_desc == ""){
        return [];
    }
    $spes = explode(',',$str_spes_desc);
    $re = [];
    foreach($spes as $v){
        $val = explode(':',$v);
        $re[$val[0]] = $val[1];
    }
    return $re;
}

//返回管理员信息
function get_manage_info($manage_id,$field = 'username')
{
    $user = app\common\model\Manage::get($manage_id);
    if($user){
        if($field == 'nickname') {
            $nickname = $user['nickname'];
            if ($nickname == '') {
                $nickname = format_mobile($user['mobile']);
            }
            return $nickname;
        }else{
            return $user->$field;
        }
    }else{
        return "";
    }
}

/**
 * 数组倒排序，取新的键
 * @param array $array
 * @return array
 */
function _krsort($array = [])
{
    krsort($array);
    if (is_array($array)) {
        $i          = 0;
        $temp_array = [];
        foreach ($array as $val) {
            $temp_array[$i] = $val;
            $i++;
        }
        return $temp_array;
    } else {
        return $array;
    }
}

/**
 * 判断钩子是否有插件
 * @param string $hookname
 * @return bool
 */
function checkAddons($hookname = '')
{
    $hooksModel = new \app\common\model\Hooks();
    $addons     = $hooksModel->where(['name' => $hookname])->field('addons')->find();
    if (isset($addons['addons']) && !empty($addons['addons'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断商品是否参加团购
 * @param int $gid
 * @return array
 */
function isInGroup($gid = 0, &$promotion_id = 0)
{
    if (!$gid) {
        return false;
    }

    $promotion = new app\common\model\Promotion();

    $where[]   = ['p.status', 'eq', $promotion::STATUS_OPEN];
    $where[]   = ['p.stime', 'lt', time()];
    $where[]   = ['p.etime', 'gt', time()];
    $where[]   = ['pc.params', 'like', '%"' . $gid . '"%'];
    $where[]   = ['p.type', 'in', [$promotion::TYPE_GROUP, $promotion::TYPE_SKILL]];
    $condition = $promotion->field('p.id as id')
        ->alias('p')
        ->join('promotion_condition pc', 'pc.promotion_id = p.id')
        ->where($where)
        ->find();

    if ($condition) {
        $promotion_id = $condition['id'];
        return true;
    }
    return false;
}

/***
 * 判断是否json
 * @param $str
 * @return bool
 */
function isjson($str){
    return is_null(json_decode($str))?false:true;
}

/**
 * 判断是否手机号
 * @param $mobile
 * @return bool
 */
function isMobile($mobile = ''){
    if (preg_match("/^1[345678]{1}\d{9}$/", $mobile)) {
        return true;
    } else {
        return false;
    }
}