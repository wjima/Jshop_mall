<?php

/**
 * 分销商模型
 */
namespace addons\DistributionCenter\model;

use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\User;
use app\common\model\UserGrade;
use think\Model;
use think\Validate;

class Distribution extends Model
{


    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';
    protected $createTime = 'ctime';

    const VERIFY_YES = 1; //审核通过
    const VERIFY_WAIT = 2;//等待审核
    const VERIFY_REFUSE = 3;//审核拒绝

    const COMMISSION_TYPE_PRE = 1;//百分比
    const COMMISSION_TYPE_FIXED = 2;//固定

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {

        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this
            ->field($tableWhere['field'])
            ->alias('d')
            ->join(config('database.prefix') . 'user u', 'u.id = d.user_id')
            ->where($tableWhere['where'])
            ->paginate($limit);

        $data        = $this->tableFormat($list->getCollection());

        $count =  $this
            ->alias('d')
            ->join(config('database.prefix') . 'user u', 'u.id = d.user_id')
            ->where($tableWhere['where'])
            ->count();
        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $count;
        $re['data']  = $data;
        return $re;
    }

    protected function tableWhere($post)
    {
        $result['where'] = [];

        if (isset($post['name']) && $post['name']) {
            $result['where'][] = ['d.name', 'like', '%' . $post['name'] . '%'];
        }

        if (isset($post['mobile']) && $post['mobile']) {
            $result['where'][] = ['d.mobile', '=', $post['mobile']];
        }

        if (isset($post['verify']) && $post['verify']) {
            $result['where'][] = ['d.verify', '=', $post['verify']];
        }


        if (isset($post['field'])) {
            $result['field'] = $post['field'];
        } else {
            $result['field'] = "d.*";
        }
        if (isset($post['order'])) {
            $result['order'] = $post['order'];
        } else {
            $result['order'] = [];
        }
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
        if (!$list->isEmpty()) {
            $list = $list->toArray();
            foreach ((array)$list as $key => $value) {
                if ($value['verify'] == self::VERIFY_YES) {
                    $list[$key]['verify'] = '审核通过';
                } elseif ($value['verify'] == self::VERIFY_REFUSE) {
                    $list[$key]['verify'] = '审核拒绝';
                } elseif ($value['verify'] == self::VERIFY_WAIT) {
                    $list[$key]['verify'] = '待审核';
                }
                $list[$key]['ctime']       = getTime($value['ctime']);
                $list[$key]['user_mobile'] = get_user_info($value['user_id'],'mobile');
                $list[$key]['utime']       = getTime($value['utime']);
                $list[$key]['verify_time'] = getTime($value['verify_time']);
                $userGradeModel            = new UserGrade();
                $pwhere['id']              = $value['grade_id'];
                $info                      = $userGradeModel->field('name')->where($pwhere)->find();
                $list[$key]['grade_name']  = $info['name'];
            }
        }
        return $list;
    }

    /**
     * 添加用户信息
     * @param $data
     * @param $user_id
     * @return array
     */
    public function addData($data, $user_id)
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => [],
        ];
        if (isset($data['mobile']) && !$data['mobile']) {
            $result['msg'] = '请填写手机号';
            return $result;
        }
        if (isset($data['mobile']) && !isMobile($data['mobile'])) {
            $result['msg'] = '请填写正确的手机号';
            return $result;
        }
        if (!$data['name']) {
            $result['msg'] = '请填写您的姓名';
            return $result;
        }
        $data['user_id'] = $user_id;
        $info            = $this->where('user_id', '=', $user_id)->find();
        if ($info) {
            $result['status']=false;
            $result['msg']='您已申请，请勿重复提交';
            return $result;
        }
        //默认等级处理
        if (!$data['grade_id']) {
            $disGradeModel    = new DistributionGrade();
            $data['grade_id'] = $disGradeModel->getDefault();
        }
        //判断是否存在
        if ($this->allowField(true)->save($data) !== false) {
            $result['msg']    = '保存成功';
            $result['status'] = true;
            return $result;
        }
        return $result;
    }

    /**
     * 获取分销商信息
     * @param $user_id 用户id
     * @param bool|false $checkStatus 是否检查满足条件
     * @return array
     */
    public function getInfo($user_id, $checkStatus = false)
    {
        $result = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => [],
        ];

        $info = $this->where('user_id', '=', $user_id)->find();

        if ($info && $info['verify'] == self::VERIFY_YES) {
            $distributionOrderModel          = new DistributionOrder();
            $info['total_settlement_amount'] = $distributionOrderModel->where([['is_settlement', 'neq', $distributionOrderModel::SETTLEMENT_CANCEL], ['user_id', '=', $user_id]])->sum('amount');//总金额
            $info['settlement_amount']       = $distributionOrderModel->where([['is_settlement', '=', $distributionOrderModel::SETTLEMENT_YES], ['user_id', '=', $user_id]])->sum('amount');//已结算金额
            $info['freeze_amount']           = $distributionOrderModel->where([['is_settlement', '=', $distributionOrderModel::SETTLEMENT_NO], ['user_id', '=', $user_id]])->sum('amount');//冻结金额
            $where[]                         = ['ctime', '>=', mktime(0, 0, 0, date('m'), 1, date('Y'))];
            $where[]                         = ['ctime', '<', mktime(23, 59, 59, date('m'), date('t'), date('Y'))];
            $where[]                         = ['user_id', '=', $user_id];
            $info['current_month_order']     = $distributionOrderModel->where($where)->count();//冻结金额
            $info['store']                   = alphaID($info['user_id']);
            //今日收益
            $info['today_freeze_amount'] = $distributionOrderModel->where([['is_settlement', 'neq', $distributionOrderModel::SETTLEMENT_CANCEL]])->where([['user_id', '=', $user_id], ['ctime', '>', strtotime(date('Y-m-d') . '00:00:00')], ['ctime', '<=', strtotime(date('Y-m-d') . '23:59:59')]])->sum('amount');//已结算金额
            //今日订单
            $info['today_order'] = $distributionOrderModel->where([['is_settlement', 'neq', $distributionOrderModel::SETTLEMENT_CANCEL]])->where([['user_id', '=', $user_id], ['ctime', '>', strtotime(date('Y-m-d') . '00:00:00')], ['ctime', '<=', strtotime(date('Y-m-d') . '23:59:59')]])->count();
            $userModel           = new User();
            //今日会员
            $info['today_user']  = $userModel->where([['pid', '=', $user_id], ['ctime', '>', strtotime(date('Y-m-d') . '00:00:00')], ['ctime', '<=', strtotime(date('Y-m-d') . '23:59:59')]])->count();
        }elseif(!$info){
            $info['verify'] = 0;//不存在审核情况
        }
        $info['store_name']       = isset($info['store_name']) ? $info['store_name'] : '';
        $info['store_desc']       = isset($info['store_desc']) ? $info['store_desc'] : '';
        $info['store_logo']       = isset($info['store_logo']) ? $info['store_logo'] : '';
        $info['store_banner']     = isset($info['store_banner']) ? $info['store_banner'] : '';
        $info['ctime']            = isset($info['ctime']) ? $info['ctime'] : time();
        $info['store_logo_src']   = _sImage($info['store_logo']);
        $info['store_banner_src'] = _sImage($info['store_banner']);
        $info['ctime']            = getTime($info['ctime']);
        $goodsModel               = new Goods();
        $info['total_goods']      = $goodsModel->where([['marketable', '=', '1']])->count();
        if(isset($info['grade_id']) && $info['grade_id']){
            $userGradeModel = new UserGrade();
            $grade = $userGradeModel->where([['id','=',$info['grade_id']]])->field('name')->find();
            $info['grade_name'] = $grade['name'];
        }

        if ($checkStatus && (!$info || !isset($info['id']) || $info['verify'] != self::VERIFY_YES)) {
            $addonModel            = new \app\common\model\Addons();
            $config                = $addonModel->getSetting('DistributionCenter');
            $info['need_apply']    = true;//是否需要申请
            $info['condition_msg'] = '您的条件已满足。';
            //无需审核,但是需要满足条件
            if (isset($config['setting']['distribution_type']) && $config['setting']['distribution_type'] == '3') {
                $info['need_apply']         = false;
                $info['condition_status']   = false;//条件状态
                $info['condition_progress'] = 0;
                //满足条件，直接成为会员
                $this->checkCondition($config, $info, $user_id);

                if ($info['condition_status'] == true && $info['condition_progress'] == 100) {
                    //添加用户
                    $userModel = new User();
                    $user      = $userModel->get($user_id);
                    if ($user['mobile']) {
                        $iData['mobile'] = $user['mobile'];
                    }
                    $iData['name']        = isset($user['nickname']) ? $user['nickname'] : $user['mobile'];
                    $iData['verify']      = self::VERIFY_YES;
                    $iData['verify_time'] = time();
                    $this->addData($iData, $user_id);
                    $info['condition_status']   = true;//条件状态
                    $info['condition_progress'] = 100;
                    $info['verify'] = self::VERIFY_YES;
                }
            } else {
                //无条件，但是需要审核
                if (isset($config['setting']['distribution_type']) && $config['setting']['distribution_type'] == '1') {
                    $info['need_apply']    = true;//是否需要申请
                    $info['condition_msg'] = '您的条件已满足。';
                    $info['condition_status']   = true;//条件状态
                    $info['condition_progress'] = 100;
                } elseif (isset($config['setting']['distribution_type']) && $config['setting']['distribution_type'] == '2') {//有条件，需要审核
                    $this->checkCondition($config, $info, $user_id);
                }
            }
        }
        $result['msg']    = '获取成功';
        $result['status'] = true;
        $result['data']   = $info;
        return $result;
    }

    /**
     * 检查是否可以成为分销商
     * @param array $config
     * @param array $info
     * @param int $user_id
     */
    public function checkCondition($config = [], &$info = [], $user_id = 0)
    {
        //判断消费
        $info['condition_status']     = false;//条件状态
        $info['condition_progress']   = 0;
        $orderModel                   = new Order();
        $where                        = [];
        $config['distribution_money'] = isset($config['setting']['distribution_money']) ? $config['setting']['distribution_money'] : 0;
        //$where[]                      = ['payed', '>=', $config['setting']['distribution_money']];
        $where[] = ['pay_status', '=', $orderModel::PAY_STATUS_YES];
        $where[] = ['user_id', '=', $user_id];
        $payed   = $orderModel->where($where)->sum('payed');
        if ($payed < $config['distribution_money'] && $config['distribution_money'] > 0) {
            $info['condition_msg'] = '您的消费额度未满足' . $config['setting']['distribution_money'] . '元无法申请，快去下单吧~';
        } else {
            $info['condition_progress'] = 50;
            //判断是否需要购买商品
            if (isset($config['setting']['distribution_goods']) && $config['setting']['distribution_goods'] == '1') {
                $info['condition_progress'] = $info['condition_progress'] + 50;
                $info['condition_status']   = true;//条件状态
                $info['condition_msg']      = '您的条件已满足，马上申请。';
            } else {
                //任意商品
                if (isset($config['setting']['distribution_goods']) && $config['setting']['distribution_goods'] == '2') {
                    $where   = [];
                    $where[] = ['user_id', '=', $user_id];
                    //$where[] = ['payed', '>=', $config['setting']['distribution_money']];
                    $where[] = ['pay_status', '=', $orderModel::PAY_STATUS_YES];
                    $order   = $orderModel->where($where)->count();
                    if ($order >= 1) {
                        $info['condition_progress'] = $info['condition_progress'] + 50;
                        $info['condition_status']   = true;//条件状态
                        $info['condition_msg']      = '您的条件已满足，马上申请。';
                    } else {
                        $info['condition_msg'] = '您的条件未满足，请任意购买一件商品即可成为分销商。';
                    }
                } elseif (isset($config['setting']['distribution_goods']) && $config['setting']['distribution_goods'] == '3') {//购买指定商品
                    //判断是否购买指定商品
                    $where   = [];
                    $where[] = ['user_id', '=', $user_id];
                    //$where[] = ['o.payed', '>=', $config['setting']['distribution_money']];
                    $where[]  = ['o.pay_status', '=', $orderModel::PAY_STATUS_YES];
                    $where[]  = ['oi.goods_id', '=', $config['setting']['distribution_goods_id']];
                    $orderNum = $orderModel->alias('o')->join(config('database.prefix') . 'order_items oi', 'oi.order_id = o.order_id')
                        ->where($where)
                        ->count();
                    if ($orderNum >= 1) {
                        $info['condition_progress'] = $info['condition_progress'] + 50;
                        $info['condition_status']   = true;//条件状态
                        $info['condition_msg']      = '您的条件已满足，马上申请。';
                    } else {
                        $goodsModel            = new \app\common\model\Goods();
                        $goodsInfo             = $goodsModel->field('name')->get($config['setting']['distribution_goods_id']);
                        $info['condition_msg'] = '您的条件未满足，请购买指定的' . $goodsInfo['name'] . '商品即可成为分销商。';
                    }
                }
            }
        }
    }

    /***
     * 更新用户数据
     * @param $data
     * @param $id
     * @return array
     */
    public function updateInfo($data, $id)
    {
        $result = [
            'status' => false,
            'msg'    => '保存失败',
            'data'   => [],
        ];
        //判断是否存在
        if ($this->allowField(true)->save($data, ['id' => $id]) !== false) {
            $result['msg']    = '保存成功';
            $result['status'] = true;
            return $result;
        }
        return $result;
    }

    /**
     * 更新用户数据
     * @param mixed $data
     * @param $user_id
     * @return array
     */
    public function updateInfoByUserId($data, $user_id)
    {
        $result = [
            'status' => false,
            'msg'    => '保存失败',
            'data'   => [],
        ];
        //判断是否存在
        $info = $this->where('user_id', '=', $user_id)->find();
        if ($info) {
            if ($this->allowField(true)->save($data, ['id' => $info['id']]) !== false) {
                $result['msg']    = '保存成功';
                $result['status'] = true;
                return $result;
            }
        } else {
            if ($this->allowField(true)->save($data) !== false) {
                $result['msg']    = '保存成功';
                $result['status'] = true;
                return $result;
            }
        }
        return $result;
    }

    /**
     * 获取分销商等级以及返佣设置
     * @param int $user_id
     * @return array
     */
    public function getGradeAndCommission($user_id = 0)
    {
        $result = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => [],
        ];

        $info = $this->where([['user_id', '=', $user_id]])->field('grade_id,id')->find();
        if (!$info) {
            return $result;//不是分销商的，不返利。 todo
        }
        $resultModel = new DistributionResult();
        $setting     = getAddonsConfigVal('DistributionCenter', 'setting');
        if (isset($info['grade_id']) && $info['grade_id']) {
            $res = $resultModel->where([['grade_id', '=', $info['grade_id']]])->select();
            if (!$res) {
                $result['data']['grade_id']     = 0;
                $result['data']['commission_1'] = [
                    'type'     => $setting['commission_type'],
                    'discount' => $setting['commission_1']
                ];
                $result['data']['commission_2'] = [
                    'type'     => $setting['commission_type'],
                    'discount' => $setting['commission_2']
                ];
                $result['data']['commission_3'] = [
                    'type'     => $setting['commission_type'],
                    'discount' => $setting['commission_3']
                ];
            } else {
                $result['data']['grade_id'] = $info['grade_id'];
                foreach ($res as $key => $val) {
                    $params                                   = json_decode($val['params'], true);
                    $result['data'][strtolower($val['code'])] = [
                        'type'     => $params['commission_type'],
                        'discount' => $params['discount']
                    ];
                }
            }
        } else {
            $result['data']['grade_id']     = 0;
            $result['data']['commission_1'] = [
                'type'     => $setting['commission_type'],
                'discount' => $setting['commission_1']
            ];
            $result['data']['commission_2'] = [
                'type'     => $setting['commission_type'],
                'discount' => $setting['commission_2']
            ];
            $result['data']['commission_3'] = [
                'type'     => $setting['commission_type'],
                'discount' => $setting['commission_3']
            ];
        }
        $result['data']['distribution_level'] = $setting['distribution_level'];
        $result['status']                     = true;
        $result['msg']                        = '获取成功';
        return $result;
    }


    /**
     * 检查当前用户是否可以升级
     * @param $user_id
     * @return array
     */
    public function checkUpdate($user_id)
    {
        $result         = [
            'status' => false,
            'msg'    => '保存失败',
            'data'   => [],
        ];
        $conditionModel = new DistributionCondition();
        $info           = $this->field('grade_id,id,mobile')->where([['user_id', '=', $user_id]])->find();
        if (isset($info['grade_id']) && $info['grade_id']) {
            //循环获取
            $distributionGradeModel = new DistributionGrade();
            $where                  = [];
            $where[]                = ['upgrade', '=', $distributionGradeModel::UPGRADE_YES];
            $where[]                = ['grade_id', '>', $info['grade_id']];
            $grade                  = $distributionGradeModel->where($where)->order('grade_id', 'asc')->find();//找下有没有可以升级的分销商等级
            if ($grade) {
                $res       = $conditionModel->where([['grade_id', '=', $grade['grade_id']]])->select();//查询出来所有条件
                $condition = true;//默认满足升级
                //循环所有条件，判断是否可以升级
                foreach ($res as $key => $val) {
                    $method = 'condition_' . $val['code'];
                    if (method_exists($conditionModel, $method)) {
                        $params    = json_decode($val['params'], true);
                        $condition = $conditionModel->$method($params, $user_id);
                        if (!$condition) {
                            break;
                        }
                    }
                }
                if ($condition) {//所有条件都满足，升级吧
                    $res           = $this->where([['user_id', '=', $user_id]])->update(['grade_id' => $grade['grade_id']]);
                    $result['msg'] = '升级成功';
                } else {
                    $result['msg'] = '条件暂不满足，无法升级';
                }
            }
        }
        return $result;
    }

    /**
     * 根据用户id获取信息
     * @param $user_id
     * @param string $field
     * @return array|mixed|null|\PDOStatement|string|Model
     */
    public function getInfoByUserId($user_id, $field = '*')
    {
        if ($field != '*') {
            $res = $this->where([['user_id', '=', $user_id]])->field($field)->find();
            return isset($res[$field]) ? $res[$field] : '';
        } else {
            return $this->where([['user_id', '=', $user_id]])->field($field)->find();
        }
    }

    /**
     * 获取店铺信息
     * @param string $store
     * @return array
     */
    public function getStore($store = '')
    {

        $result                   = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => [],
        ];
        $info                     = $this->field('id,user_id,store_name,store_desc,store_logo,store_banner,name,ctime')->where([['user_id', '=', $store]])->find();
        $info['store_logo_src']   = _sImage($info['store_logo']);
        $info['store_banner_src'] = _sImage($info['store_banner']);
        $info['ctime']            = getTime($info['ctime']);
        $info['store_name']       = ($info['store_name']) ? $info['store_name'] : '';
        $info['store_desc']       = ($info['store_desc']) ? $info['store_desc'] : '';
        $goodsModel               = new Goods();
        $info['total_goods']      = $goodsModel->where([['marketable', '=', '1']])->count();
        if (isset($info['id']) || isset($info['user_id'])) {
            $info->hidden(['id', 'user_id']);
        }
        $result['data']   = $info;
        $result['msg']    = '获取成功';
        $result['status'] = true;
        return $result;
    }

}
