<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: sin<sin@jihainet.com>
// +----------------------------------------------------------------------

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillPayments as BillPaymentsModel;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\Payments;
use app\common\model\BillRefund as BillRefundModel;
use app\common\model\OrderItems;
use app\common\model\T;
use app\common\model\UserTocash;
use app\common\model\User;
use Request;

class Report extends Manage
{


    //具体参数意思参见https://www.echartsjs.com/option.html
    private $option = [
        'title' => [
            'text' => '报表'
        ],
        'tooltip' => [
            'trigger' => 'axis'
        ],
        'legend' => [
            'data' => []
        ],
        'grid' => [
            'left' => '3%',
            'right' => '4%',
            'bottom' => '3%',
            'containLabel' => true
        ],
        'toolbox' => [
            'feature' => [
                'saveAsImage' => []
            ]
        ],
        'xAxis'=> [
            'type'=> 'category',
            'boundaryGap' => false,
            'data' => [],
        ],
        'yAxis' => [
            'type' => 'value',
            'name' => '元'
        ],
        'series' => []
    ];

    public function index()
    {
        return $this->fetch('index');
    }

    public function order(){
        if(Request::isAJAX()){
            $date_re = $this->getDate();
            if(!$date_re['status']){
                return $date_re;
            }
            $this->option['title']['text'] = "订单统计";
            $legend = [
                'order_all' => '全部',
                'order_nopay' => '待付款',
                'order_payed' => '已付款',
               // 'order_ship' => '发货',
               // 'order_refund' =>'售后'
            ];
            $this->option['legend']['data'] = array_values($legend);
            $xData = $this->getXdata($date_re['data']);
            if(!$xData['status']){
                return $xData;
            }
            $this->option['xAxis']['data'] = $xData['data'];
            $datas = [];        //保存所有数据

            foreach($legend as $k => $v){
                switch($k){
                    case 'order_all':
                        $where = "
                            and ctime >" .$date_re['data']['start'].  "
                            and ctime <" .$date_re['data']['end'].  "
                        ";
                        $data = $this->orderMark($date_re['data'],$where,'ctime');
                        break;
                    case 'order_nopay':
                        $where = "
                            and ctime >" .$date_re['data']['start'].  "
                            and ctime <" .$date_re['data']['end'].  "
                            and pay_status = 1
                        ";
                        $data = $this->orderMark($date_re['data'],$where,'ctime');
                        break;
                    case 'order_payed':
                        $where = "
                            and payment_time >" .$date_re['data']['start'].  "
                            and payment_time <" .$date_re['data']['end'].  "
                            and pay_status > 1
                        ";
                        $data = $this->orderMark($date_re['data'],$where,'payment_time');
                        break;
                    case 'order_refund':

                        break;
                }

                if($data['status']){
                    $this->option['series'][] = [
                        'name' => $v,
                        'type' => 'line',
                        'data' => array_column($data['data'],'val')
                    ];
                    //存所有数据
                    $datas[$k] = $data['data']; 
                }else{
                    $this->option['series'][] = [
                        'name' => $v,
                        'type' => 'line',
                        'data' => []
                    ];
                }
            }
            //组装数据列表用于table里使用
            $table = [];
            for($i = 0;$i< $date_re['data']['num'];$i++){
                $item = [];
                $item['x'] = $xData['data'][$i];
                foreach($datas as $k => $v){
                    $item[$k.'_val'] = $v[$i]['val'];
                    $item[$k.'_num'] = $v[$i]['num'];
                }
                $table[] = $item;
            }

            return [
                'status' => true,
                'data'   => [
                    'option' => $this->option,
                    'table' => $table
                ],
                'msg'    => '',
            ];
        }else{
            return $this->fetch();
        }
    }

    //收款单。
    public function payments(){
        if(Request::isAJAX()){
            $date_re = $this->getDate();
            if(!$date_re['status']){
                return $date_re;
            }
            $this->option['title']['text'] = "财务统计";
            $legend = [
                'payments_all' => '收款单',
                'payments_order' => '订单收款',
                'payments_order_refund' => '订单退款',
                'payments_recharge' => '充值',
                'payments_tocash' =>'提现'
            ];
            $this->option['legend']['data'] = array_values($legend);
            $xData = $this->getXdata($date_re['data']);
            if(!$xData['status']){
                return $xData;
            }
            $this->option['xAxis']['data'] = $xData['data'];
            $datas = [];        //保存所有数据

            foreach($legend as $k => $v){
                switch($k){
                    case 'payments_all':
                        $where = "
                            and utime >" .$date_re['data']['start'].  "
                            and utime <" .$date_re['data']['end'].  "
                            and status = 2
                        ";
                        $data = $this->paymentsMark($date_re['data'],$where,'utime');
                        break;
                    case 'payments_order':
                        $where = "
                            and utime >" .$date_re['data']['start'].  "
                            and utime <" .$date_re['data']['end'].  "
                            and status = 2
                            and type = 1
                        ";
                        $data = $this->paymentsMark($date_re['data'],$where,'utime');
                        break;
                    case 'payments_order_refund':
                        $where = "
                            and utime >" .$date_re['data']['start'].  "
                            and utime <" .$date_re['data']['end'].  "
                            and status = 2
                            and type = 1
                        ";
                        $data = $this->refundMark($date_re['data'],$where,'utime');
                        break;
                    case 'payments_recharge':
                        $where = "
                            and utime >" .$date_re['data']['start'].  "
                            and utime <" .$date_re['data']['end'].  "
                            and status = 2
                            and type = 2
                        ";
                        $data = $this->paymentsMark($date_re['data'],$where,'utime');
                        break;
                    case 'payments_tocash':
                        $where = "
                            and utime >" .$date_re['data']['start'].  "
                            and utime <" .$date_re['data']['end'].  "
                            and type = 2
                        ";
                        $data = $this->tocashMark($date_re['data'],$where,'utime');
                        break;

                }

                if($data['status']){
                    $this->option['series'][] = [
                        'name' => $v,
                        'type' => 'line',
                        'data' => array_column($data['data'],'val')
                    ];
                    //存所有数据
                    $datas[$k] = $data['data']; 
                }else{
                    $this->option['series'][] = [
                        'name' => $v,
                        'type' => 'line',
                        'data' => []
                    ];
                }
            }
            //组装数据列表用于table里使用
            $table = [];
            for($i = 0;$i< $date_re['data']['num'];$i++){
                $item = [];
                $item['x'] = $xData['data'][$i];
                foreach($datas as $k => $v){
                    $item[$k.'_val'] = $v[$i]['val'];
                    $item[$k.'_num'] = $v[$i]['num'];
                }
                $table[] = $item;
            }

            return [
                'status' => true,
                'data'   => [
                    'option' => $this->option,
                    'table' => $table
                ],
                'msg'    => '',
            ];
        }else{
            return $this->fetch();
        }
    }

    public function goods(){
        if(Request::isAJAX()){
            $result = [
                'code' => 0,
                'msg' => '',
                'count' => 0,
                'data' => []
            ];
            $page = input('param.page',1);
            $limit = input('param.limit',5000);
            $sort = input('param.thesort','desc');
            $filter = input('param.filter','nums');
            $filter_sed = $filter=='nums'?'rnums':'nums';

            if(input('?param.date')){
                $theDate = explode(' 到 ',input('param.date'));
                if(count($theDate) == 2){
                    $start = strtotime($theDate[0]);
                    $end = strtotime($theDate[1])+60*60*24;
                }else{
                    return $result;
                }
            }else{
                //默认今天
                $start = strtotime(date('Y-m-d'));
                $end = $start+60*60*24;
            }

            $sql = "
                from 
                (   (
                        select oi.nums,oi.amount,oi.sn,oi.name,oi.addon,0 as rnums,1 as type,oi.id
                        from ".config('database.prefix')."order_items oi
                        left join `".config('database.prefix')."order` o on oi.order_id = o.order_id
                        where o.pay_status <> 1
                        and o.payment_time > ".$start."
                        and o.payment_time <= ".$end." 
                    )
                    union
                    (
                        select 0 as nums,0 as amount,bai.sn,bai.name,bai.addon,bai.nums as rnums,2 as type,bai.id
                        from ".config('database.prefix')."bill_aftersales_items bai
                        left join `".config('database.prefix')."bill_aftersales` ba on bai.aftersales_id = ba.aftersales_id
                        where ba.status = 2
                        and ba.utime > ".$start."
                        and ba.utime <= ".$end."
                    )
                ) t
                group by t.sn
                order by sum(t.".$filter.") ".$sort.",sum(t.".$filter_sed.") ".$sort."
            ";

            // $sql = "
            //     from ".config('database.prefix')."order_items oi
            //     left join `".config('database.prefix')."order` o on oi.order_id = o.order_id
            //     where o.pay_status <> 1
            //     and o.payment_time > ".$start."
            //     and o.payment_time <= ".$end."
            //     group by oi.sn
            //     order by sum(oi.".$filter.") ".$sort.",sum(oi.".$filter_sed.") ".$sort."
            // ";
            $orderItemModel = new OrderItems();
            $count = $orderItemModel->query("select 1 ".$sql); 
            if(!$count){
               return $result;
            }
            $result['count'] = count($count);

            $select = "select sum(nums) as nums,sum(amount) as amount,sn,name,addon,sum(rnums) as rnums ";
            $limit_sql = " limit ".($page-1)*$limit.",".$limit;
            $result['data'] = $orderItemModel->query($select.$sql.$limit_sql); 
            if($sort == "desc"){
                $row = ($page-1)*$limit;
            }else{
                $row = $result['count'] - ($page-1)*$limit + 1;
            }
            foreach($result['data'] as $k => $v){
                if($sort == "desc"){
                    $v['id'] = ++$row;
                }else{
                    $v['id'] = --$row;
                }

                if($limit == 5000){
                    $v['addon'] = "\"".$v['addon']."\"";    //如果是导出的话，这里会含有英文逗号，所以要加上双引号
                }

                $result['data'][$k] = $v; 
            }
            $result['start'] = $start;
            $result['end'] = $end;
            $result['sql'] = $select.$sql.$limit_sql;
            return $result;


        }else{
            return $this->fetch();
        }
    }

    //商品收藏统计
    public function goodsCollection(){
        if(Request::isAJAX()){
            $result = [
                'code' => 0,
                'msg' => '',
                'count' => 0,
                'data' => []
            ];
            $page = input('param.page',1);
            $limit = input('param.limit',5000);
            $sort = input('param.thesort','desc');

            if(input('?param.date')){
                $theDate = explode(' 到 ',input('param.date'));
                if(count($theDate) == 2){
                    $start = strtotime($theDate[0]);
                    $end = strtotime($theDate[1])+60*60*24;
                }else{
                    return $result;
                }
            }else{
                //默认今天
                $start = strtotime(date('Y-m-d'));
                $end = $start+60*60*24;
            }
            $sql = "
                from ".config('database.prefix')."goods_collection gc
                left join `".config('database.prefix')."goods` g on gc.goods_id = g.id
                where gc.ctime > ".$start."
                and gc.ctime <= ".$end."
                group by gc.goods_id
                order by sum(gc.goods_id) ".$sort."
            ";
            $goodsModel = new Goods();
            $count = $goodsModel->query("select 1 ".$sql);
            if(!$count){
                return $result;
            }
            $result['count'] = count($count);

            $select = "select count(gc.goods_id) as nums,gc.goods_id,gc.goods_name,g.image_id";
            $limit_sql = " limit ".($page-1)*$limit.",".$limit;
            $result['data'] = $goodsModel->query($select.$sql.$limit_sql);
            if($sort == "desc"){
                $row = ($page-1)*$limit;
            }else{
                $row = $result['count'] - ($page-1)*$limit + 1;
            }
            foreach($result['data'] as $k => $v){
                if($sort == "desc"){
                    $v['id'] = ++$row;
                }else{
                    $v['id'] = --$row;
                }


                $v['image_url'] = _sImage($v['image_id']);
                $result['data'][$k] = $v;
            }
            $result['start'] = $start;
            $result['end'] = $end;
            $result['sql'] = $select.$sql.$limit_sql;
            return $result;


        }else{
            return $this->fetch();
        }
    }


    //根据时间，返回时间段
    private function getDate(){
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        if(input('?param.date')){
            $theDate = explode(' 到 ',input('param.date'));
            if(count($theDate) == 2){
                $data['start'] = strtotime($theDate[0]);
                $data['end'] = strtotime($theDate[1])+60*60*24;
                $data['section'] = 1;
            }else{
                $result['msg'] = error_code(10076,true);
                return $result;
            }
        }else{
            return error_code(10000);
        }
        //切片维度，1是小时，2是天，3是周，4是月，5是季度，6是半年，7是年
        if(input('?param.section')){
            $data['section'] = input('param.section');
        }
        //算统计需要的参数
        return $this->getTmp($data);

    }

    //换算前台时间按钮
    public function getDateType(){
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '123',
        ];
        if(input('?param.date_type')){

            switch (input('param.date_type')){
                case 1:             //当天
                    $result['data']['start'] = date('Y-m-d');
                    $result['data']['end'] = $result['data']['start'];
                    break;
                case 2:             //昨天
                    $result['data']['start'] = date("Y-m-d",strtotime("-1 day")) ;//strtotime(date('Y-m-d'),strtotime('-1 day'));
                    $result['data']['end'] = $result['data']['start'];
                    break;
                case 3:              //本周
                    $week_num = (date('w') != 0 ? date('w'):7);
                    $week_num--;
                    $result['data']['start'] = date('Y-m-d',time() - $week_num * 60*60*24);
                    $result['data']['end'] = date('Y-m-d');
                    break;
                case 4:              //上周
                    $week_num = (date('w') != 0 ? date('w'):7);
                    $week_num = $week_num + 6;
                    $result['data']['start'] = date('Y-m-d',time() - $week_num * 60*60*24);
                    $result['data']['end'] = date('Y-m-d',time() - ($week_num - 6) * 60*60*24);
                    break;
                case 5:              //本月
                    $result['data']['start'] = date("Y-m-01");
                    $result['data']['end'] = date('Y-m-d');
                    break;
                case 6:              //上月
                    $result['data']['start'] = date("Y-m-01",strtotime('-1 month'));
                    $result['data']['end'] = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day'));
                    break;
                case 7:              //最近7天
                    $result['data']['start'] = date('Y-m-d',time() - 6 * 60*60*24);
                    $result['data']['end'] = date('Y-m-d');
                    break;
                case 8:              //最近一月
                    $result['data']['start'] = date("Y-m-d",strtotime('-1 month +1 day'));
                    $result['data']['end'] = date('Y-m-d');
                    break;
                case 9:              //最近3月
                    $result['data']['start'] = date("Y-m-d",strtotime('-3 month +1 day'));
                    $result['data']['end'] = date('Y-m-d');
                    break;
                case 10:              //最近6年
                    $result['data']['start'] = date("Y-m-d",strtotime('-6 month +1 day'));
                    $result['data']['end'] = date('Y-m-d');
                    break;
                case 11:              //最近1年
                    $result['data']['start'] = date("Y-m-d",strtotime('-12 month +1 day'));
                    $result['data']['end'] = date('Y-m-d');
                    break;

                default:
                    return error_code(10077);
            }
            return $result;
        }else{
            return error_code(10000);
        }
    }


    //订单报表
    private function orderMark($date_arr,$where,$join_val){
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];

        $sql = "
            select tmp_x.x,ifnull(sum(o.order_amount),0) as val,count(o.order_id) as num
            from
              (SELECT @xi:=@xi+1 as x from
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x1,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x2,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x3,
                (SELECT @xi:=-1) x0 limit 0,".$date_arr['num'].") tmp_x
            left join (
                select * from `".config('database.prefix')."order`
                where 1=1
                ".$where."
            ) o on tmp_x.x = (( cast(o.".$join_val." as signed) - ".$date_arr['start'].") div (".$date_arr['section']."))
            group by tmp_x.x
        ";

        $model = new Order();
        $re = $model->query($sql);

        $result['data'] = $re;
        $result['sql'] = $sql;
        $result['status'] = true;
        return $result;
    }

    //支付单报表
    private function paymentsMark($date_arr,$where,$join_val){
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];

        $sql = "
            select tmp_x.x,ifnull(sum(o.money),0) as val,count(o.payment_id) as num
            from
              (SELECT @xi:=@xi+1 as x from
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x1,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x2,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x3,
                (SELECT @xi:=-1) x0 limit 0,".$date_arr['num'].") tmp_x
            left join (
                select * from `".config('database.prefix')."bill_payments`
                where 1=1
                ".$where."
            ) o on tmp_x.x = (( cast(o.".$join_val." as signed)  - ".$date_arr['start'].") div (".$date_arr['section']."))
            group by tmp_x.x
        ";
        $model = new Order();
        $re = $model->query($sql);
        $result['data'] = $re;
        $result['sql'] = $sql;
        $result['status'] = true;
        return $result;
    }
    //退款单报表
    private function refundMark($date_arr,$where,$join_val){
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];

        $sql = "
            select tmp_x.x,ifnull(sum(o.money),0) as val,count(o.refund_id) as num
            from
              (SELECT @xi:=@xi+1 as x from
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x1,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x2,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x3,
                (SELECT @xi:=-1) x0 limit 0,".$date_arr['num'].") tmp_x
            left join (
                select * from `".config('database.prefix')."bill_refund`
                where 1=1
                ".$where."
            ) o on tmp_x.x = (( cast(o.".$join_val." as signed) - ".$date_arr['start'].") div (".$date_arr['section']."))
            group by tmp_x.x
        ";
        $model = new Order();
        $re = $model->query($sql);
        $result['data'] = $re;
        $result['sql'] = $sql;
        $result['status'] = true;
        return $result;
    }

    //用户提现报表
    private function tocashMark($date_arr,$where,$join_val){
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];

        $sql = "
            select tmp_x.x,ifnull(sum(o.money),0) as val,count(o.id) as num
            from
              (SELECT @xi:=@xi+1 as x from
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x1,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x2,
                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) x3,
                (SELECT @xi:=-1) x0 limit 0,".$date_arr['num'].") tmp_x
            left join (
                select * from `".config('database.prefix')."user_tocash`
                where 1=1
                ".$where."
            ) o on tmp_x.x = ((cast(o.".$join_val." as signed) - ".$date_arr['start'].") div (".$date_arr['section']."))
            group by tmp_x.x
        ";
        $model = new Order();
        $re = $model->query($sql);
        $result['data'] = $re;
        $result['sql'] = $sql;
        $result['status'] = true;
        return $result;
    }

    //根据时间节点和时间粒度算x轴个数
    private function getTmp($date_arr){
        $result = [
            'status' => false,
            'data'   => $date_arr,
            'msg'    => '',
        ];
        if($date_arr['end'] <= $date_arr['start']){
            $result['msg'] = error_code(10078,true);
            return $result;
        }

        $diff_day = $date_arr['end'] - $date_arr['start'];
        switch($date_arr['section']){
            case 1:                 //小时
                $result['data']['section'] = 60*60;
                break;
            case 2:                 //天
                $result['data']['section'] = 60*60*24;
                break;
            default:
                $result['msg'] = error_code(10079,true);
                return $result;
        }
        //算x轴数据个数
        $result['data']['num'] = ceil($diff_day / $result['data']['section']);

        $result['status'] = true;
        return $result;

    }
    private function getXdata($date_arr){
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '',
        ];


        //校验,x轴最多1000个
        $x_nums = ceil(($date_arr['end'] - $date_arr['start']) / $date_arr['section']);
        if($x_nums > 1000){
            return error_code(13226);
        }



        $x_type = "";
        switch($date_arr['section']){
            case 3600:                 //小时
                if($x_nums<= 24) {
                    $x_type = "H时";
                }elseif($x_nums <= 720){
                    $x_type = "d日H时";
                }else{
                    $x_type = "m月d日H时";
                }
                break;
            case 86400:                 //天
                if($x_nums <= 31){
                    $x_type = "d号";
                }elseif($x_nums <= 365){
                    $x_type = "m.d";
                }else{
                    $x_type = "Y.m.d";
                }
                break;
        }
        if($x_type == ""){
            return error_code(10000);
        }



        for($i = $date_arr['start'];$i<($date_arr['end']);$i=$i+$date_arr['section']){
            if($date_arr['section'] == 3600){
                $result['data'][] = date($x_type,$i+$date_arr['section']);      //小时是结尾的表示当前切片的时间
            }else{
                $result['data'][] = date($x_type,$i);       //天是开始表示当前切片的时间
            }

        }
        return $result;
    }



}
