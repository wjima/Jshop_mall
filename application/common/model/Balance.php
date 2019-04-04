<?php
namespace app\common\model;
use think\Db;


/**
 * 用户余额
 * Class Balance
 * @package app\common\model
 * @author keinx
 */
class Balance extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';

    const TYPE_PAY = 1;                 //用户消费，对应支付单表
    const TYPE_REFUND = 2;              //用户退款，用余额支付的话，退款类型对应1
    const TYPE_RECHARGE = 3;            //充值
    const TYPE_TOCASH = 4;              //提现
    const TYPE_DISTRIBUTION = 5;        //三级分销佣金
    const TYPE_ADMIN = 7;               //后台操作

    /**
     * 充值
     */
    public function recharge()
    {

    }


    /**
     * 余额变动记录
     * @param $user_id  //当前用户id,当是店铺的时候，取店铺创始人的user_id
     * @param $type     //类型
     * @param $money    //金额，永远是正的
     * @param string $source_id //资源id
     * @param int $cate_money   //服务费金额 (提现)
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function change($user_id, $type, $money, $source_id = "", $cate_money = 0)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '操作成功'
        ];

        if($money != 0)
        {
            //取用户实际余额
            $userModel = new User();
            $userInfo = $userModel::withTrashed()->where([ 'id' => $user_id ])->find();
            if(!$userInfo)
            {
                return error_code(11004);
            }

            //取描述，并简单校验
            $re = $this->getMemo($type, $money, $source_id, $cate_money);
            if(!$re['status'])
            {
                return $re;
            }
            $memo = $re['data'];

            (float)$money = $money;
            if($type != self::TYPE_ADMIN)
            {
                //后台充值或调不改绝对值
                $money = abs($money);
            }
            //如果是减余额的操作，还是加余额操作
            if($type == self::TYPE_PAY || /* $type == self::TYPE_REFUND || //退款是往账户上加钱的 */$type == self::TYPE_TOCASH)
            {
                $money = - $money - $cate_money;
            }
            $balance = $userInfo[ 'balance' ] + $money;
            if(($balance) < 0)
            {
                return error_code(11007);
            }

            $data['user_id'] = $user_id;
            $data['type'] = $type;
            $data['money'] = $money;
            $data['balance'] = $balance;
            $data['source_id'] = $source_id;
            $data['memo'] = $memo;
            $data['ctime'] = time();
            $blanceModel = new Balance();
            $blanceModel->save($data);  //为啥要now在save，是因为存在多个余额更新的时候，只会更新一条
            //上面保存好主体表，下面保存明细表
            $userInfo->balance = $balance;
            $userInfo->save();
        }

        $result['status'] = true;
        return $result;
    }


    /**
     * 取得描述，并做简单校验
     * @param $type
     * @param $money
     * @param string $source_id
     * @param string $cate_money
     * @return array|mixed
     */
    private function getMemo($type, $money, $source_id = "", $cate_money = '')
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        switch($type)
        {
            case self::TYPE_PAY:
                $result['data'] = '消费了' . $money . '元';
                break;
            case self::TYPE_REFUND:
                $result['data'] = '收到了退款' . $money . '元';
                break;
            case self::TYPE_RECHARGE:
                $result['data'] = '充值了' . $money . '元';
                break;
            case self::TYPE_TOCASH:
                $result['data'] = '提现了' . $money . '元';
                $result['data'] .= $cate_money ? ',手续费'. $cate_money . '元' : '';
                break;
            case self::TYPE_DISTRIBUTION:
                $result['data'] = '佣金' . $money . '元';
                break;
            case self::TYPE_ADMIN:
                $result['data'] = '后台操作' . $money . '元';
                break;
            default:
                return error_code(10000);
        }
        //::todo    这里还可以做一些其他的校验
        return $result;
    }


    /**
     * 返回layui的table所需要的格式
     * @param $post
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if(isset($post['limit']))
        {
            $limit = $post['limit'];
        }
        else
        {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this::with('userInfo')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection()); //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }


    /**
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['user_id']) && $post['user_id'] != "" )
        {
            $where[] = ['user_id', 'eq', $post['user_id']];
        }
        else
        {
            if(isset($post['mobile']) && $post['mobile'] != "")
            {
                if($user_id = get_user_id($post['mobile']))
                {
                    $where[] = ['user_id', 'eq', $user_id];
                }
                else
                {
                    $where[] = ['user_id', 'eq', '99999999'];   //如果没有此用户，那么就赋值个数值，让他查不出数据
                }
            }
        }
        if(isset($post['type']) && $post['type'] != "")
        {
            $where[] = ['type', 'eq', $post['type']];
        }
        if(isset($post['datetime']) && $post['datetime'] != "")
        {
            $datetime = explode(' - ', $post['datetime']);
            $sd = strtotime($datetime[0].' 00:00:00');
            $ed = strtotime($datetime[1].' 23:59:59');
            $where[] = ['ctime', 'BETWEEN', [$sd, $ed]];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'ctime desc';
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @param $list //array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v)
        {
            if($v['ctime'])
            {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if($v['type'])
            {
                $list[$k]['type'] = config('params.balance')['type'][$v['type']];
            }
        }
        return $list;
    }


    /**
     * 关联用户信息
     * @return \think\model\relation\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne('User', 'id', 'user_id')->bind([
            'mobile',
            'nickname'
        ]);
    }


    /**
     * 获取用户的余额明细记录
     * @param $user_id
     * @param string $order
     * @param int $page
     * @param int $limit
     * @param int $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBalanceList($user_id, $order = '', $page = 1, $limit = 10, $type = 0)
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $where[] = ['user_id', 'eq', $user_id];
        if($type != 0)
        {
            $where[] = ['type', 'eq', $type];
        }

        $data = $this->where($where)->order($order)->page($page, $limit)->select();
        if(!$data->isEmpty())
        {
            foreach($data as $v)
            {
                $v['type'] = config('params.balance')['type'][$v['type']];
                $v['ctime'] = getTime($v['ctime']);
            }
            $result['data'] = $data;
        }
        $count = $this
            ->where($where)
            ->count();
        $result['total'] = ceil($count/$limit);
        return $result;
    }


    /**
     * 获取用户的邀请佣金
     * @param $user_id
     * @return array
     */
    public function getInviteCommission($user_id)
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => 0
        ];

        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', self::TYPE_DISTRIBUTION];
        $return['data'] = $this->where($where)->sum('money');

        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }

        return $return;
    }
}