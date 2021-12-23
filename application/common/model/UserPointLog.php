<?php
namespace app\common\model;

use think\Db;
use think\facade\Cache;

/**
 * 用户积分记录表
 * Class UserPointLog
 * @package app\common\model
 */
class UserPointLog extends Common
{
    const POINT_TYPE_SIGN = 1; //签到
    const POINT_TYPE_REBATE = 2; //购物返积分
    const POINT_TYPE_DISCOUNT = 3; //购物使用积分
    const POINT_TYPE_ADMIN_EDIT = 4; //后台编辑
    const POINT_TYPE_PRIZE = 5; //奖励积分
    const POINT_TYPE_EXCHANGE = 6; //积分兑换
    const SIGN_FIXED_POINT = 1; //签到固定积分
    const SIGN_RANDOM_POINT = 2; //签到随机积分

//    protected $point_type = [                 用config('params.user_point_log')['type'][$v['type']]这样的方式来取，这里就注释掉了。
//        self::POINT_TYPE_SIGN => '签到',
//        self::POINT_TYPE_REBATE => '购物奖励',
//        self::POINT_TYPE_DISCOUNT => '购物消费',
//        self::POINT_TYPE_ADMIN_EDIT => '后台编辑'
//    ];

    /**
     * 积分设置
     * @param $user_id
     * @param $num
     * @param int $type
     * @param string $remarks
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setPoint($user_id, $num, $type = self::POINT_TYPE_SIGN, $remarks = '')
    {
        if ($num != 0) {
            //获取积分账号信息
            $user_model = new User();
            $user_info  = $user_model->where(['id' => $user_id])->find();

            $new_point = $user_info['point'] + $num;
            //积分余额判断
            if ($new_point < 0) {
                return error_code(11604);
            }

            //插入记录
            $data = [
                'user_id' => $user_id,
                'type'    => $type,
                'num'     => $num,
                'balance' => $new_point,
                'remarks' => $remarks,
                'ctime'   => time()
            ];
            $this->insert($data);

            //插入主表
            $where[] = ['id', 'eq', $user_id];
            $user_info->where($where)
                ->setInc('point', $num);
        }

        $return['status'] = true;
        $return['msg']    = '积分更改成功';

        return $return;
    }


    /**
     * 签到
     * @param $user_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sign($user_id)
    {
        $return   = [
            'status' => false,
            'msg'    => '',
            'data'   => 0
        ];
        $lock_key = 'user_sign_' . $user_id;//防止高并发重复签到问题
        if (!Cache::has($lock_key)) {
            Cache::set($lock_key, '1', 3);

            //判断是否已经签到
            $res = $this->isSign($user_id);
            if ($res['status']) {
                return error_code(11602);
            }

            //获取店铺签到积分设置
            $sign_point_type = getSetting('sign_point_type'); //签到积分奖励类型

            //判断是固定积分计算还是随机积分计算
            if ($sign_point_type == self::SIGN_RANDOM_POINT) {
                //随机计算
                $point = $this->signRandomPointCalculation();
            } else {
                //固定计算
                $point = $this->signFixedPointCalculation($user_id);
            }
            $return['data'] = $point;

            //插入数据库
            $result        = $this->setPoint($user_id, $point, self::POINT_TYPE_SIGN, '积分签到，获得' . $point . '个积分');
            $return['msg'] = $result['msg'];
            if ($result['status']) {
                Cache::rm($lock_key);
                $return['status'] = true;
                $return['msg']    = '签到成功';
            }
        } else {
            $result['msg'] = '请勿重复签到';
        }
        return $return;
    }


    /**
     * 判断今天是否签到
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isSign($user_id)
    {
        $return = error_code(11603);

        $where[]    = ['user_id', 'eq', $user_id];
        $where[]    = ['type', 'eq', self::POINT_TYPE_SIGN];
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday   = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $where[]    = ['ctime', 'BETWEEN', [$beginToday, $endToday]];
        //$where[] = ['DATE_FORMAT(from_unixtime(ctime), "%Y-%m-%d")', 'eq', date('Y-m-d', time())];
        //兼容问题
        $day = $this->where($where)
            ->find();

        if ($day) {
            $return['status'] = true;
            $return['msg']    = '今天已经签到了';
        }

        return $return;
    }


    /**
     * 签到随机积分计算
     * @return float|int
     */
    protected function signRandomPointCalculation()
    {
        $sign_random_min = getSetting('sign_random_min'); //最小随机
        $sign_random_max = getSetting('sign_random_max'); //最大随机
        $point           = mt_rand($sign_random_min, $sign_random_max); //随机积分
        //$point = $this->signAppointDatePointCalculation( $point); //判断计算指定日期
        return $point;
    }


    /**
     * 签到指定积分计算
     * @param $user_id
     * @return array|float|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function signFixedPointCalculation($user_id)
    {
        $first_sign_point           = getSetting('first_sign_point'); //首次签到积分
        $continuity_sign_additional = getSetting('continuity_sign_additional'); //连续签到追加
        $sign_most_point            = getSetting('sign_most_point'); //签到最多积分

        //获取连续签到天数
        if ($continuity_sign_additional > 0) {
            $max_continuity_day = ceil(($sign_most_point - $first_sign_point) / $continuity_sign_additional); //最大连续签到天数
        } else {
            //连续追加0的话说明每天签到积分都一样多，那么最大连续签到天数就是1天
            $max_continuity_day = 1; //最大连续签到天数
        }

        $day = date('Y-m-d', strtotime('-' . $max_continuity_day . ' day'));

        //兼容问题
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $where[] = ['FROM_UNIXTIME(ctime)', '>=', $day];
        $res     = $this->field('DATE_FORMAT(FROM_UNIXTIME(ctime), "%Y-%m-%d") as day')
            ->where($where)
            ->group('DATE_FORMAT(FROM_UNIXTIME(ctime), "%Y-%m-%d")')
            ->select();

        $new_res = [];
        foreach ($res as $k => $v) {
            $new_res[] = $v['day'];
        }
        $int = 0; //连续签到天数
        for ($i = 1; $i <= $max_continuity_day; $i++) {
            $now = date('Y-m-d', strtotime('-' . $i . ' day'));
            if (in_array($now, $new_res)) {
                $int++;
            } else {
                break;
            }
        }

        //积分
        $point = $first_sign_point + $continuity_sign_additional * $int;
        $point = ($point > $sign_most_point) ? $sign_most_point : $point;

        //$point = $this->signAppointDatePointCalculation($point); //判断计算指定日期
        return $point;
    }


    /**
     * 指定日期签到积分计算
     * @param $old_point
     * @return float|int|mixed
     */
    protected function signAppointDatePointCalculation($old_point)
    {
        $sign_appoint_date_status = '';//getShopSetting( 'sign_appoint_date_status'); //指定日期
        $nowDate                  = date('Y-m-d', time());
        if ($sign_appoint_date_status) {
            //开启指定日期
            $sign_appoint_date = '';//getShopSetting('sign_appoint_date'); //特殊指定日期
            $sign_appoint_date = json_decode($sign_appoint_date, true);
            if (in_array($nowDate, $sign_appoint_date)) {
                //当前是指定日期
                $sign_appoint_data_type = '';//getShopSetting('sign_appoint_data_type'); //特殊指定日期奖励类型
                if ($sign_appoint_data_type == self::SIGN_APPOINT_DATE_RATE) {
                    //倍率
                    $sign_appoint_date_rate = 2;//getShopSetting('sign_appoint_date_rate'); //特殊指定日期倍数
                    $point                  = $old_point * $sign_appoint_date_rate;
                } else {
                    //追加
                    $sign_appoint_date_additional = 1;//getShopSetting( 'sign_appoint_date_additional'); //特殊指定日期追加数量
                    $point                        = $old_point + $sign_appoint_date_additional;
                }
            } else {
                //不是指定日期
                $point = $old_point;
            }
        } else {
            //没有开启指定日期
            $point = $old_point;
        }
        return $point;
    }


    /**
     * 获取积分记录
     * @param $user_id
     * @param bool $type
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pointLogList($user_id, $type = false, $page = 1, $limit = 20,$params = [])
    {
        $return = [
            'status' => false,
            'msg'    => error_code(10025,true),
            'data'   => [],
            'total'  => 0,
            'count'  => 0
        ];
        if ($type) {
            $where[] = ['type', 'eq', $type];
        }
        if($user_id){
            $where[] = ['user_id', 'eq', $user_id];
        }

        if($params){
            if(isset($params['mobile']) && !empty($params['mobile']) && !$user_id){
                $user_id_search = get_user_id($params['mobile']);
                if($user_id_search){
                    $where[] = ['user_id', 'eq', $user_id_search];
                }
            }
            if(isset($params['date']) && !empty($params['date'])){
                $date_string = $params['date'];
                $date_array = explode(' 到 ', urldecode($date_string));
                $sdate = strtotime($date_array[0] . ' 00:00:00');
                $edate = strtotime($date_array[1] . ' 23:59:59');
                $where[] = ['ctime', ['>=', $sdate], ['<=', $edate], 'and'];
            }

        }

        $res = $this->field('id, type, num, balance, remarks, ctime,user_id')
            ->where($where)
            ->order('ctime', 'desc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)->count();

        $return['data']  = $res;
        $return['count'] = $count;
        $return['total'] = ceil($count / $limit);
        if ($res) {
            $return['status'] = true;
            if (count($res) >= 1) {
                $return['msg'] = '积分记录获取成功';
                foreach ($return['data'] as &$v) {
                    $v['type']  = config('params.user_point_log')['type'][$v['type']];
                    $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                    $v['username'] = get_user_info($v['user_id'],'showname');
                }
            } else {
                $return['msg'] = '暂无积分记录';
            }
        }
        return $return;
    }


    /**
     * 订单完成送积分操作
     * @param $user_id
     * @param $money
     * @param $order_id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderComplete($user_id, $money, $order_id)
    {
        $point_switch = getSetting('point_switch');
        if($point_switch == 1){
            $orders_reward_proportion = getSetting('orders_reward_proportion');
            if ($orders_reward_proportion != 0) {
                $point = floor($money / $orders_reward_proportion);
                $this->setPoint($user_id, $point, self::POINT_TYPE_REBATE, '订单：' . $order_id . ' 积分奖励');
            }
        }
    }


    /**
     * 返回layui的table所需要的格式
     * @param $post
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        // $re['sql']   = $this->getLastSql();

        return $re;
    }


    /**
     * @param $post
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['mobile']) && $post['mobile'] != "") {
            if ($user_id = get_user_id($post['mobile'])) {
                $where[] = ['user_id', 'eq', $user_id];
            } else {
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'ctime desc';
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list //array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['type']) {
                $list[$k]['type'] = config('params.user_point_log')['type'][$v['type']];
            }
            if ($v['user_id']) {
                $list[$k]['user_id'] = get_user_info($v['user_id']);
            }

            if ($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
        }
        return $list;
    }


    /**
     * 获取签到信息
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSignInfo($user_id, $time = 0)
    {
        $return = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [
                'isSign'     => true, //今日是否已经签到
                'asi'        => [], //签到的日期
                'total'      => 0, //累计签到
                'continuous' => 0, //连续签到
                'next'       => 0, //下次签到奖励积分
                'rule'       => [], //签到规则
                'omission'       => 0, //漏签天数
                'signday'       => [], //签到日期
                'point'       => 0 //积分
            ]
        ];

        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $date    = $this->field('DATE_FORMAT(FROM_UNIXTIME(ctime),"%Y-%m-%d") as `date`')
            ->where($where)
            ->group('DATE_FORMAT(FROM_UNIXTIME(ctime), "%Y-%m-%d")')
            ->select();

        $asi    = [];
        $total  = 0;
        $isSign = false;
        if ($date !== false) {
            foreach ($date as $k => $v) {
                $_date = explode("-", $v['date']);
                array_push($asi, $_date);
                $total++;
                if ($v['date'] == date('Y-m-d', time())) {
                    $isSign = true;
                }
            }
        }
        $fasi       = array_reverse($asi);
//        $continuous = $this->continuousSignCalculation($fasi);
        if(!$time){
            $time = time();
        }else{
            $time = strtotime(date("Y-m-d H:i:s",mktime(23, 59 , 59,date("m"),1,date("Y"))));
        }
        $endtime = strtotime(date("Y-m-01 H:i:s",mktime(00, 00 , 00,date("m"),1,date("Y"))));
        $continuous = $this->continuousSignCalculation($user_id,$time,$endtime);
        $omission = $this->omissionSignCalculation($user_id,$time,$endtime);
        $next       = $this->nextSignCalculation($fasi);
        $rule       = $this->getSignRule();
        $signday    = $this->getsignday($user_id,$time);

        $return['data']['isSign']     = $isSign;
        $return['data']['asi']        = $asi;
        $return['data']['total']      = $total;
        $return['data']['continuous'] = $continuous;
        $return['data']['next']       = $next;
        $return['data']['rule']       = $rule;
        $return['data']['omission']   = $omission;
        $return['data']['signday']   = $signday["count"];
        $return['data']['point']   = $signday["point"];
        return $return;
    }

    /**
     * 获取签到日期数据
     * @param $user_id
     * @param $seller_id
     * @param $date
     * @return array
     */
    public function getsignday($user_id,$date){
        $stime=mktime(0,0,0,date('m'),1,date('Y'));
        $etime=mktime(23,59,59,date('m'),date('t'),date('Y'));

        $where = [];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $where[] = ['ctime', ['EGT',$stime ],['ELT',$etime],'and'];
        $where[] = ['user_id', 'eq', $user_id];
        $count = $this->where($where)->column("ctime");
        foreach ($count as $k=>$v){
            $count[$k] = date('Y-m-d',$v);
        }
        $point = $this->where($where)->sum("num");
        $data = [
            "point" => $point,
            "count" => $count
        ];
        return $data;
    }

    /**
     * 获取用户签到漏签次数
     * @param $user_id
     * @param string $date
     * @param int $frequency
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function omissionSignCalculation($user_id, $date,$endtime, $frequency = 0){
        if(!$date){
            $date = strtotime("-1 day",time());
        }
        //计算本月内的
        if($date < $endtime){
            return $frequency;
        }

        $where = [];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $where[] = ['ctime', ['EGT',strtotime(date('Y-m-d 00:00:00',$date)) ],['ELT',strtotime(date('Y-m-d 23:59:59',$date))],'and'];
        $where[] = ['user_id', 'eq', $user_id];
        $info = $this->where($where)->find();
        if(!$info){
            $frequency += 1;
        }
        $date = strtotime("-1 day",$date);
        return $this->omissionSignCalculation($user_id, $date,$endtime, $frequency);
    }


    /**
     * 获取用户连续签到次数
     * @param $user_id
     * @param $date
     * @param $endtime
     * @param int $frequency
     * @return int
     */
    public function continuousSignCalculation($user_id, $date, $endtime, $frequency = 0)
    {
        //todo::连续签到时长计算
        if(!$date){
            $date = time();
        }
        //计算本月内的
        if($date < $endtime){
            return $frequency;
        }

        $where = [];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['ctime', ['EGT',strtotime(date('Y-m-d 00:00:00',$date)) ],['ELT',strtotime(date('Y-m-d 23:59:59',$date))],'and'];
        $res = $this->where($where)->select();
        // 先查询今天是否签到，如果没签到而且是查询第一次 就从昨天开始查询
        if(!$res && $frequency == 0){
            $date = strtotime("-1 day",time());
            $where = [];
            $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
            $where[] = ['ctime', ['EGT',strtotime(date('Y-m-d 00:00:00',$date)) ],['ELT',strtotime(date('Y-m-d 23:59:59',$date))],'and'];
            $where[] = ['user_id', 'eq', $user_id];
            $r = $this->where($where)->find();
            if(!$r){
                return 0;
            }
            $frequency = $frequency + 1;
            $date = date("Y-m-d",strtotime("-2 day",strtotime($date." 23:59:59")));
            return $this->continuousSignCalculation($user_id, $date,$endtime, $frequency);
        }
        if(count($res) < 1){
            return $frequency;
        }
        $frequency = $frequency + 1;

        $date = strtotime("-1 day",$date);

        return $this->continuousSignCalculation($user_id, $date,$endtime,$frequency);
    }


    /**
     * 下一次签到积分计算
     * @return int
     */
    public function nextSignCalculation($fasi)
    {
        //下一次签到奖励积分计算（包括今天没签到或今天已签到）
        return 0;
    }


    /**
     * 获取签到规则
     * @return array
     */
    public function getSignRule()
    {
        $point_discounted_proportion = getSetting('point_discounted_proportion');
        $orders_point_proportion     = getSetting('orders_point_proportion');
        $orders_reward_proportion    = getSetting('orders_reward_proportion');
        $sign_point_type             = getSetting('sign_point_type');
        $first_sign_point            = getSetting('first_sign_point');
        $continuity_sign_additional  = getSetting('continuity_sign_additional');
        $sign_most_point             = getSetting('sign_most_point');
        $sign_random_min             = getSetting('sign_random_min');
        $sign_random_max             = getSetting('sign_random_max');

        $rule[] = '下单时' . $point_discounted_proportion . '积分可抵扣1元人民币。';
        $rule[] = '下单使用积分抵扣时，最多可以抵扣订单额的' . $orders_point_proportion . '%。';
        $rule[] = '订单额每满' . $orders_reward_proportion . '元，奖励1积分。';
        if ($sign_point_type == self::SIGN_FIXED_POINT) {
            //固定积分奖励
            $rule[] = '连续签到首次奖励' . $first_sign_point . '积分，之后每日多奖励' . $continuity_sign_additional . '积分，单日最多可获得签到奖励' . $sign_most_point . '积分。';
        } else {
            //随机积分奖励
            $rule[] = '每日随机签到奖励积分，最少' . $sign_random_min . '积分，最多' . $sign_random_max . '积分。';
        }

        return $rule;
    }
}