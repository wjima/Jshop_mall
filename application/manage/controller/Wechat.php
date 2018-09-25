<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\WeixinPublish;
use Request;
use org\ThirdWx;
use app\common\model\WeixinAuthor;
use app\common\model\Template;
use think\facade\Cache;
use think\facade\Log;
use app\common\validate\WeixinAuthor as wxAuthorValidate;
use app\common\model\TemplateOrder;


class Wechat extends Manage
{

    private $author = [];//小程序授权信息
    private $authorType = 'b2c';//授权类型


    public function index()
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => '绑定小程序',
        ];
        if (!Request::isAjax()) {
            $this->error("非法访问", 'wechat/info');
        }

        if ($this->checkBind()) {
            $result['msg'] = '您已绑定小程序，请勿继续绑定';
            return $result;
        } else {
            $thirdWx       = new ThirdWx();
            $accessToken   = $thirdWx->getComponentAccessToken();
            $pre_auth_code = $thirdWx->getPreAuthCode($accessToken);
            $wechat        = config('thirdwx.');
            $this->assign('wechat', $wechat);
            $callbackurl = url("wechat/callback", '', '', true);
            $this->assign('callback', $callbackurl);
            $this->assign('pre_auth_code', $pre_auth_code);
        }
        $this->view->engine->layout(false);
        $result['status'] = true;
        $result['data']   = $this->fetch('index');
        return $result;
    }

    //重新授权页面
    public function reAuthor()
    {
        $thirdWx       = new ThirdWx();
        $accessToken   = $thirdWx->getComponentAccessToken();
        $pre_auth_code = $thirdWx->getPreAuthCode($accessToken);
        $wechat        = config('thirdwx.');
        $callbackurl   = url("wechat/callback", '', '', true);
        $url           = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=' . $wechat['appid'] . '&pre_auth_code=' . $pre_auth_code . '&redirect_uri=' . $callbackurl . '&auth_type=2';
        header('Location:' . $url);
        exit();
    }

    /**
     * 自助绑定小程序
     */
    public function edit()
    {
        $id                = input('id/d', 0);
        $weixinAuthorModel = new WeixinAuthor();

        if ($id) { //编辑时
            $authorInfo = $weixinAuthorModel->where(['id' => $id])->find();
            $this->assign('authorInfo', $authorInfo);
        } else { //第一次自助绑定时
            if ($this->checkBind()) {
                $this->error('请勿重复绑定', 'wechat/info');
            }
        }
        $wechat = config('thirdwx.');
        $this->assign('wechat', $wechat);
        return $this->fetch('edit');
    }

    public function doEdit()
    {
        $result            = [
            'status' => false,
            'data'   => '',
            'msg'    => '保存失败',
        ];
        $data              = input('request.');
        $weixinAuthorModel = new WeixinAuthor();

        $iData    = [
            'nick_name'        => $data['nick_name'],
            'head_img'         => $data['head_img'],
            'signature'        => $data['signature'],
            'user_name'        => $data['user_name'],//原始ID
            'principal_name'   => $data['principal_name'],
            'appid'            => $data['appid'],
            'appsecret'        => $data['appsecret'],
            'signature'        => $data['signature'],
            'principal_name'   => $data['principal_name'],
            'bind_type'        => $weixinAuthorModel::BINDTYPESELF,
            'author_type'        =>$this->authorType,
            'verify_type_info' => $data['verify_type_info'] ? $data['verify_type_info'] : '0',
        ];
        $validate = new wxAuthorValidate();
        if (!$validate->check($iData)) {
            $result['msg'] = $validate->getError();
            return $result;
        }
        $authorInfo = $weixinAuthorModel->field('bind_type,id')->where(['author_type' => $iData['author_type']])->find();
        if ($authorInfo&&$authorInfo['bind_type'] != $weixinAuthorModel::BINDTYPESELF) {
            $result['msg'] = '非法访问';
            return $result;
        }
        if (!$authorInfo) {//更新
            $res = $weixinAuthorModel->doAdd($iData);
        } else {//插入
            $res = $weixinAuthorModel->save($iData, ['id' => $authorInfo['id']]);
        }
        if ($res !== false) {
            $result['status'] = true;
            $result['msg']    = '保存成功';
            return $result;
        }
        return $result;
    }

    /**
     * 授权后回调地址，并获取授权信息
     * todo 更新授权app的服务器地址
     * @return mixed
     */
    public function callback()
    {
        $input   = input('request.');
        $thirdWx = new ThirdWx();
        /*$input = [
            'auth_code'=>'queryauthcode@@@ElCcDrHdcZMjwErZkjCwc6UyTpuOnw0jC2ZsjQfmLuS-jix1wr_qqOQ5GkquXfw5O1RM8y8VLzzwvG39zDnQaw',
            'expires_in'=>'3600',
        ];*/


        $authorization_info = $thirdWx->getAuthorizerAccessToken($input['auth_code']);
        if (!$authorization_info) {
            $this->error("授权失败", 'wechat/info');
        }
        //获取用户信息
        $authorizer_info = $thirdWx->getAuthorizerInfo($authorization_info['authorizer_appid']);
        if (!$authorization_info) {
            $this->error("授权失败", 'wechat/info');
        }
        $weixinAuthor      = $authorizer_info['authorizer_info'];
        $weixinAuthorModel = new WeixinAuthor();
        $iData             = [
            'nick_name'                => $weixinAuthor['nick_name'],
            'head_img'                 => $weixinAuthor['head_img'],
            'signature'                => $weixinAuthor['signature'],
            'service_type_info'        => $weixinAuthor['service_type_info']['id'],
            'verify_type_info'         => $weixinAuthor['verify_type_info']['id'],
            'user_name'                => $weixinAuthor['user_name'],//原始ID
            'qrcode_url'               => $weixinAuthor['qrcode_url'],
            'business_info'            => json_encode($weixinAuthor['business_info']),
            'principal_name'           => $weixinAuthor['principal_name'],
            'bind_type'                => $weixinAuthorModel::BINDTYPEAUTHOR,
            'authorization_info'       => json_encode($authorizer_info['authorization_info']),
            'appid'                    => $authorizer_info['authorization_info']['authorizer_appid'],
            'authorizer_refresh_token' => $authorizer_info['authorization_info']['authorizer_refresh_token'],
            'authorizer_access_token'  => $authorization_info['authorizer_access_token'],
            'func_info'                => json_encode($authorizer_info['authorization_info']['func_info']),
            'expires_in'               => time() + $authorization_info['expires_in'],
        ];
        if ($weixinAuthor['MiniProgramInfo']) {
            $iData['miniprograminfo'] = json_encode($weixinAuthor['MiniProgramInfo']);
        }
        $checkbelong = $weixinAuthorModel->getAuthorInfoByAppId($iData['appid']);
        if ($checkbelong) {
            $this->error("授权失败", 'Index/index');
        }
        if (!$checkbelong) {
            //授权http加入队列
            $jobClass             = 'app\\job\\Thirdwx@exec';
            $params['authorType'] = $this->authorType;
            //$params['seller_name'] = session('seller.seller_name');
            \think\Queue::push($jobClass, $params);//加入添加站点http队列
        }
        $exist = $weixinAuthorModel->field('id')->where(['user_name' => $iData['user_name']])->find();
        if ($exist) {
            $weixinAuthorModel->save($iData, ['id' => $exist['id']]);
        } else {
            if (!$weixinAuthorModel->doAdd($iData)) {
                Log::record("商户：" . '授权信息保存失败');
            }
        }
        if ($authorization_info) {
            $this->assign('auth_success', 'true');
        } else {
            $this->assign('auth_success', 'false');
        }
        $this->success("授权成功", 'wechat/info');
        return $this->fetch('callback');
    }

    /**
     *展示授权信息
     */
    public function info()
    {
        $weixinAuthorModel = new WeixinAuthor();
        $authorInfo        = $weixinAuthorModel->getAuthorInfo();
        if ($authorInfo) {
            $this->assign('isAuthor', 'true');
            $tpOrderModel = new TemplateOrder();
            $order_id     = $tpOrderModel->getTempOrder($authorInfo['appid']);
            $this->assign('order_id', $order_id);
        } else {
            $this->assign('isAuthor', 'false');
            $this->assign('order_id', false);

        }
        $this->assign('authorInfo', $authorInfo);
        return $this->fetch('info');
    }

    /**
     * 获取模板信息
     */
    public function template()
    {
        $templateModel = new Template();
        $data          = $templateModel->getAllTemplate($templateModel::TYPE_MINI);
        $this->assign('data', $data);

        return $this->fetch('template');
    }

    /**
     * 启用模板
     */
    public function enableTemplate()
    {
        $result      = [
            'status' => false,
            'data'   => '',
            'msg'    => '启用失败',
        ];
        $template_id = input('id/d', '0');
        if ($template_id === '') {
            $result['msg'] = '参数错误';
            return $result;
        }
        if (!$this->checkBind()) {
            $result['msg'] = '请先绑定小程序';
            return $result;
        }
        $tpOrderModel = new TemplateOrder();

        $orderId = $tpOrderModel->getTempOrder($this->author['appid'], $template_id);
        if ($orderId) {
            $result['msg'] = '您已启用过该模板，请不要重复启用';
            return $result;
        }
        $iData = [
            'template_id' => $template_id,
            'appid'       => $this->author['appid'],
        ];
        if (!$tpOrderModel->doAdd($iData)) {
            $result['msg'] = '启用失败';
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = '启用成功，请到我的模板中上传代码吧~';
        return $result;
    }


    /**
     * 关闭授权弹窗
     */
    public function closeAuthor()
    {
        Cache::set("closeauthor", 'true');
        $result = [
            'status' => true,
            'data'   => '',
            'msg'    => '关闭成功',
        ];
        return $result;
    }

    /**
     * 自助授权模式变更授权&二维码授权模式取消授权
     * @return array
     */
    public function changeAuthor()
    {
        $result            = [
            'status' => true,
            'data'   => '',
            'msg'    => '删除成功',
        ];
        $weixinAuthorModel = new WeixinAuthor();
        $res               = $weixinAuthorModel->where([])->delete();
        if ($res !== false) {
            return $result;
        }
        $result['status'] = false;
        $result['msg']    = '删除失败';
        return $result;
    }

    /**
     * 判断商户是否绑定过小程序
     */
    private function checkBind()
    {
        $weixinAuthorModel = new WeixinAuthor();
        $authorInfo        = $weixinAuthorModel->getAuthorInfo($this->authorType);
        if ($authorInfo) {
            $this->author = $authorInfo;
            return true;
        } else {
            return false;
        }
    }

}
