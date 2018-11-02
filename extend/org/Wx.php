<?php
/**
 * 微信接口通讯实现类
 */
namespace org;
use think\facade\Cache;
use think\facade\Log;

class Wx
{
    /**
     * 默认配置
     * @var array
     */
//    private $config = [
//        'appid' => 'wxd9d17d7370b11efd',
//        'secret' => '172c182d95a872fbd6148f5f1b4901e6'
//    ];


    /**
     * 一般小程序功能
     * 根据前台传过来的code 去取openid和session_key
     * @param $code
     * @return array
     */
    public function code_to_sessionkey($appid,$secret,$code)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $re = file_get_contents($url);
        $re = json_decode($re,true);
        if(!isset($re['errcode'])){
            $result['data'] = $re;
            $result['status'] = true;
        }else{
            $result['msg'] = $re['errcode'].":".$re['errmsg'];
        }
        $result['url'] = $url;
        return $result;
    }
    /**
     * 第三方小程序托管取得session_key方法
     * 根据前台传过来的code 去取openid和session_key
     * @param $code
     * @return array
     */
    public function c_code_to_sessionkey($appid,$code)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $component_appid = config('thirdwx.appid');
        $thirdWx = new ThirdWx();
        $component_access_token = $thirdWx->getComponentAccessToken();
        if(!$component_access_token){
            $result['msg'] = '微信平台没有取到component_access_token';
            return $result;
        }

        $re = file_get_contents('https://api.weixin.qq.com/sns/component/jscode2session?appid='.$appid.'&js_code='.$code.'&grant_type=authorization_code&component_appid='.$component_appid.'&component_access_token='.$component_access_token);
        $re = json_decode($re,true);
        if(!isset($re['errcode'])){
            $result['data'] = $re;
            $result['status'] = true;
        }else{
            $result['msg'] = $re['errcode'].":dd".$re['errmsg'];
        }
        return $result;
    }




    public function decrypt($edata,$iv,$sessionKey)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        if (strlen($sessionKey) != 24) {
            $result['msg'] = 'sessionKey不正确';
            return $result;
        }
        $aesKey=base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            $result['msg'] = 'iv不正确';
            return $result;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($edata);

        $re=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $data=json_decode($re,true);
        if( $data)
        {
            $result['status'] = true;
            $result['data'] = $data;
            return $result;
        }else{
            $result['msg'] = '解密错误';
            return $result;
        }
    }


    /**
     * 获取access_token
     * @param string $appid
     * @param string $secret
     * @return mixed
     */
    public function getAccessToken($appid = 'wxd9d17d7370b11efd', $secret = '172c182d95a872fbd6148f5f1b4901e6')
    {
        //todo::$appid和$secret从配置文件获取
        //查询是否有缓存的access_token todo::改成mysql数据库存储
        $key = $appid.'_'.$secret;
        $val = Cache::get($key);
        if(!$val)
        {
            //没有缓存的，去微信接口获取access_token
            $curl = new Curl();
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
            $res = $curl->get($url);
            $res = json_decode($res, true);
            $val = $res['access_token'];

            //存储缓存获取的access_token todo::改成mysql数据库存储
            Cache::set($key, $val, 7200);
        }
        //返回access_token
        return $val;
    }


    /**
     * 二维码生成
     * @param $access_token
     * @param string $store
     * @param string $page
     * @return array
     */
    public function getParameterQRCode($access_token, $store = '', $page = 'pages/index/index', $style)
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => ''
        ];

        $filename = "qrcode/".$store."-".$style['width']."-".$style['auto_color']."-".$style['line_color_r']."-".$style['line_color_g']."-".$style['line_color_b']."-".$style['is_hyaline'].".jpg";
        if(file_exists($filename))
        {
            //有这个二维码了
            $return['status'] = true;
            $return['msg'] = '二维码获取成功';
            $return['data'] = $filename;
        }
        else
        {
            //没有去官方请求生成
            $curl = new Curl();
            $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;
            $data = [
                'scene' => $store,
                'page' => $page,
                'width' => $style['width'],
                'auto_color' => $style['auto_color'],
                'line_color' => ['r'=>$style['line_color_r'],'g'=>$style['line_color_g'],'b'=>$style['line_color_b']],
                'is_hyaline' => $style['is_hyaline']
            ];

            $data = json_encode($data);
            $res = $curl->post($url, $data);

            $file = fopen($filename, "w");//打开文件准备写入
            fwrite($file, $res);//写入
            fclose($file);//关闭

            if(file_exists($filename))
            {
                $return['status'] = true;
                $return['msg'] = '二维码获取成功';
                $return['data'] = $filename;
            }
        }

        return $return;
    }

    /**
     * 发送模板消息
     * @param $appid
     * @param $secret
     * @param $message
     * @return bool
     */
    public function sendTemplateMessage($appid, $secret, $message)
    {
        $accessToken = $this->getAccessToken($appid, $secret);
        $curl        = new Curl();
        $url         = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $accessToken;

        $data        = json_encode($message);
        Log::info('模板消息发送：' . $data);

        $res         = $curl->post($url, $data);
        Log::info('模板消息返回：' . $res);
        $res = json_decode($res, true);
        if (isset($res['errcode']) && $res['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }

}