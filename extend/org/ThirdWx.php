<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/18
 * Time: 下午5:33
 */

namespace org;

include_once ROOT_PATH . DS . "extend/org/wechat/WXBizMsgCrypt.php";

use app\common\model\WeixinAuthor;
use org\Curl;
use think\facade\Cache;
use think\facade\Log;

class ThirdWx
{

    private $wxhost = 'https://api.weixin.qq.com/cgi-bin';//通用操作地址
    private $wxahost = 'https://api.weixin.qq.com/wxa';//小程序操作地址

    private $errcode = [
        '-1'    => '系统繁忙',
        '85064' => '找不到草稿',
        '85065' => '模板库已满',
        '42001' => 'token过期，请重新授权',
        '41001' => 'token过期，请重新授权',
        '47001' => '数据格式错误',
        '86000' => '不是由第三方代小程序进行调用',
        '86001' => '不存在第三方的已经提交的代码',
        '85006' => '标签格式错误',
        '85007' => '页面路径错误',
        '85008' => '类目填写错误',
        '85009' => '已经有正在审核的版本',
        '85010' => 'item_list有项目为空',
        '85011' => '标题填写错误',
        '85023' => '审核列表项目数超出范围',
        '85077' => '小程序类目信息失效，请重新选择类目',
        '86002' => '小程序还未设置昵称、头像、简介。请先设置完后再重新提交。',
        '89019' => '业务域名无更改，无需重复设置',
        '89020' => '尚未设置小程序业务域名',
        '89021' => '请求保存的域名不是第三方平台中已设置的小程序业务域名或子域名',
        '89029' => '业务域名数量超过限制',
        '85020' => '审核状态未满足发布',
        '85019' => '没有审核版本',
        '85015' => '该账号不是小程序账号',
        '85016' => '域名数量超过限制',
        '85017' => '没有新增域名，请确认小程序已经添加了域名或该域名是否没有在第三方平台添加',
        '85018' => '域名没有在第三方平台设置',
        '89019' => '业务域名无更改，无需重复设置',
        '89020' => '尚未设置小程序业务域名，请先在第三方平台中设置小程序业务域名后在调用本接口',
        '89021'=>'请求保存的域名不是第三方平台中已设置的小程序业务域名或子域名',
        '89029'=>'业务域名数量超过限制',
    ];

    /**
     * 微信消息解密
     * @param $xmlData
     * @return array
     */
    public function decrypt($xmlData)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '无解密参数',
        ];
        if ($xmlData == '') {
            return $result;
        }

        $wechat_config  = config('thirdwx.');
        $timeStamp      = '';
        $nonce          = '';
        $appId          = $wechat_config['appid'];
        $encodingAesKey = $wechat_config['encrypt_key'];
        $token          = $wechat_config['token'];
        $pc             = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $xml_tree       = new \DOMDocument();
        $xml_tree->loadXML($xmlData);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;

        $format   = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);
        $msg_sign = '';
        // 第三方收到公众号平台发送的消息
        $msg     = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            $parseXml = xmlToArray($msg);
            switch ($parseXml['InfoType']) {
                case 'component_verify_ticket'://微信发送授权口令
                    $this->setComponentVerifyTicket($parseXml);
                    break;
                case 'authorized':
                    $this->setAuthorized($parseXml);
                    break;
                case 'unauthorized'://取消授权
                    $this->unAuthorized($parseXml);
                    break;
                default:
                    $this->setComponentVerifyTicket($parseXml);
                    break;
            }
            $result['msg']    = '解密成功';
            $result['data']   = $parseXml;
            $result['status'] = true;
        } else {
            $result['msg'] = '解密失败，错误代码' . $errCode;
        }
        return $result;
    }

    /**
     * 设置授权秘钥
     * @param $info
     * @return bool
     */
    public function setComponentVerifyTicket($info)
    {
        Cache::set('component_verify_ticket', $info['ComponentVerifyTicket']);
        return true;
    }

    /***
     * 授权成功
     * @param $info
     * @return bool
     */
    public function setAuthorized($info)
    {
        return true;
    }

    /**
     * 取消授权
     * @param $info
     * @return bool
     */
    public function unAuthorized($info)
    {
        $weixinAuthorModel = new WeixinAuthor();
        $weixinAuthorModel->unAuthorized($info);
        return true;
    }

    /**
     * 获取第三方平台component_access_token
     * @return mixed
     */
    public function getComponentAccessToken()
    {
        $wechat_config = config('thirdwx.');

        $component_access_token = Cache::get('component_access_token');
        //$component_access_token=Cache::set('component_access_token',''); //授权异常时处理

        if (!$component_access_token) {
            $component_verify_ticket = Cache::get('component_verify_ticket');

            $curl                = new Curl();
            $component_token_url = $this->wxhost . '/component/api_component_token';
            $data                = [
                'component_appid'         => $wechat_config['appid'],
                'component_appsecret'     => $wechat_config['appsecret'],
                'component_verify_ticket' => $component_verify_ticket,
            ];
            $data                = json_encode($data);
            $res                 = $curl->post($component_token_url, $data);
            $res                 = json_decode($res, true);

            if (isset($res['component_access_token'])&&$res['component_access_token']) {
                Cache::set('component_access_token', $res['component_access_token'], 3600);//1个小时过期
                return $res['component_access_token'];
            } else {
                Log::record('获取component_access_token出错，错误信息：' . json_encode($res));
            }
        }
        return $component_access_token;
    }


    /***
     * 获取预授权码
     * @param string $access_token
     * @return string
     */
    public function getPreAuthCode($access_token = '')
    {
        $wechat_config = config('thirdwx.');

        $curl                       = new Curl();
        $api_create_preauthcode_url = $this->wxhost . '/component/api_create_preauthcode?component_access_token=' . $access_token;
        $data                       = [
            'component_appid' => $wechat_config['appid'],
        ];

        $data = json_encode($data);
        $res  = $curl->post($api_create_preauthcode_url, $data);
        $res  = json_decode($res, true);
        if ($res['pre_auth_code']) {
            return $res['pre_auth_code'];
        } else {
            Log::record('获取pre_auth_code出错，错误信息：' . $res);
            //todo 待处理
            return false;
        }
    }

    /**
     * 获取小程序的接口调用凭据
     * @param string $auth_code
     * @return array
     */
    public function getAuthorizerAccessToken($auth_code = '')
    {
        $wechat_config = config('thirdwx.');
        $accessToken   = $this->getComponentAccessToken();

        $curl               = new Curl();
        $api_query_auth_url = $this->wxhost . '/component/api_query_auth?component_access_token=' . $accessToken;
        $data               = [
            'component_appid'    => $wechat_config['appid'],
            'authorization_code' => $auth_code,
        ];
        $data               = json_encode($data);
        $res                = $curl->post($api_query_auth_url, $data);
        $res                = json_decode($res, true);
        if ($res['authorization_info']) {
            return $res['authorization_info'];
        } else {
            Log::record('获取pre_auth_code出错，错误信息：' . $res);
            //todo 待处理
            return false;
        }
    }

    /**
     * 刷新小程序token操作
     * @param string $authorizer_appid
     * @param string $refresh_token
     * @return bool|mixed
     */
    public function refreshAuthorToken($authorizer_appid = '', $refresh_token = '')
    {
        $wechat_config = config('thirdwx.');
        $accessToken   = $this->getComponentAccessToken();

        $curl               = new Curl();
        $api_query_auth_url = $this->wxhost . '/component/api_authorizer_token?component_access_token=' . $accessToken;
        $data               = [
            'component_appid'          => $wechat_config['appid'],
            'authorizer_appid'         => $authorizer_appid,
            'authorizer_refresh_token' => $refresh_token,
        ];
        $data               = json_encode($data);
        $res                = $curl->post($api_query_auth_url, $data);
        $accessToken        = json_decode($res, true);

        if ($accessToken['authorizer_access_token']) {
            //todo 直接更新授权信息
            return $accessToken;
        } else {
            Log::error('刷新用户' . $authorizer_appid . '出错，错误信息：' . $res);
            //todo 待处理
            return false;
        }
    }

    /**
     * 获取授权者信息
     * @param string $authorizer_appid
     * @return bool|mixed
     */
    public function getAuthorizerInfo($authorizer_appid = '')
    {
        $wechat_config = config('thirdwx.');
        $accessToken   = $this->getComponentAccessToken();

        $curl                = new Curl();
        $authorizer_info_url = $this->wxhost . '/component/api_get_authorizer_info?component_access_token=' . $accessToken;
        $data                = [
            'component_appid'  => $wechat_config['appid'],
            'authorizer_appid' => $authorizer_appid,
        ];
        $data                = json_encode($data);
        $res                 = $curl->post($authorizer_info_url, $data);
        $authorizerInfo      = json_decode($res, true);
        if ($authorizerInfo['authorizer_info']) {
            return $authorizerInfo;
        } else {
            Log::error('获取用户用户' . $authorizer_appid . '出错，错误信息：' . $res);
            //todo 待处理
            return false;
        }
    }

    /**
     * 获取草稿箱模板列表
     * @return array
     */
    public function getTemplateDraft()
    {
        $accessToken = $this->getComponentAccessToken();

        $curl         = new Curl();
        $template_url = $this->wxahost . '/gettemplatedraftlist?access_token=' . $accessToken;
        $res          = $curl->get($template_url);

        $templateList = json_decode($res, true);
        if ($templateList['errcode'] == '0') {
            return $templateList['draft_list'];
        } else {
            Log::error('获取草稿箱模板列表出错，错误信息：' . $res);
            //todo 待处理
            return false;
        }
    }

    /**
     * 获取模板列表
     * @return bool
     */
    public function getTemplate()
    {
        $accessToken = $this->getComponentAccessToken();

        $curl         = new Curl();
        $template_url = $this->wxahost . '/gettemplatelist?access_token=' . $accessToken;
        $res          = $curl->get($template_url);
        $templateList = json_decode($res, true);
        if ($templateList['errcode'] == '0') {
            return $templateList['template_list'];
        } else {
            Log::error('获取模板列表出错，错误信息：' . $res);
            //todo 待处理
            return false;
        }
    }

    /**
     * 添加草稿到模板列表
     * @param int $draft_id
     * @param string $msg
     * @return bool
     */
    public function addToTemplate($draft_id = 0, &$msg = '')
    {
        $wechat_config = config('thirdwx.');
        $accessToken   = $this->getComponentAccessToken();

        $curl    = new Curl();
        $add_url = $this->wxahost . '/component/addtotemplate?access_token=' . $accessToken;
        $data    = [
            'draft_id' => $draft_id,
        ];
        $data    = json_encode($data);
        $res     = $curl->post($add_url, $data);
        $res     = json_decode($res, true);
        if ($res['errcode'] == '0') {
            return true;
        } else {
            $msg = $this->getError($res);
            Log::error($msg);
            return false;
        }
    }

    /**
     * 删除指定模板
     * @param int $template_id
     * @param string $msg
     * @return bool
     */
    public function deleteTemplate($template_id = 0, &$msg = '')
    {
        $accessToken = $this->getComponentAccessToken();

        $curl       = new Curl();
        $delete_url = $this->wxahost . '/component/deletetemplate?access_token=' . $accessToken;
        $data       = [
            'template_id' => $template_id,
        ];
        $data       = json_encode($data);
        $res        = $curl->post($delete_url, $data);
        $res        = json_decode($res, true);
        if ($res['errcode'] == '0') {
            $msg = '删除成功';
            return true;
        } else {
            $msg = $this->getError($res);
            Log::error($msg);
            return false;
        }
    }

    /**
     * 为授权的小程序帐号上传小程序代码
     * @param string $accessToken 第三方平台获取到的该小程序授权的authorizer_access_token
     * @param array $data 包含template_id，ext_json，user_version，user_desc
     * @param array $author 授权信息
     * @return array
     */
    public function commit($accessToken = '', $data = [], &$author = [], &$msg = '')
    {
        $curl       = new Curl();
        $delete_url = $this->wxahost . '/commit?access_token=' . $accessToken;
        $data       = [
            'template_id'  => $data['template_id'],
            'ext_json'     => $data['ext_json'],
            'user_version' => $data['user_version'],
            'user_desc'    => $data['user_desc'],
        ];

        $data = json_encode($data);

        $res = $curl->post($delete_url, $data);
        $res = json_decode($res, true);
        if ($res['errcode'] == '0') {
            $msg = '上传成功';
            return true;
        } else {
            $msg = $this->getError($res);
            Log::error($msg);
            return false;
        }
    }

    /**
     * 获取体验二维码
     * @param string $accessToken
     * @return mixed
     */
    public function getQrcode($accessToken = '')
    {
        $qr_url = $this->wxahost . '/get_qrcode?access_token=' . $accessToken;
        $heads  = get_headers($qr_url, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            header("Content-type: text/html; charset=utf-8");
            echo '无法获取体验二维码，请联系客服';
            exit();
        }
        //判断是否内网
        if (!isIntranet($qr_url)) {
            header('content-type:image/jpg;');
            $content = file_get_contents($qr_url);
            echo $content;
            exit();
        } else {
            header("Content-type: text/html; charset=utf-8");
            echo '无法获取体验二维码，请联系客服';
            exit();
        }

    }

    /**
     * 获取可审核分类
     * @param string $accessToken
     * @return bool
     */
    public function getCategory($accessToken = '', &$author)
    {
        $curl         = new Curl();
        $category_url = $this->wxahost . '/get_category?access_token=' . $accessToken;
        $res          = $curl->get($category_url);
        $res          = json_decode($res, true);

        if ($res['errcode'] == '0') {
            return $res['category_list'];
        } else {
            if ($res['errcode'] == '42001' || $res['errcode'] == '41001') {
                $refresh = $this->refreshAuthorToken($author['appid'], $author['authorizer_refresh_token']);

                if ($refresh) {
                    $author['authorizer_refresh_token'] = $refresh['authorizer_refresh_token'];
                    $author['authorizer_access_token']  = $refresh['authorizer_access_token'];
                    return $this->getCategory($refresh['authorizer_access_token'], $author);
                } else {
                    //todo
                    Log::record('token刷新失败');
                }
            }
            $msg = $this->getError($res);
            Log::record($msg);
            return false;
        }
    }

    /**
     * 获取可审核页面
     * @param string $accessToken
     * @return array
     */
    public function getPage($accessToken = '', &$author)
    {
        $curl     = new Curl();
        $page_url = $this->wxahost . '/get_page?access_token=' . $accessToken;
        $res      = $curl->get($page_url);
        $res      = json_decode($res, true);
        if ($res['errcode'] == '0') {
            return $res['page_list'];
        } else {
            if ($res['errcode'] == '42001') {
                $refresh = $this->refreshAuthorToken($author['appid'], $author['authorizer_refresh_token']);
                if ($refresh) {
                    $author['authorizer_refresh_token'] = $refresh['authorizer_refresh_token'];
                    $author['authorizer_access_token']  = $refresh['authorizer_access_token'];
                    return $this->getPage($refresh['authorizer_access_token'], $author);
                } else {
                    //todo
                    Log::record('token刷新失败');
                }
            }
            $msg = $this->getError($res);
            Log::record($res);
            return false;
        }
        return [];
    }

    /**
     * 小程序提交审核
     * @param $accessToken
     * @param array $itemList
     * @param array $author
     * @return bool
     */
    public function submitAudit($accessToken, $itemList = [], &$author = [], &$msg = '')
    {

        $curl       = new Curl();
        $subimt_url = $this->wxahost . '/submit_audit?access_token=' . $accessToken;

        $data = json_encode($itemList, 320);
        $res  = $curl->post($subimt_url, $data);
        $res  = json_decode($res, true);
        Log::record($res);
        if ($res['errcode'] == '0') {
            $msg = '提交成功';
            return $res['auditid'];
        } else {
            if ($res['errcode'] == '42001') {
                $refresh = $this->refreshAuthorToken($author['appid'], $author['authorizer_refresh_token']);
                if ($refresh) {
                    $author['authorizer_refresh_token'] = $refresh['authorizer_refresh_token'];
                    $author['authorizer_access_token']  = $refresh['authorizer_access_token'];
                    return $this->submit_audit($refresh['authorizer_access_token'], $itemList, $author, $msg);
                } else {
                    //todo
                    Log::record('token刷新失败');
                }
            }
            $msg = $this->getError($res);
            Log::record($res);
            return false;
        }
    }

    /**
     * 查询审核信息
     * @param $accessToken
     * @param string $auditid
     * @param array $author
     * @return bool
     */
    public function getAuditStatus($accessToken, &$auditid = '', &$author = [], &$msg = '')
    {
        $curl       = new Curl();
        $search_url = $this->wxahost . '/get_auditstatus?access_token=' . $accessToken;
        if (!$auditid) {
            $search_url = $this->wxahost . '/get_latest_auditstatus?access_token=' . $accessToken;
            $res        = $curl->get($search_url);
        } else {
            $data = [
                'auditid' => $auditid,
            ];

            $data = json_encode($data);
            $res  = $curl->post($search_url, $data);
        }

        $res = json_decode($res, true);
        if ($res['errcode'] == '0') {
            if ($res['auditid']) {
                $auditid = $res['auditid'];
            }
            if ($res['status'] == '0') {
                $msg = '审核成功';
            } elseif ($res['status'] == '1') {
                $msg = '审核失败。失败原因：' . $res['reason'];
            } elseif ($res['status'] == '2') {
                $msg = '审核中';
            }else{
                $msg = 'token过期';
            }
            return $res['status'];
        } else {
            if ($res['errcode'] == '42001') {
                $refresh = $this->refreshAuthorToken($author['appid'], $author['authorizer_refresh_token']);
                if ($refresh) {
                    $author['authorizer_refresh_token'] = $refresh['authorizer_refresh_token'];
                    $author['authorizer_access_token']  = $refresh['authorizer_access_token'];
                    return $this->getAuditStatus($refresh['authorizer_access_token'], $auditid, $author);
                } else {
                    //todo
                    Log::record('token刷新失败');
                }
            }
            $msg = $this->getError($res);
            Log::record($msg);
            return false;
        }
    }

    /**
     * 小程序发布已审核的小程序
     * @param $accessToken
     * @param array $author
     * @return bool
     */
    public function release($accessToken, &$author = [], &$msg = '')
    {
        $curl        = new Curl();
        $release_url = $this->wxahost . '/release?access_token=' . $accessToken;

        $data = "{}";
        $res  = $curl->post($release_url, $data);
        $res  = json_decode($res, true);
        if ($res['errcode'] == '0') {
            $msg = '发布成功';
            return true;
        } else {
            $msg = $this->getError($res);
            Log::record($res);
            return false;
        }
    }

    /**
     * 修改服务器域名
     * @param $accessToken
     * @param $author
     * @param string $msg
     * @return bool
     */
    public function modify_domain($accessToken, &$author, &$msg = '')
    {
        $curl        = new Curl();
        $release_url = $this->wxahost . '/modify_domain?access_token=' . $accessToken;
        $wechat      = config('thirdwx.');
        $data        = [
            'action'          => 'add',
            'requestdomain'   => [$wechat['requestdomain']],
            'wsrequestdomain' => [$wechat['wsrequestdomain']],
            'uploaddomain'    => [$wechat['uploaddomain']],
            'downloaddomain'  => [$wechat['downloaddomain']],
        ];
        $data        = json_encode($data, 320);
        $res         = $curl->post($release_url, $data);
        $res         = json_decode($res, true);
        if ($res['errcode'] == '0') {
            $msg = '修改成功';
            return true;
        } else {
            $msg = $this->getError($res);
            Log::record($res);
            return false;
        }
    }

    /**
     *
     * @param $accessToken
     * @param $author
     * @param string $msg
     * @return bool
     */
    public function setWebViewDomain($accessToken, &$author, &$msg = '')
    {
        $curl        = new Curl();
        $release_url = $this->wxahost . '/setwebviewdomain?access_token=' . $accessToken;
        $wechat      = config('thirdwx.');

        $data = [
            'action'        => 'add',
            'webviewdomain' => [$wechat['webviewdomain']],
        ];
        $data = json_encode($data, 320);
        $res  = $curl->post($release_url, $data);
        $res  = json_decode($res, true);
        if ($res['errcode'] == '0') {
            $msg = '业务域名设置成功';
            return true;
        } else {
            if ($res['errcode'] == '42001') {
                $refresh = $this->refreshAuthorToken($author['appid'], $author['authorizer_refresh_token']);
                if ($refresh) {
                    $author['authorizer_refresh_token'] = $refresh['authorizer_refresh_token'];
                    $author['authorizer_access_token']  = $refresh['authorizer_access_token'];
                    return $this->setWebViewDomain($refresh['authorizer_access_token'], $author, $msg);
                } else {
                    //todo
                    Log::record('token刷新失败');
                }
            }
            $msg = $this->getError($res);
            Log::record($res);
            return false;
        }
    }

    /**
     * 获取错误信息
     * @param $res
     * @return mixed
     */
    private function getError($res){

        if(isset($this->errcode[$res['errcode']])){
            return $this->errcode[$res['errcode']];
        }else{
            return $res['errmsg'];
        }
    }

}