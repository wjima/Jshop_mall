<?php

/* *
 * 功能：共有有参数，暂未使用本页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
class ContentBuilder
{
    //第三方应用授权令牌
    private $appAuthToken;

    //异步通知地址(仅扫码支付使用)
    private $notifyUrl;

    //同步跳转地址
    private $returnUrl;

    public function setAppAuthToken($appAuthToken)
    {
        $this->appAuthToken = $appAuthToken;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function getAppAuthToken()
    {
        return $this->appAuthToken;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl=$returnUrl;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }
}