<?php
/* *
 * 功能：支付宝手机网站alipay.data.dataservice.bill.downloadurl.query (查询对账单下载地址)接口调试入口页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 请确保项目文件有可写权限，不然打印不了日志。
 */

header("Content-type: text/html; charset=utf-8");


require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'buildermodel/AlipayDataDataserviceBillDownloadurlQueryContentBuilder.php';
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';
if (!empty($_POST['WIDbill_type']) && !empty($_POST['WIDbill_date'])){
	//账单类型，商户通过接口或商户经开放平台授权后其所属服务商通过接口可以获取以下账单类型：trade、signcustomer；
	//trade指商户基于支付宝交易收单的业务账单；signcustomer是指基于商户支付宝余额收入及支出等资金变动的帐务账单；
    $bill_type = trim($_POST['WIDbill_type']);
    //账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM。
    $bill_date = trim($_POST['WIDbill_date']);
    
    $RequestBuilder = new AlipayDataDataserviceBillDownloadurlQueryContentBuilder();
    $RequestBuilder->setBillType($bill_type);
    $RequestBuilder->setBillDate($bill_date);
    $Response = new AlipayTradeService($config);
    $result=$Response->downloadurlQuery($RequestBuilder);
    return ;
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>alipay.data.dataservice.bill.downloadurl.query(查询对账单下载地址)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
    *{
        margin:0;
        padding:0;
    }
    ul,ol{
        list-style:none;
    }
    body{
        font-family: "Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
    }
    .hidden{
        display:none;
    }
    .new-btn-login-sp{
        padding: 1px;
        display: inline-block;
        width: 75%;
    }
    .new-btn-login {
        background-color: #02aaf1;
        color: #FFFFFF;
        font-weight: bold;
        border: none;
        width: 100%;
        height: 30px;
        border-radius: 5px;
        font-size: 16px;
    }
    #main{
        width:100%;
        margin:0 auto;
        font-size:14px;
    }
    .red-star{
        color:#f00;
        width:10px;
        display:inline-block;
    }
    .null-star{
        color:#fff;
    }
    .content{
        margin-top:5px;
    }
    .content dt{
        width:100px;
        display:inline-block;
        float: left;
        margin-left: 20px;
        color: #666;
        font-size: 13px;
        margin-top: 8px;
    }
    .content dd{
        margin-left:120px;
        margin-bottom:5px;
    }
    .content dd input {
        width: 85%;
        height: 28px;
        border: 0;
        -webkit-border-radius: 0;
        -webkit-appearance: none;
    }
    #foot{
        margin-top:10px;
        position: absolute;
        bottom: 15px;
        width: 100%;
    }
    .foot-ul{
        width: 100%;
    }
    .foot-ul li {
        width: 100%;
        text-align:center;
        color: #666;
    }
    .note-help {
        color: #999999;
        font-size: 12px;
        line-height: 130%;
        margin-top: 5px;
        width: 100%;
        display: block;
    }
    #btn-dd{
        margin: 20px;
        text-align: center;
    }
    .foot-ul{
        width: 100%;
    }
    .one_line{
        display: block;
        height: 1px;
        border: 0;
        border-top: 1px solid #eeeeee;
        width: 100%;
        margin-left: 20px;
    }
    .am-header {
        display: -webkit-box;
        display: -ms-flexbox;
        display: box;
        width: 100%;
        position: relative;
        padding: 7px 0;
        -webkit-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
        background: #1D222D;
        height: 50px;
        text-align: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        box-pack: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        box-align: center;
    }
    .am-header h1 {
        -webkit-box-flex: 1;
        -ms-flex: 1;
        box-flex: 1;
        line-height: 18px;
        text-align: center;
        font-size: 18px;
        font-weight: 300;
        color: #fff;
    }
</style>
</head>
<body text=#000000 bgColor="#ffffff" leftMargin=0 topMargin=4>
<header class="am-header">
        <h1>支付宝手机网站查询对账单下载地址(接口名：alipay.data.dataservice.bill.downloadurl.query)</h1>
</header>
<div id="main">
        <form name=alipayment action='' method=post target="_blank">
            <div id="body" style="clear:left">
                <dl class="content">
                    <dt>账单类型：</dt>
                    <dd>
                        <input id="WIDbill_type" name="WIDbill_type" value="trade" />
                    </dd>
                    <hr class="one_line">
                    <dt>账单时间：</dt>
                    <dd>
                        <input id="WIDbill_date" name="WIDbill_date" />
                    </dd>
                    <hr class="one_line">
                    <dt></dt>
                    <dd>
                        <span style="line-height: 28px; color:red;">注意：账单类型和账单时间不能为空！账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM。</span>
                    </dd>
                    <dd id="btn-dd">
                        <span class="new-btn-login-sp">
                            <button class="new-btn-login" type="submit" style="text-align:center;">确 认</button>
                        </span>
                        <span class="note-help">如果您点击“确认”按钮，即表示您同意该次的执行操作。</span>
                    </dd>
                </dl>
            </div>
		</form>
        <div id="foot">
			<ul class="foot-ul">
				<li>
					支付宝版权所有 2015-2018 ALIPAY.COM 
				</li>
			</ul>
		</div>
	</div>
</body>
</html>