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
        $curl = new Curl();
        $re = $curl->get($url);
        $re = json_decode($re,true);
        if(!isset($re['errcode'])){
            $result['data'] = $re;
            $result['status'] = true;
        }else{
            $result['msg'] = $re['errcode'].":".$re['errmsg'];
        }
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
    public function getAccessToken($appid = '', $secret = '')
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
            Cache::set($key, $val, 3600);
        }
        //返回access_token
        return $val;
    }


    /**
     * 二维码生成
     * @param $access_token
     * @param string $page
     * @param string $invite
     * @param int $type
     * @param string $id
     * @param string $group_id
     * @param string $team_id
     * @param array $style
     * @param string $wx_appid
     * @return array
     */
    public function getParameterQRCode($access_token, $page = 'pages/share/jump', $invite = '', $type = 1, $id = '', $group_id = '', $team_id = '', $style = [], $wx_appid = '')
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => ''
        ];

        $styles = implode("-", $style);
        $filename = "static/qrcode/wechat/".md5($page.$invite.$type.$id.$group_id.$team_id.$wx_appid.$styles).".jpg";

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
            if($type == 1)
            {
                //商品详情页
                if($invite)
                {
                    $scene = share_parameter_encode('type=2&invite='.$invite.'&id='.$id);
                }
                else
                {
                    $scene = share_parameter_encode('type=2&id='.$id);
                }
            }
            else if($type == 2)
            {
                //首页
                if($invite)
                {
                    $scene = share_parameter_encode('type=3&invite='.$invite);
                }
                else
                {
                    $scene = share_parameter_encode('type=3');
                }
            }
            else if($type == 3)
            {
                //拼团
                if($invite)
                {
                    if($team_id)
                    {
                        $scene = share_parameter_encode('type=5&invite='.$invite.'&id='.$id.'&team_id='.$team_id);
                    }
                    else
                    {
                        $scene = share_parameter_encode('type=5&invite='.$invite.'&id='.$id);
                    }
                }
                else
                {
                    if($team_id)
                    {
                        $scene = share_parameter_encode('type=5&id='.$id.'&team_id='.$team_id);
                    }
                    else
                    {
                        $scene = share_parameter_encode('type=5&id='.$id);
                    }
                }
            }
            else if($type == 4)
            {
                //店铺首页
                if($invite)
                {
                    $scene = share_parameter_encode('type=9&invite='.$invite.'&id='.$id);
                }
                else
                {
                    $scene = share_parameter_encode('type=9&id='.$id);
                }
            }
            else
            {
                //默认首页
                if($invite)
                {
                    $scene = share_parameter_encode('type=3&invite='.$invite);
                }
                else
                {
                    $scene = share_parameter_encode('type=3');
                }
            }

            $data = [
                'scene' => $scene,
                'page' => $page
            ];
            if(isset($style['width']))
            {
                $data['width'] = $style['width'];
            }
            if(isset($style['auto_color']))
            {
                $data['auto_color'] = $style['auto_color'];
            }
            if(isset($style['line_color_r']) && isset($style['line_color_g']) && isset($style['line_color_b']))
            {
                $data['line_color'] = ['r'=>$style['line_color_r'],'g'=>$style['line_color_g'],'b'=>$style['line_color_b']];
            }
            if(isset($style['is_hyaline']))
            {
                $data['is_hyaline'] = $style['is_hyaline'];
            }
            $data = json_encode($data);
            $res = $curl->post($url, $data);
            $flag = json_decode($res, true);
            if($flag && $flag['errcode'] == 41030)
            {
                $return['msg'] = '后台小程序配置的APPID和APPSECRET对应的小程序未发布上线，无法生成海报';
                return $return;
            }
            elseif($flag && $flag['errcode'] == 40001)
            {
                $return['msg'] = '微信小程序access_token已过期，无法为你生成海报';
                return $return;
            }
            elseif($flag && isset($flag['errcode']))
            {
                $return['msg'] = $flag['errcode'].':'.$flag['errmsg'];
                return $return;
            }

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
     * 小程序发送模板消息
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

    /**
     * 后台生成小程序码
     * @param int $form_id
     * @return bool
     */
    public function getFormWxcode($form_id = 0)
    {
        if (!$form_id) {
            echo '关键参数丢失';
            exit();
        }
        $appid       = getSetting('wx_appid');
        $secret      = getSetting('wx_app_secret');
        $accessToken = $this->getAccessToken($appid, $secret);
        $path        = "pages/form/detail/form?id=" . $form_id;
        $curl        = new Curl();
        $url         = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $accessToken;
        $data        = [
            'path'       => $path,
            'width'      => 300,
            'is_hyaline' => false,
            'auto_color' => false
        ];
        $data        = json_encode($data, JSON_UNESCAPED_SLASHES);
        $res         = $curl->post($url, $data);
        Log::info('生成小程序码消息返回：' . $res);
        if (isjson($res)) {
            $res = json_decode($res, true);
            echo $res['errmsg'];
            exit;//输出错误信息
        } else {
            echo $res;
            exit();
        }
    }

}
