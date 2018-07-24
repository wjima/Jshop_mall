<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\WeixinPublish;
use Request;
use org\ThirdWx;
use app\common\model\SellerSetting;
use app\common\model\WeixinAuthor;
use app\common\model\Template;
use think\facade\Cache;
use think\facade\Log;
use app\common\validate\WeixinAuthor as wxAuthorValidate;
use app\common\model\TemplateOrder;
use app\common\model\Seller as SellerModel;

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
            'seller_id'        => $this->sellerId,
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
        $authorInfo = $weixinAuthorModel->field('bind_type,id')->where(['seller_id' => $this->sellerId, 'author_type' => $iData['author_type']])->find();
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
            'seller_id'                => $this->sellerId,
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
        if ($checkbelong && $checkbelong['seller_id'] !== $this->sellerId) {
            $this->error("授权失败", 'Index/index');
        }
        if (!$checkbelong) {
            //授权http加入队列
            $jobClass             = 'app\\job\\Thirdwx@exec';
            $params['authorType'] = $this->authorType;
            $params['seller_id']  = $this->sellerId;
            $params['seller_name'] = session('seller.seller_name');
            \think\Queue::push($jobClass, $params);//加入添加站点http队列
        }
        $exist = $weixinAuthorModel->field('id')->where(['user_name' => $iData['user_name']])->find();
        if ($exist) {
            $weixinAuthorModel->save($iData, ['id' => $exist['id']]);
        } else {
            if (!$weixinAuthorModel->doAdd($iData)) {
                Log::record("商户：" . $this->sellerId . '授权信息保存失败');
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
        $authorInfo        = $weixinAuthorModel->getAuthorInfo($this->sellerId);
        if ($authorInfo) {
            $this->assign('isAuthor', 'true');
            $tpOrderModel = new TemplateOrder();
            $order_id     = $tpOrderModel->getTempOrder($this->sellerId, $authorInfo['appid']);
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
        $list          = $templateModel->getAllTemplate($templateModel::TYPE_MINI);
        $tpOrderModel  = new TemplateOrder();
        foreach ($list as $key => $val) {
            $ishave = $tpOrderModel->field('id')->where(['template_id' => $val['id'], 'seller_id' => $this->sellerId])->find();
            if ($ishave) {
                $list[$key]['selected'] = 'true';
            } else {
                $list[$key]['selected'] = 'false';
            }
        }
        $tplist = $tpOrderModel->getAllTemp($this->sellerId);

        $this->assign('list', $list);
        $this->assign('tplist', $tplist);

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

        $orderId = $tpOrderModel->getTempOrder($this->sellerId, $this->author['appid'], $template_id);
        if ($orderId) {
            $result['msg'] = '您已启用过该模板，请不要重复启用';
            return $result;
        }
        $iData = [
            'seller_id'   => $this->sellerId,
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
     * 上传模板
     * @return array|string
     */
    public function uploadTemplate()
    {
        $result         = [
            'status' => false,
            'data'   => '',
            'msg'    => '上传失败',
        ];
        $template_id = input('id/d', '0');
        if ($template_id === '') {
            $result['msg'] = '参数错误';
            return $result;
        }
        $templateModel = new Template();
        $sellerModel   = new SellerModel();
        $site_token    = $sellerModel->getSellerToken($this->sellerId);
        //shop_logo
        $shop_logo = getShopSetting($this->sellerId,'shop_logo');
        $shop_logo = _sImage($shop_logo);
        //获取模板信息
        $template = $templateModel->where(['id' => $template_id])->find();
        if (!$template) {
            $result['msg'] = '模板不存在';
            return $result;
        }

        //获取小程序授权信息
        if (!$this->checkBind()) {
            $result['msg'] = '请先绑定小程序';
            return $result;
        }
        $wechat = config('thirdwx.');

        $template['ext_json'] = str_replace('{{$site_token}}', $site_token, $template['ext_json']);
        $template['ext_json'] = str_replace('{{$appid}}', $this->author['appid'], $template['ext_json']);
        $template['ext_json'] = str_replace('{{$nick_name}}', $this->author['nick_name'], $template['ext_json']);
        $template['ext_json'] = str_replace('{{$api_url}}', $wechat['api_url'], $template['ext_json']);
        $template['ext_json'] = str_replace('{{$signature}}', $this->author['signature'], $template['ext_json']);
        $template['ext_json'] = str_replace('{{$app_logo}}', $shop_logo, $template['ext_json']);
        $extJson              = json_decode($template['ext_json'], true);

        $data['ext_json']     = json_encode($extJson, 320);//wxd9d17d7370b11efd
        $data['template_id']  = $template['th_template_id'];
        $data['user_version'] = $template['version'];
        $data['user_desc']    = $template['user_desc'];
        //上传模板
        $thirdWx   = new ThirdWx();
        $commitRes = $thirdWx->commit($this->author['authorizer_access_token'], $data, $this->author, $msg);

        if ($commitRes) {
            $this->assign('publish_status', 'true');
        } else {
            $result['msg'] = $msg;
            return $result;
        }
        $this->assign('template_id', $template['id']);
        $this->view->engine->layout(false);
        $result['status'] = true;
        $result['msg']    = '上传成功';
        $result['data']   = $this->fetch('uploadRes');
        return $result;
    }

    /**
     * 上传成功，获取体验二维码
     */
    public function getQrcode()
    {
        $thirdWx = new ThirdWx();
        if (!$this->checkBind()) {
            echo '请先绑定小程序';
            exit();
        }
        //获取访问二维码
        $qrImage = $thirdWx->getQrcode($this->author['authorizer_access_token']);
        echo '<img src="' . $qrImage . '">';
        exit();
    }

    /**
     * 设置小程序页面标题和tag
     */
    public function verify()
    {
        $result         = [
            'status' => false,
            'data'   => '',
            'msg'    => '上传失败',
        ];
        $template_id = input('id/d', '0');
        if ($template_id === '') {
            $this->error("参数错误");
        }
        //获取可上传分类
        if (!$this->checkBind()) {
            $this->error("请先绑定", 'wechat/index');
        }
        $thirdWx      = new ThirdWx();
        $categoryList = $thirdWx->getCategory($this->author['authorizer_access_token'], $this->author);
        if (!$categoryList) {
            $this->error("授权过期，请重新授权", 'Wechat/reAuthor');//跳转重新授权页面
        }

        $pageList = $thirdWx->getPage($this->author['authorizer_access_token'], $this->author);

        $templateModel = new Template();

        $template = $templateModel->field('id,th_template_id')->where(['id' => $template_id])->find();

        $newCategoryList = [];
        if ($categoryList) {
            foreach ($categoryList as $key => $val) {
                $catname = $val['first_class'];
                $catid   = $val['first_id'];
                if ($val['second_class']) {
                    $catname .= '->' . $val['second_class'];
                    $catid   .= '_' . $val['second_id'];
                }
                if ($val['third_class']) {
                    $catname .= '->' . $val['third_class'];
                    $catid   .= '_' . $val['third_id'];
                }
                $newCategoryList[] = [
                    'id'   => $catid,
                    'name' => $catname,
                ];
            }
        }
        Cache::set($this->sellerId . $this->author['appid'] . 'wxacate', json_encode($newCategoryList));//写入缓存中
        $newPageList = [];
        if ($pageList) {
            foreach ($pageList as $key => $val) {
                $conf = Template::getWxaPage($template['id'], $val);
                if ($conf) {
                    $newPageList[$key] = [
                        'page'  => $val,
                        'tag'   => $conf['tag'],
                        'title' => $conf['title'],
                    ];
                }
            }
        }
        $this->assign('categoryList', $newCategoryList);
        $this->assign('pageList', $newPageList);
        $this->assign('th_template_id', $template['th_template_id']);
        return $this->fetch('verify');
    }

    /**
     *提交审核
     */
    public function doVerify()
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => '发布失败',
        ];

        $data           = input('item/a', []);
        $th_template_id = input('th_template_id');
        if (!$this->checkBind()) {
            $result['msg'] = '请先绑定小程序';
            return $result;
        }

        $wxcate = Cache::get($this->sellerId . $this->author['appid'] . "wxacate");
        $wxcate = json_decode($wxcate, true);
        if (!$wxcate) {
            $result['msg'] = '请先创建审核';
            return $result;
        }
        $itemList = [];
        $i        = 0;
        foreach ((array)$data as $key => $val) {
            if ($val['cat_id']) {
                $itemList[$i] = [
                    'address' => $val['page'],
                    'tag'     => $val['tag'],
                    'title'   => $val['title'],
                ];
                $catlist      = explode('_', $val['cat_id']);
                $catname      = '';
                foreach ($wxcate as $cate) {
                    if ($val['cat_id'] == $cate['id']) {
                        $catname = $cate['name'];
                    }
                }
                if (!$catname) {
                    return $result;
                }
                $catenamelist                = explode('->', $catname);
                $itemList[$i]['first_id']    = $catlist[0];
                $itemList[$i]['first_class'] = $catenamelist[0];
                if ($catlist[1]) {
                    $itemList[$i]['second_id']    = $catlist[1];
                    $itemList[$i]['second_class'] = $catenamelist[1];
                }
                if ($catlist[2]) {
                    $itemList[$i]['third_id']    = $catlist[2];
                    $itemList[$i]['third_class'] = $catenamelist[2];
                }

            }
        }
        if (!$itemList) {
            $result['msg'] = '请至少对一个页面选择类目';
            return $result;
        }

        $thirdWx             = new ThirdWx();
        $auData['item_list'] = $itemList;

        $auditid = $thirdWx->submitAudit($this->author['authorizer_access_token'], $auData, $this->author, $msg);
        //$auditid = '419643945';
        $templateModel = new Template();

        $template = $templateModel->field('id')->where(['th_template_id' => $th_template_id])->find();

        if ($auditid !== false) {
            $weixinPublishModel = new WeixinPublish();
            $iData              = [
                'seller_id'      => $this->sellerId,
                'template_id'    => $template['id'],
                'th_template_id' => $th_template_id,
                'audit_status'   => '-1',
                'reason'         => '',
                'auditid'        => $auditid,
                'appid'          => $this->author['appid'],
                'reason'         => $msg,
            ];
            $publish_id         = $weixinPublishModel->searchPublish($this->sellerId, $this->author['appid'], $auditid);
            if ($publish_id) {
                $res = $weixinPublishModel->updatePublish($iData, ['id' => $publish_id]);
            } else {
                $res = $weixinPublishModel->doAdd($iData);
            }
            if ($res) {
                $result['msg']    = '提交审核成功';
                $result['status'] = true;
            }
        } else {
            $result['msg'] = $msg;
        }

        return $result;
    }

    /**
     * 查询审核结果
     */
    public function getVerifyStatus()
    {
        $result             = [
            'status' => false,
            'data'   => '',
            'msg'    => '发布失败',
        ];
        $thirdWx            = new ThirdWx();
        $weixinPublishModel = new WeixinPublish();

        $publish = $weixinPublishModel->getAuditid($this->sellerId);

        if (!$this->checkBind()) {
            $result['msg'] = '请先绑定小程序';
            return $result;
        }
        $auditid = $publish['auditid'];
        $res     = $thirdWx->getAuditStatus($this->author['authorizer_access_token'], $auditid, $this->author, $msg);

        if ($res !== false) {
            $udata = [
                'audit_status' => $res,
                'reason'       => $msg,
                'auditid'      => $auditid,
            ];
            $weixinPublishModel->save($udata, ['id' => $publish['id'], 'seller_id' => $this->sellerId]);
            $result['status'] = true;
            $result['msg']    = $msg;
        } else {
            $result['msg'] = $msg;
        }
        return $result;
    }

    /**
     * 代码发布
     */
    public function release()
    {
        $result  = [
            'status' => false,
            'data'   => '',
            'msg'    => '发布失败',
        ];
        $thirdWx = new ThirdWx();
        if (!$this->checkBind()) {
            $result['msg'] = '请先绑定小程序';
            return $result;
        }
        $res = $thirdWx->release($this->author['authorizer_access_token'], $this->author, $msg);
        if ($res) {
            //发布状态更新
            $weixinPublishModel = new WeixinPublish();
            $udata              = [
                'publish_status' => '1',
                'publish_msg'    => $msg,
            ];
            $auditid            = $weixinPublishModel->getAuditid($this->sellerId);
            $weixinPublishModel->save($udata, ['auditid' => $auditid['auditid'], 'seller_id' => $this->sellerId]);
            $result['status'] = true;
            $result['msg']    = '发布成功';
        } else {
            $result['msg'] = $msg;
        }
        return $result;
    }

    /**
     * 小程序审核历史
     */
    public function verifyList()
    {
        if (Request::isAjax()) {
            $request              = input('param.');
            $request['seller_id'] = $this->sellerId;
            $weixinPublishModel   = new WeixinPublish();
            return $weixinPublishModel->tableData($request);
        }
        return $this->fetch('verifyList');
    }

    /**
     * 关闭授权弹窗
     */
    public function closeAuthor()
    {
        Cache::set($this->sellerId . "closeauthor", 'true');
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
        $res               = $weixinAuthorModel->where(['seller_id' => $this->sellerId])->delete();
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
        $authorInfo        = $weixinAuthorModel->getAuthorInfo($this->sellerId, $this->authorType);
        if ($authorInfo) {
            $this->author = $authorInfo;
            return true;
        } else {
            return false;
        }
    }

    public function setDomain()
    {
        //授权http加入队列
        $jobClass             = 'app\\job\\Thirdwx@exec';
        $params['authorType'] = $this->authorType;
        $params['seller_id']  = $this->sellerId;
        $params['seller_name'] = session('seller.seller_name');
        \think\Queue::push($jobClass, $params);//加入添加站点http队列
        return [
            'status'=>true,
            'msg'=>'操作成功，设置结果请到我的消息中查看',
            'data'=>[],
        ];
    }

}
