<?php
namespace app\common\model;
use think\Db;

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
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setPoint($user_id, $num, $type = self::POINT_TYPE_SIGN, $remarks = '')
    {
        $return = [
            'status' => false,
            'msg' => ''
        ];

        //获取积分账号信息
        $user_model = new User();
        $user_info = $user_model->where(['id'=>$user_id])->find();

        $new_point = $user_info['point'] + $num;
        //积分余额判断
        if($new_point < 0)
        {
            $return['msg'] = '积分余额不足';
            return $return;
        }

        $return = false;
        Db::startTrans();
        try{
            //插入记录
            $data = [
                'user_id' => $user_id,
                'type' => $type,
                'num' => $num,
                'balance' => $new_point,
                'remarks' => $remarks,
                'ctime' => time()
            ];
            $this->insert($data);

            //插入主表
            $where[] = ['id', 'eq', $user_id];
            $user_info->where($where)
                ->setInc('point', $num);

            Db::commit();
            $return['status'] = true;
            $return['msg'] = '积分更改成功';
        }catch(\Exception $e){
            Db::rollback();
            $return['msg'] = '积分更改失败';
        }
        return $return;
    }


    /**
     * 签到
     * @param int $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign($user_id)
    {
        $return = [
            'status' => false,
            'msg' => '',
            'data' => 0
        ];

        //判断是否已经签到
        $res = $this->isSign($user_id);
        if($res['status'])
        {
            $return['msg'] = '今天已经签到，无需重复签到';
            return $return;
        }

        //获取店铺签到积分设置
        $sign_point_type = self::SIGN_RANDOM_POINT; //getShopSetting( 'sign_point_type'); //签到积分奖励类型

        //判断是固定积分计算还是随机积分计算
        if($sign_point_type == self::SIGN_RANDOM_POINT)
        {
            //随机计算
            $point = $this->signRandomPointCalculation();
        }
        else
        {
            //固定计算
            $point = $this->signFixedPointCalculation($user_id);
        }
        $return['data'] = $point;

        //插入数据库
        $result = $this->setPoint($user_id, $point, self::POINT_TYPE_SIGN, '积分签到，获得'.$point.'个积分');
        $return['msg'] = $result['msg'];
        if($result['status'])
        {
            $return['status'] = true;
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
        $return = [
            'status' => false,
            'msg' => '今天还没有签到'
        ];

        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $where[] = ['DATE_FORMAT(from_unixtime(ctime), "%Y-%m-%d")', 'eq', date('Y-m-d', time())];

        $day = $this->field('DATE_FORMAT(from_unixtime(ctime), "%Y-%m-%d") as day')
            ->where($where)
            ->find();

        if($day)
        {
            $return['status'] = true;
            $return['msg'] = '今天已经签到了';
        }

        return $return;
    }


    /**
     * 签到随机积分计算
     * @return float|int
     */
    protected function signRandomPointCalculation()
    {
        $sign_random_min = 1; //getShopSetting( 'sign_random_min'); //最小随机
        $sign_random_max = 10; //getShopSetting( 'sign_random_max'); //最大随机
        $point = mt_rand($sign_random_min, $sign_random_max); //随机积分
        //$point = $this->signAppointDatePointCalculation( $point); //判断计算指定日期
        return $point;
    }


    /**
     * 签到指定积分计算
     * @param $user_id
     * @return float|int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function signFixedPointCalculation($user_id)
    {
        $first_sign_point = 1;//getShopSetting('first_sign_point'); //首次签到积分
        $continuity_sign_additional = 1;//getShopSetting('continuity_sign_additional'); //连续签到追加
        $sign_most_point = 10;//getShopSetting('sign_most_point'); //签到最多积分

        //获取连续签到天数
        $max_continuity_day = ceil(($sign_most_point - $first_sign_point) / $continuity_sign_additional); //最大连续签到天数
        $day = date('Y-m-d', strtotime('-'.$max_continuity_day.' day'));

        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', self::POINT_TYPE_SIGN];
        $where[] = ['from_unixtime(ctime)', '>=', $day];
        $res = $this->field('DATE_FORMAT(from_unixtime(ctime), "%Y-%m-%d") as day')
            ->where($where)
            ->group('DATE_FORMAT(from_unixtime(ctime), "%Y-%m-%d")')
            ->select();

        $new_res = [];
        foreach($res as $k => $v)
        {
            $new_res[] = $v['day'];
        }
        $int = 0; //连续签到天数
        for($i = 1; $i <= $max_continuity_day; $i++)
        {
            $now = date('Y-m-d', strtotime('-'.$i.' day'));
            if(in_array($now, $new_res))
            {
                $int++;
            }
            else
            {
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
        $nowDate = date('Y-m-d', time());
        if($sign_appoint_date_status)
        {
            //开启指定日期
            $sign_appoint_date = '';//getShopSetting('sign_appoint_date'); //特殊指定日期
            $sign_appoint_date = json_decode($sign_appoint_date, true);
            if(in_array($nowDate, $sign_appoint_date))
            {
                //当前是指定日期
                $sign_appoint_data_type = '';//getShopSetting('sign_appoint_data_type'); //特殊指定日期奖励类型
                if($sign_appoint_data_type == self::SIGN_APPOINT_DATE_RATE)
                {
                    //倍率
                    $sign_appoint_date_rate = 2;getShopSetting('sign_appoint_date_rate'); //特殊指定日期倍数
                    $point = $old_point * $sign_appoint_date_rate;
                }
                else
                {
                    //追加
                    $sign_appoint_date_additional = 1;//getShopSetting( 'sign_appoint_date_additional'); //特殊指定日期追加数量
                    $point = $old_point + $sign_appoint_date_additional;
                }
            }
            else
            {
                //不是指定日期
                $point = $old_point;
            }
        }
        else
        {
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
    public function pointLogList($user_id, $type = false, $page = 1, $limit = 20)
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        if($type)
        {
            $where[] = ['type', 'eq', $type];
        }
        $where[] = ['user_id', 'eq', $user_id];

        $res = $this->field('id, type, num, balance, remarks, ctime')
            ->where($where)
            ->order('ctime', 'desc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)->count();

        $return['data'] = $res;
        $return['count'] = $count;
        if($res)
        {
            $return['status'] = true;
            if(count($res)>=1)
            {
                $return['msg'] = '积分记录获取成功';
                foreach($return['data'] as &$v)
                {
                    $v['type'] = config('params.user_point_log')['type'][$v['type']];
                    $v['balance'] = $v['balance']+$v['num'];
                    $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                }
            }
            else
            {
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderComplete($user_id, $money, $order_id)
    {
        $orders_reward_proportion = 10;//getShopSetting( 'orders_reward_proportion');
        $point = floor($money / $orders_reward_proportion);
        $this->setPoint($user_id, $point, self::POINT_TYPE_REBATE, '订单:'.$order_id.'的积分奖励');
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
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }
        if(isset($post['type']) && $post['type'] != ""){
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
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v) {
            if($v['type']) {
                $list[$k]['type'] = config('params.user_point_log')['type'][$v['type']];
            }
            if($v['user_id']) {
                $list[$k]['user_id'] = get_user_info($v['user_id']);
            }

            if($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
        }
        return $list;
    }
}