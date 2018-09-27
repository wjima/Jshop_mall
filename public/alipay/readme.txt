一、免责申明：
此DEMO仅供参考，实际开发中需要结合具体业务场景修改使用。

二、demo的运行环境：适用于php5.5以上的开发环境

代码简要说明
wappay文件夹下
	buildermodel ---------- 对应的接口的bizcontent业务参数进行封装处理，且做了json转换，比字符串传参更佳方便。
	service->AlipayTradeService.php      ---------- 所有接口中使用的方法。


AlipayTradeService.php 文件内方法说明

1、SDK请求方法
aopclientRequestExecute($request,$ispage=false)
$request：对应接口请求的对象
$ispage：是否为页面跳转请求（手机网站支付必须为页面跳转，查询，退款则可以无需页面跳转）

2、手机网站支付接口的方法
wapPay($builder,$return_url,$notify_url)
$builder：业务参数，使用buildmodel中的对象生成。
$return_url：同步跳转地址
$notify_url：异步通知地址

3、手机网站查询接口
Query($builder)
$builder：业务参数，使用buildmodel中的对象生成。

4、手机网站退款接口
Refund($builder)
$builder：业务参数，使用buildmodel中的对象生成。

5、手机网站关闭接口
Close($builder)
$builder：业务参数，使用buildmodel中的对象生成。

6、手机网站退款查询接口
refundQuery($builder)
$builder：业务参数，使用buildmodel中的对象生成。

7、手机网站账单下载接口
downloadurlQuery($builder)
$builder：业务参数，使用buildmodel中的对象生成。

8、支付宝返回的信息验签
check($arr)
$arr：收到的支付宝返回信息数组

9、打印日志
writeLog($text)
$text：要打印的字符串