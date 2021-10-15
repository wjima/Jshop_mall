<?php

namespace addons\DistributionCenter\model;

use app\common\model\Order;
use app\common\model\UserGrade;
use think\Model;
use think\Validate;

class DistributionCondition extends Model
{

    public $code = [
        'USER_ORDERS'    => [
            'name' => '个人消费总额(已完成的订单)',
            'type' => 'user',
        ],
        'USER_ORDERSNUM' => [
            'name' => '个人订单数量(已完成的订单)',
            'type' => 'user',
        ],
        /*        'GROUP_ORDERS' => [
                    'name' => '团队消费总额',
                    'type' => 'user',
                ],*/
        'GOODS_ALL'      => [
            'name' => '所有商品满足条件',
            'type' => 'goods',
        ],
        'GOODS_IDS'      => [
            'name' => '购买指定商品',
            'type' => 'goods',
        ],
        /*        'USER_GRADE' => [
                    'name' => '直推几个指定用户等级',
                    'type' => 'user',
                ]*/
    ];

    /**
     * @param $code
     * @param array $params
     * @return string
     */
    public function getConditionMsg($code, $params = [])
    {
        switch ($code) {
            case 'GOODS_ALL':
                $msg = '购买所有商品 ';
                break;
            case 'GOODS_IDS':
                $msg = '购买指定商品 ';
                break;
            case 'USER_ORDERS':
                $msg = '个人消费总额 ';
                break;
            case 'USER_ORDERSNUM':
                $msg = '个人订单数量 ';
                break;
            case 'GROUP_ORDERS':
                $msg = '团队消费总额 ';
                break;
            case 'USER_GRADE':
                $msg = '直推' . $params['num'] . '个' . $params['rank'] . '等级用户';
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

        $tableWhere   = $this->tableWhere($post);
        $list         = $this
            ->field($tableWhere['field'])
            ->alias('dc')
            ->join(config('database.prefix') . 'user_grade ug', 'ug.id = dc.grade_id')
            ->where($tableWhere['where'])
            ->select();
        $data         = $this->tableFormat($list);
        $res['code']  = 0;
        $res['msg']   = '';
        $res['count'] = count($list);
        $res['data']  = $data;
        return $res;
    }


    protected function tableWhere($post)
    {
        $result['where'] = [];
        if (isset($post['grade_id'])) {
            $result['where']['dc.grade_id'] = $post['grade_id'];
        }

        if (isset($post['field'])) {
            $result['field'] = $post['field'];
        } else {
            $result['field'] = "dc.*";
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
        foreach ($list as $k => $v) {
            if ($v['code']) {
                $list[$k]['name'] = $this->code[$v['code']]['name'];
            }
            if ($v['params']) {
                $list[$k]['params'] = $v['params'];
            }
        }
        return $list;
    }


    /**
     *  添加促销的条件
     * User:wht
     * @param array $data
     * @return array
     */
    public function addData($data)
    {

        //校验结果
        $result = $this->addCheck($data);
        if (!$result['status']) {
            return $result;
        }
        $result['status'] = false;          //重新置成false

        $data['params'] = json_encode($data['params']);
        if ($data['id'] != '') {
            //更新
            $info = $this->getInfo($data['id']);
            if ($info) {
                if ($this->allowField(true)->save($data, ['id' => $data['id']]) !== false) {
                    $result['status'] = true;
                } else {
                    $result['msg'] = "保存失败";
                }
                return $result;

            }
        } else {
            //添加
            //先判断是否有此促销
            $userGradeModel = new UserGrade();
            $where['id']    = $data['grade_id'];
            $info           = $userGradeModel->where($where)->find();
            if ($info) {
                if ($this->allowField(true)->save($data)) {
                    $result['status'] = true;
                } else {
                    $result['msg'] = "保存失败";
                }
                return $result;
            } else {
                $result['msg'] = '没有找到此等级记录';
                return $result;
            }
        }
    }


    //添加或者编辑的时候，校验信息
    private function addCheck($data)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        if (!isset($data['code']) || !isset($data['grade_id']) || !isset($data['params'])) {
            return error_code(10003);
        }
        if (!isset($this->code[$data['code']])) {
            return error_code(15004);
        }
        switch ($data['code']) {
            case 'USER_ORDERS':
                if (!preg_match("/^[0-9]+(.[0-9]{1,2})?$/", $data['params']['money'])) {
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                break;
            case 'GOODS_CATS':
                if (!preg_match("/^[1-9][0-9]*$/", $data['params']['nums'])) {
                    $result['msg'] = "数量必须输入正整数";
                    return $result;
                }
                if ($data['params']['cat_id'] == '') {
                    $result['msg'] = "请选择商品分类";
                    return $result;
                }
                break;
            case 'GOODS_BRANDS':
                if (!preg_match("/^[1-9][0-9]*$/", $data['params']['nums'])) {
                    $result['msg'] = "数量必须输入正整数";
                    return $result;
                }
                if ($data['params']['brand_id'] == '') {
                    $result['msg'] = "请选择商品";
                    return $result;
                }
                break;
            case 'ORDER_FULL':
                if (!preg_match("/^[0-9]+(.[0-9]{1,2})?$/", $data['params']['money'])) {
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                if ($data['params']['money'] == '') {
                    $result['msg'] = "请输入金额";
                    return $result;
                }
                break;
        }
        $result['status'] = true;


        return $result;
    }


    public function toDel($id)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        $info   = $this->getInfo($id);
        if ($info) {
            $this->where(['id' => $info['id'], 'grade_id' => $info['grade_id']])->delete();
            $result['status'] = true;
            return $result;
        } else {
            $result['msg'] = '没有找到此等级记录';
            return $result;
        }
    }


    //取信息
    public function getInfo($id)
    {
        $where['dc.id'] = $id;
        $info           = $this
            ->field('dc.*')
            ->alias('dc')
            ->join(config('database.prefix') . 'user_grade ug', 'ug.id = dc.grade_id')
            ->where($where)->find();
        if ($info) {
            $info['params'] = json_decode($info['params'], true);
        }
        return $info;
    }


    //所有商品满足条件
    public function condition_GOODS_ALL($params, $user_id)
    {
        $orderModel = new Order();
        $where[]    = ['pay_status', '=', $orderModel::PAY_STATUS_YES];
        $where[]    = ['user_id', '=', $user_id];
        $total_pay  = $orderModel->where($where)->count();
        if ($total_pay >= 1) {
            return true;
        } else {
            return false;
        }
    }

    //购买指定商品满足条件
    public function condition_GOODS_IDS($params, $user_id)
    {
        $orderModel = new Order();
        $where      = [];
        $where[]    = ['user_id', '=', $user_id];
        $where[]    = ['o.pay_status', '=', $orderModel::PAY_STATUS_YES];
        $where[]    = ['oi.goods_id', '=', $params['goods_id']];
        $total_pay  = $orderModel->alias('o')->join(config('database.prefix') . 'order_items oi', 'oi.order_id = o.order_id')
            ->where($where)
            ->count();
        if ($total_pay >= 1) {
            return true;
        } else {
            return false;
        }
    }

    //用户订单总额
    public function condition_USER_ORDERS($params, $user_id)
    {
        $orderModel = new Order();
        $where[]    = ['pay_status', '=', $orderModel::PAY_STATUS_YES];
        $where[]    = ['user_id', '=', $user_id];
        if ($params['limit_day']) {
            $start_date = strtotime(date('Y-m-d', strtotime('-' . $params['limit_day'] . ' days')) . ' 00:00:00');
            $end_time   = time();
            $where[]    = ['payment_time', ['>', $start_date], ['<=', $end_time], 'and'];
        }
        $total_pay = $orderModel->where($where)->sum('payed');
        if ($total_pay >= $params['money'] || $params['money'] <= 0) {
            return true;
        } else {
            return false;
        }
    }

    //用户订单数量
    public function condition_USER_ORDERSNUM($params, $user_id)
    {
        $orderModel = new Order();
        $where[]    = ['pay_status', '=', $orderModel::PAY_STATUS_YES];
        $where[]    = ['user_id', '=', $user_id];
        if ($params['limit_day']) {
            $start_date = strtotime(date('Y-m-d', strtotime('-' . $params['limit_day'] . ' days')) . ' 00:00:00');
            $end_time   = time();
            $where[]    = ['payment_time', ['>', $start_date], ['<=', $end_time], 'and'];
        }
        $total_pay = $orderModel->where($where)->count();
        if ($total_pay >= $params['num'] || $params['num'] <= 0) {
            return true;
        } else {
            return false;
        }
    }


}
