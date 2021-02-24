<?php
namespace addons\MiniManage\model;
use addons\MiniManage\MiniManage;
use app\common\model\BillAftersalesItems;
use app\common\model\BillReship;
use app\common\model\OrderItems;
use org\Curl;
use think\facade\Cache;
use think\Model;


class AdminMessage extends Model
{
    const SEND_TRUE = 1;        //发送
    const SEND_FALSE= 2;        //不发送

    /**
     * 商户发送消息给用户
     * @param $user_id              接受者id
     * @param $code                 模板编码
     * @param $params               参数
     */
    public function sendMessage($user_id,$code,$params)
    {
        $info = $this->getTpl($code);
        if($code == "after_ship"){
            $Model = new BillReship();
            $params = $Model->where("reship_id","eq",$params)->find();
        }
        //站内消息
        if($info['message'] == self::SEND_TRUE  && $info['overall'] == self::SEND_TRUE ){
            $this->send($user_id,$code,$params);
        }
        if($info['wx_tpl_message'] == self::SEND_TRUE && $info['overall'] == self::SEND_TRUE){
            $this->sendMessageGg($code,$params);
        }
        return [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];

    }
    public function getTpl($code){
        $Model = new AdminMessageCenter();
        $info = $Model->where("code","eq",$code)->find();
        if(!$info){
            $info['message'] = 1;
            $info['wx_tpl_message'] = 2;
        }
        return $info;
    }
    public function send($user_id,$code,$params)
    {
        $content = $this->temp($code,$params);

        if($content == ''){
            return error_code(10009);
        }
        $data['user_id'] = $user_id;
        $data['code'] = $code;
        $data['params'] = json_encode($params);
        $data['content'] = $content;
        $data['ctime'] = time();
        $data['utime'] = time();
        $this->save($data);
        return [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
    }

    public function temp($code,$params){
        $msg = '';
        switch ($code)
        {
            case 'create_order':                 //下单成功
                $msg = "订单创建成功。";
                break;
            case 'order_payed':
                $msg = "订单支付成功";
                break;
            case 'user_register':
                $msg = "用户注册成功";
                break;
            case 'after_order':
                $msg = "售后申请";
                break;
            case 'after_ship':
                $msg = "售后已发货";
                break;
        }
        return $msg;
    }
    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();
        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['id']) && $post['id'] != ""){
            $where[] = ['id', 'eq', $post['id']];
        }

        if(isset($post['user_id']) && $post['user_id'] != ""){
            $where[] = ['user_id', 'eq', $post['user_id']];
        }

        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }
        if(isset($post['code']) && $post['code'] != ""){
            $where[] = ['code', 'eq', $post['code']];
        }

        if(input('?param.date')){
            $theDate = explode(' 到 ',input('param.date'));
            if(count($theDate) == 2){
                $where[] = ['ctime', '<', strtotime($theDate[1])];
                $where[] = ['ctime', '>', strtotime($theDate[0])];
            }
        }


        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "ctime desc";
        return $result;
    }
    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v) {
//            if($v['status']) {
//                $list[$k]['status'] = config('params.message')['status'][$v['status']];
//            }
            if($v['content']) {
                $list[$k]['content'] = $v['content'];
            }
            if($v['params']){
                $list[$k]['params'] = json_decode($v['params']);
            }

            if($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if($v['user_id']) {
                $list[$k]['user_mobile'] = get_user_info($v["user_id"]);
            }

        }
        return $list;
    }



    /**
     * 公众号推送消息
     * @param $order_id
     * @return array
     */
    public function sendMessageGg($code,$params)
    {
        if($code == "user_register"){
            $data = $params;
        }elseif($code == 'create_order'){
            $data = $this->assembleData2($params);
        }elseif($code == 'order_payed'){
            $data = $this->assembleData3($params);
        }elseif ($code == 'after_order'){
            $data = $this->assembleData4($params);
        }elseif ($code == "after_ship"){
            $data = $params;
        }
        if ($data) {
             $this->sendMess($code,$data);
        }
        return [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
    }

    /**
     * 公众号推送消息组装数据
     * @param $order_id
     * @return array
     */
    public function assembleData2($params)
    {
        $itemModel = new OrderItems();
        $list= $itemModel->where("order_id","eq",$params["order_id"])->select();
        $name ='';
        foreach ($list as $k=>$v){
            $name .= $v["name"];
        }
        $params["goodsname"] = $name;
        $newdata = json_encode($params, 320);
        return $newdata;
    }
    /**
     * 公众号推送消息组装数据
     * @param $order_id
     * @return array
     */
    public function assembleData3($params)
    {
        $itemModel = new OrderItems();
        $list= $itemModel->where("order_id","eq",$params["order_id"])->select();
        $name ='';
        foreach ($list as $k=>$v){
            $name .= $v["name"];
        }
        $params["goodsname"] = $name;
        $newdata = json_encode($params, 320);
        return $newdata;
    }
    public function assembleData4($params)
    {
        $itemModel = new BillAftersalesItems();
        $list= $itemModel->field("name")->where("aftersales_id","eq",$params["aftersales_id"])->select();
        $name ='';
        foreach ($list as $k=>$v){
            $name .= $v["name"];
        }
        $params["name"] = $name;
        $newdata = json_encode($params, 320);
        return $newdata;
    }

    /**
     * 发公众号信息
     * @return array
     */
    public function sendMess($code,$params)
    {
        $str = request()->domain();
        $url = "https://jihainet.com/b2c/Callback/sendMessage";
        $data["keys"] =$str;
        $data["sign"] = strtotime(date('Y-m-d',time()));
        $data["code"] = $code;
        $data["params"] = $params;
        $curl = new Curl();
        $res = $curl->post($url,$data);
        return json_decode($res,true);
    }
}