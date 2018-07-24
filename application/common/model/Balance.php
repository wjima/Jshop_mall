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
    const TYPE_LIQUIDATION = 2;         //清算，对应支付单表     共享商户收款后以这个状态增加到账户余额上去
    const TYPE_REFUND = 3;              //用户用余额支付的话，退款类型，对应1
    const TYPE_LREFUND = 4;             //商户的退款，钱要从余额里扣掉,余额不足，退款失败，对应2
    const TYPE_RECHARGE = 5;            //充值
    const TYPE_TOCASH = 6;              //提现

    //充值
    public function recharge()
    {

    }

    //其他的余额变化

    /**余额变动记录
     *
     * @param $user_id          当前用户id,当是店铺的时候，取店铺创始人的user_id
     * @param $type             类型
     * @param $money            金额，永远是正的
     * @param string $source_id 资源id
     *
     * @return array
     */
    public function change( $user_id, $type, $money, $source_id = "" )
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        //取用户实际余额
        $userModel = new User();
        $userInfo = $userModel::withTrashed()->where([ 'id' => $user_id ])->find();
        if ( !$userInfo ) {
            return error_code(11004);
        }

        //取描述，并简单校验
        $re = $this->getMemo($type, $money, $source_id);
        if ( !$re[ 'status' ] ) {
            return $re;
        }
        $memo = $re[ 'data' ];

        (float)$money = $money;
        //如果是减余额的操作，还是加余额操作
        if (
            $type == self::TYPE_PAY ||
            $type == self::TYPE_REFUND ||
            $type == self::TYPE_TOCASH
        ) {
            $money = - $money;
        }
        $balance = $userInfo[ 'balance' ] + $money;
        if ( ( $balance ) < 0 ) {
            return error_code(11007);
        }
        Db::startTrans();
        try {
            $data[ 'user_id' ] = $user_id;
            $data[ 'type' ] = $type;
            $data[ 'money' ] = $money;
            $data[ 'balance' ] = $balance;
            $data[ 'source_id' ] = $source_id;
            $data[ 'memo' ] = $memo;
            $data[ 'ctime' ] = time();
            $this->save($data);
            //上面保存好主体表，下面保存明细表
            $userInfo->balance = $balance;
            $userInfo->save();

            Db::commit();
        } catch ( \Exception $e ) {
            Db::rollback();
            $result[ 'msg' ] = $e->getMessage();
            return $result;
        }
        $result[ 'status' ] = true;
        return $result;
    }

    /**
     * 取得描述，并做简单校验
     *
     * @param $type
     * @param $money
     * @param string $source_id
     *
     * @return array|\think\Config
     */
    private function getMemo( $type, $money, $source_id = "" )
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        switch ( $type ) {
            case self::TYPE_PAY:
                $result[ 'data' ] = '消费了' . $money . '元';
                break;
            case self::TYPE_LIQUIDATION:
                $result[ 'data' ] = '平台清算了' . $money . '元，支付单号：' . $source_id;
                break;
            case self::TYPE_REFUND:
                $result[ 'data' ] = '收到了退款' . $money . '元';
                break;
            case self::TYPE_LREFUND:
                $result[ 'data' ] = '平台订单退款' . $money . '元，退款单号：' . $source_id;
                break;
            case self::TYPE_RECHARGE:
                $result[ 'data' ] = '充值了' . $money . '元';
                break;
            case self::TYPE_TOCASH:
                $result[ 'data' ] = '提现了' . $money . '元';
                break;
            default:
                return error_code(10000);
        }
        //::todo    这里还可以做一些其他的校验
        return $result;
    }

    /**
     * 返回layui的table所需要的格式
     *
     * @author sin
     *
     * @param $post
     *
     * @return mixed
     */
    public function tableData( $post )
    {
        if ( isset($post[ 'limit' ]) ) {
            $limit = $post[ 'limit' ];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this::with('userInfo')->field($tableWhere[ 'field' ])->where($tableWhere[ 'where' ])->order($tableWhere[ 'order' ])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re[ 'code' ] = 0;
        $re[ 'msg' ] = '';
        $re[ 'count' ] = $list->total();
        $re[ 'data' ] = $data;
        $re[ 'sql' ] = $this->getLastSql();

        return $re;
    }

    protected function tableWhere( $post )
    {
        $where = [];
        if ( isset($post[ 'user_id' ]) && $post[ 'user_id' ] != "" ) {
            $where[] = [ 'user_id', 'eq', $post[ 'user_id' ] ];
        } else {
            if ( isset($post[ 'mobile' ]) && $post[ 'mobile' ] != "" ) {
                if ( $user_id = get_user_id($post[ 'mobile' ]) ) {
                    $where[] = [ 'user_id', 'eq', $user_id ];
                } else {
                    $where[] = [ 'user_id', 'eq', '99999999' ];       //如果没有此用户，那么就赋值个数值，让他查不出数据
                }
            }

        }
        if ( isset($post[ 'type' ]) && $post[ 'type' ] != "" ) {
            $where[] = [ 'type', 'eq', $post[ 'type' ] ];
        }
        $result[ 'where' ] = $where;
        $result[ 'field' ] = "*";
        $result[ 'order' ] = 'ctime desc';
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     *
     * @author sin
     *
     * @param $list  array格式的collection
     *
     * @return mixed
     */
    protected function tableFormat( $list )
    {
        foreach ( $list as $k => $v ) {
            if ( $v[ 'ctime' ] ) {
                $list[ $k ][ 'ctime' ] = getTime($v[ 'ctime' ]);
            }
            if ( $v[ 'type' ] ) {
                $list[ $k ][ 'type' ] = config('params.balance')[ 'type' ][ $v[ 'type' ] ];
            }
        }
        return $list;
    }

    //关联用户信息
    public function userInfo()
    {
        return $this->hasOne('User', 'id', 'user_id')->bind([
            'mobile',
            'nickname'
        ]);
    }


    /**
     *
     *  获取用户的余额明细记录
     * @param $user_id
     * @param string $order
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBalanceList( $user_id, $order='', $page=1, $limit=10 )
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [ ]
        ];

        $data = $this->where('user_id', $user_id)->order($order)->page($page, $limit)->select();
        if (!$data->isEmpty()) {
            foreach ( $data as $v ) {
                $v[ 'type' ] = config('params.balance')[ 'type' ][ $v[ 'type' ] ];
                $v[ 'ctime' ] = getTime( $v[ 'ctime' ] );
            }
            $result['data'] = $data;
        }
        $count = $this
            ->where('user_id', $user_id)
            ->count();
        $result['total'] = ceil( $count/$limit );
        return $result;
    }

}