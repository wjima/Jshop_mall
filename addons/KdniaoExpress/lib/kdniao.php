<?php
namespace addons\KdniaoExpress\lib;

use addons\KdniaoExpress\model\FaceSheet;
use app\common\model\BillDelivery;
use org\Curl;

class kdniao
{

    private $apiKey = '';//加密私钥，快递鸟提供
    private $ebusinessid = ''; //电商ID
    const DEVREQURL = 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json'; //测试地址
    const PROREQURL = 'http://api.kdniao.com/api/EOrderService'; //正式地址
    const IPSERVICEURL = 'http://www.kdniao.com/External/GetIp.aspx';//获取ip地址
    const PRINTURL = 'http://www.kdniao.com/External/PrintOrder.aspx';//打印订单地址
    const DEVSEARCHURL = 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json';//即时查询测试地址
    const PROSEARCHURL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';//即时查询生产URL地址
    const DEBUG = false;//是否调试
    const ISPRIVIEW = 0;//是否预览，0-不预览 1-预览
    private $isNotice = 1;//是否通知快递员上门揽件 0-通知 1-	不通知 不填则默认为1

    function __construct($ebusinessid, $apiKey)
    {
        $this->apiKey      = $apiKey;
        $this->ebusinessid = $ebusinessid;

    }

    /**
     * 设置是否通知快递
     * @param int $isNotice
     */
    public function setNotice($isNotice = 1)
    {
        $this->isNotice = $isNotice;
    }


    /**
     * 获取电子面单
     */
    public function getKdApiEOrder($order)
    {
        $return                = [
            'msg'    => '',
            'status' => false,
            'data'   => [],
        ];
        $eorder                = [];
        $eorder["ShipperCode"] = $order['logi_code'];//快递公司编码
        if (isset($order['logi_no']) && $order['logi_no']) {
            $eorder['LogisticCode'] = $order['logi_no'];
        }
        $eorder["OrderCode"]             = $order['order_id'];
        $eorder["PayType"]               = 1;//现付
        $eorder["ExpType"]               = 1;//标准快件
        $eorder["IsNotice"]              = $this->isNotice;//是否通知快递员上门揽件 0-通知 1-	不通知 不填则默认为1
        $eorder["IsReturnPrintTemplate"] = '1';
        // 发货人信息
        $sender           = [];
        $sender["Name"]   = getSetting('reship_name');
        $sender["Mobile"] = getSetting('reship_mobile');
        $senderAreaId     = getSetting('reship_area_id') ? getSetting('reship_area_id') : [];
        if (!$senderAreaId) {
            $return['msg'] = '请先配置退货信息';
            return $return;
        }
        $areainfo = get_area($senderAreaId);
        list($province, $city, $area) = explode(' ', $areainfo);
        $sender["ProvinceName"] = $province;
        $sender["CityName"]     = $city;
        $sender["ExpAreaName"]  = $area;
        $sender["Address"]      = getSetting('reship_address');
        //收货人信息
        list($province, $city, $area) = explode(' ', $order['ship_area_name']);
        $receiver                 = [];
        $receiver["Name"]         = $order['ship_name'];
        $receiver["Mobile"]       = $order['ship_mobile'];
        $receiver["ProvinceName"] = $province;
        $receiver["CityName"]     = $city;
        $receiver["ExpAreaName"]  = $area;
        $receiver["Address"]      = $order['ship_address'];

        //取发货明细
        $commodity = [];
        if (isset($order['items'])) {
            $items = $order['items'];
            foreach ($items->toArray() as $key => $item) {
                $commodityOne              = [];
                $commodityOne["GoodsName"] = $item['name'];
                if ($item['addon']) {
                    $commodityOne["GoodsName"] = $commodityOne["GoodsName"] . '-' . $item['addon'];
                }
                $commodityOne["GoodsCode"]     = $item['bn'];
                $commodityOne["Goodsquantity"] = $item['nums'];
                $commodityOne["GoodsWeight"]   = number_format($item['weight'] / 1000, 3);//保留3位小数
                $commodity[]                   = $commodityOne;
            }
        }

        $eorder["Sender"]    = $sender;
        $eorder["Receiver"]  = $receiver;
        $eorder["Commodity"] = $commodity;

        $faceSheetModel = new FaceSheet();
        $faceSheet = $faceSheetModel->where(['logi_code'=>$order['logi_code']])->find();
        if(!$faceSheet){
            $return['msg'] = '请配置电子面单！';
            return $return;
        }

        $eorder["CustomerName"] = $faceSheet['customer_name'];//快递公司账号
        $eorder["CustomerPwd"]  = $faceSheet['customer_pwd'];//快递公司密码
        $eorder["SendSite"]     = $faceSheet['send_site'];//快递公司网点地址
        $eorder['TemplateSize'] = $faceSheet['template_size'];//快递单模板

        $jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);

        $jsonResult = $this->submitEOrder($jsonParam);
        $result     = json_decode($jsonResult, true);
        if (!$result['Success']) {
            $return['msg'] = $result['Reason'];
            return $return;
        }
        $return['data']['order']         = $result['Order'];
        $return['data']['printTemplate'] = $result['PrintTemplate'];
        $return['msg']                   = '获取成功';
        $return['status']                = true;

        return $return;
    }

    /**
     * Json方式 调用电子面单接口
     */
    private function submitEOrder($requestData)
    {
        $datas             = array(
            'EBusinessID' => $this->ebusinessid,
            'RequestType' => '1007', //电子面单
            'RequestData' => urlencode($requestData),
            'DataType'    => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->apiKey);
        $curl              = new Curl();
        if (self::DEBUG) {
            $result = $curl->post(self::DEVREQURL, $datas);
        } else {
            $result = $curl->post(self::PROREQURL, $datas);
        }
        return $result;
    }

    /**
     * 批量打印
     * 组装POST表单用于调用快递鸟批量打印接口页面
     * @param $data
     * @param string $PortName
     */
    public function build_form($data, $PortName = '', $is_priview = 1)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $request_data[] = [
                    'OrderCode' => $value['order_id'],
                    'PortName'  => $PortName,
                ];
            }
        }
        //OrderCode:需要打印的订单号，和调用快递鸟电子面单的订单号一致，PortName：本地打印机名称，请参考使用手册设置打印机名称。支持多打印机同时打印。
        $request_data = json_encode($request_data, JSON_UNESCAPED_UNICODE);
        $data_sign    = $this->encrypt($this->get_ip() . $request_data, $this->apiKey);
        //组装表单
        $form = '<form id="form1" method="POST" action="' . self::PRINTURL . '"><input type="text" name="RequestData" value=\'' . $request_data . '\'/><input type="text" name="EBusinessID" value="' . $this->ebusinessid . '"/><input type="text" name="DataSign" value="' . $data_sign . '"/><input type="text" name="IsPreview" value="' . $is_priview . '"/><script>form1.submit();</script></form>';
        print_r($form);
        exit();
    }


    /**
     * 判断是否为内网IP
     * @param ip IP
     * @return 是否内网IP
     */
    private function is_private_ip($ip)
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * 获取客户端IP(非用户服务器IP)
     * @return 客户端IP
     */
    private function get_ip()
    {
        /* return '223.88.55.182';*/ //本地请填写自己的外网IP
        //获取客户端IP
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (!$ip || $this->is_private_ip($ip)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::IPSERVICEURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            return $output;
        } else {
            return $ip;
        }
    }


    /**
     * 电商Sign签名生成
     * @param $data 内容
     * @param $apiKey apiKey
     * @return string DataSign签名
     */
    private function encrypt($data, $apiKey)
    {
        return urlencode(base64_encode(md5($data . $apiKey)));
    }

    /**
     * Json方式 查询订单物流轨迹
     */
    public function getOrderTracesByJson($shipperCode = '', $logisticCode = '')
    {

        $return      = [
            'message' => '',
            'status'  => false,
            'data'    => [],
        ];
        $requestData = [
            'OrderCode'    => '',
            'ShipperCode'  => trim($shipperCode),
            'logisticCode' => trim($logisticCode),
        ];
        if ($requestData['ShipperCode'] == 'SF') {
            //查询订单收货人手机号后四位
            $billDeliveryModel = new BillDelivery();
            $deliveryInfo      = $billDeliveryModel->where([['logi_no', '=', $requestData['logisticCode']], ['logi_code', '=', 'SF']])->field('ship_mobile')->find();
            if ($deliveryInfo && isset($deliveryInfo['ship_mobile'])) {
                $requestData['CustomerName'] = substr($deliveryInfo['ship_mobile'], -4);
            }
        }
        $requestData = json_encode($requestData, JSON_UNESCAPED_UNICODE);

        $requesttype = getAddonsConfigVal('KdniaoExpress', 'requesttype');

        $datas = array(
            'EBusinessID' => $this->ebusinessid,
            'RequestType' => $requesttype ? $requesttype : '1002',//8001
            'RequestData' => urlencode($requestData),
            'DataType'    => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData, $this->apiKey);
        $curl              = new Curl();

        $result = $curl->post(self::PROSEARCHURL, $datas);
        $result = json_decode($result, true);
        //根据公司业务处理返回的信息......
        if (isset($result['Success']) && !$result['Success']) {
            $return['message'] = $result['Reason'];
            $return['status']  = '201';//错误
            return $return;
        } else {
            $traces           = [];
            $result['Traces'] = array_reverse($result['Traces']);
            foreach ((array)$result['Traces'] as $key => $value) {
                $traces[$key]['time']    = $value['AcceptTime'];
                $traces[$key]['context'] = $value['AcceptStation'];
            }
            $return['data']    = $traces;
            $return['message'] = '获取成功';
            $return['nu']      = $result['LogisticCode'];
            $return['state']   = $result['State'];
            $return['status']  = '200';
            return $return;
        }
    }

}