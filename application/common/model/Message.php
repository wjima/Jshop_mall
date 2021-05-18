<?php
namespace app\common\model;

/**
 * 站内信
 * Class Message
 * @package app\common\model
 * @author sin
 */
class Message extends Common
{
    const STATUS_UNREAD = 1;        //未读
    const STATUS_READED= 2;        //已读

//时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    /**
     * @param $user_id              接受者id
     * @param $code                 模板编码
     * @param $params               参数
     */
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
        $this->save($data);


        return [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
    }
    //消息查看，更新已读状态
    public function info($user_id,$id){
        $where['user_id'] = $user_id;
        $where['id'] = $id;
        $info = $this->where($where)->find();
        if($info){
            $info->status = self::STATUS_READED;
            $info->save();
            return $info;
        }else{
            return [];
        }
    }


    public function temp($code,$params){
        $msg = '';
        switch ($code)
        {
            case 'create_order':                 //下单成功
                $msg = "订单创建成功。";
                break;
            case 'order_payed':
                $msg = "恭喜您，订单支付成功,祝您购物愉快。";
                break;
            case 'order_cancle':
                $msg = "您的订单：".$params['order_id']."已取消";
                break;
            case 'remind_order_pay':
                $msg = "您的订单还有3个小时就要取消了，请立即进行支付。";
                break;
            case 'delivery_notice':
                $msg = "你好，你的订单已经发货。";
                break;
            case 'aftersales_pass':
                $msg = "你好，您的售后单".$params['aftersales_id']."平台已处理。";
                break;
            case 'refund_success':
                $msg = "用户你好，你的退款已经处理，请确认。";
                break;
            case 'aftersales_add':
                $msg = "你好，有新的售后订单了，请及时处理。";
                break;
            case 'seller_order_notice':
                $msg = "您有新的订单了，请及时处理。";
                break;
        }
        return $msg;
    }

    /**
     * 判断是否有新消息
     * @param $user_id      用户id
     */
    public function hasNew($user_id)
    {
        $where['user_id'] = $user_id;
        $where['status'] = self::STATUS_UNREAD;
        $re =  $this->tableData($where);
        if($re['data']->isempty()){
            return false;
        }else{
            return true;
        }
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
        $list = $this::with('userInfo')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        // $re['sql'] = $this->getLastSql();

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
        $result['order'] = "status asc,ctime desc";
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

            if($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }

        }
        return $list;
    }


    public function userInfo()
    {
        return $this->hasOne('User','id','user_id')->bind([
            'user_mobile' => 'mobile',
        ]);
    }

}