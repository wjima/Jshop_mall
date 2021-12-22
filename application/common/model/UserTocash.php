<?php
namespace app\common\model;
use org\Curl;
use think\Db;
use think\Validate;


class UserTocash extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const TYPE_WITE = 1;          //等待审核
    const TYPE_SUCCESS = 2;        //提现成功
    const TYPE_FAIL = 3;          //提现失败

    public function tocash($user_id, $money,$bankcards_id)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        //最低提现金额
        $tocash_money_low = getSetting('tocash_money_low');
        if($money < $tocash_money_low){
            return error_code(11063,false,$tocash_money_low);
        }
        //判断历史提现金额
        $where[] = ['ctime','>=',strtotime(date('Y-m-d').' 00:00:00')];
        $where[] = ['ctime','<=',strtotime(date('Y-m-d').' 23:59:59')];
        $where[] = ["user_id","=",$user_id];
        $where[] = ["type",'neq',self::TYPE_FAIL];
        $todayMoney = $this->where($where)->sum('money');
        $todayMoney = $todayMoney + $money;//历史今天提现加上本次提现
        $tocash_money_limit = getSetting('tocash_money_limit');
        if($tocash_money_limit && $todayMoney > $tocash_money_limit){
            return error_code(11064,false,$tocash_money_limit);
        }

        $userModel = new User();
        $userInfo = $userModel->getUserInfo($user_id);
        if(!$userInfo['status']){
            return error_code(11004);
        }else{
            $userInfo = $userInfo['data'];
        }
        if($money > $userInfo['balance']){
            return error_code(11015);
        }
        // 计算提现服务费(金额)
        $cateMoney = $this->cateMoney($money);
        if (($money + $cateMoney) > $userInfo['balance']) {
            return error_code(11015);
        }

        $userBankcardsModel = new UserBankcards();
        $bankcardsInfo = $userBankcardsModel->where(['user_id'=>$user_id,'id'=>$bankcards_id])->find();
        if(!$bankcardsInfo){
            return error_code(11016);
        }

        // 当手续费低于0.01，当0.01计算
        if($cateMoney != 0 && $cateMoney < 0.01){
            $cateMoney = 0.01;
        }

        // 保留两位
        $cateMoney = round($cateMoney, 0, 2);

        $money = bcsub($money, $cateMoney, 2);

        $data['user_id'] = $user_id;
        $data['money'] = $money;
        $data['account_bank'] = $bankcardsInfo['account_bank'];
        $data['account_name'] = $bankcardsInfo['account_name'];
        $data['card_number'] = $bankcardsInfo['card_number'];
        $data['bank_name'] = $bankcardsInfo['bank_name'];
        $data['bank_area_id'] = $bankcardsInfo['bank_area_id'];
        $data['bank_code'] = $bankcardsInfo['bank_code'];
        $data['withdrawals'] = $cateMoney;
        $re = $this->save($data);

        if($re){
            $balanceModel = new Balance();
            return $balanceModel->change($user_id,$balanceModel::TYPE_TOCASH,$money,$this->id, $cateMoney);
        }else{
            return error_code(11065);
        }
    }
    //提现审核
    public function examine($id,$type){
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        $where['id'] = $id;
        $where['type'] = self::TYPE_WITE;
        $info = $this->where($where)->find();
        if(!$info){
            return error_code(11066);
        }
        if(isset(config('params.user_tocash.type')[$type])){
            $res = $this->save(['type'=>$type],$where);
            $result['status'] = true;
            $result['data'] = $type;
            if($res !== false)
            {

                //失败给用户退钱到余额
                if($type == self::TYPE_FAIL)
                {
                    $tocash = $this->get($id);
                    $userModel = new User();
                    $userWhere[] = ['id', 'eq', $tocash['user_id']];
                    // 提现金额 加 服务费返还
                    $r = $userModel->where($userWhere)->inc('balance',$tocash['money'] + $tocash['withdrawals'])->update();
                    if($r !== false)
                    {
                        //添加记录
                        $newUserInfo = $userModel->get($tocash['user_id']);
                        $balanceModel = new Balance();
                        $balanceData = [
                            'user_id' => $tocash['user_id'],
                            'type' => $balanceModel::TYPE_TOCASH,
                            'money' => $tocash['money'] + $tocash['withdrawals'],
                            'balance' => $newUserInfo['balance'],
                            'source_id' => $id,
                            'memo' => '提现驳回退款'.$tocash['money'].'元',
                            'ctime' => time()
                        ];
                        $balanceModel->save($balanceData);
                    }
                }
            }
            return $result;
        }else{
            return error_code(10008);
        }
    }


    /**
     *
     *  提现服务费(金额)
     * @param $tocashMoney
     * @return float|int
     */
    protected function cateMoney ($tocashMoney)
    {
        $cate = getSetting('tocash_money_rate');

        $cateMoney = $tocashMoney * ($cate / 100);

        return $cateMoney;
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
//        $re[ 'sql' ] = $this->getLastSql();

        return $re;
    }

    protected function tableWhere( $post )
    {
        $where = [];
        if ( isset($post['user_id']) && $post['user_id'] != "" ) {
            $where[] = ['user_id', 'eq', $post['user_id'] ];
        } else {
            if ( isset($post['mobile']) && $post['mobile'] != "" ) {
                $user_id = get_user_id($post['mobile']);
                if ( $user_id ) {
                    $where[] = [ 'user_id', 'eq', $user_id ];
                } else {
                    $where[] = [ 'user_id', 'eq', '99999999' ];       //如果没有此用户，那么就赋值个数值，让他查不出数据
                }
            }

        }

        if ( isset($post['type']) && $post['type'] != "" ) {
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
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if ($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }
            if ($v['type']) {
                $list[$k]['type'] = config('params.user_tocash')['type'][$v['type']];
            }
            if ($v['user_id']) {
                $list[$k]['mobile'] = get_user_info($v['user_id']);
            }
//            if($v['card_number']){
//                $list[$k]['card_number'] = bankCardNoFormat($v['card_number']);
//            }
        }
        return $list;
    }


    /**
     *
     *  获取用户提现记录列表
     * @param $user_id
     * @param $page
     * @param $limit
     * @param string $type  类型
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userToCashList($user_id, $page, $limit, $type='')
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $where = [];
        if (isset($type) && !empty($type)) {
            $where[] = ['type', 'eq', $type];
        }
        $where[] = ['user_id', 'eq', $user_id];
        $list = $this->where($where)->order('ctime desc')->page($page, $limit)->select();
        $count = $this->where($where)->count();

        if (!$list->isEmpty()) {
            foreach ($list as $v) {
                $v['type'] = config('params.user_tocash')['type'][$v['type']];
                $v['card_number'] = bankCardNoFormat($v['card_number']);
                $v['ctime'] = getTime($v['ctime']);
            }
            $result[ 'data' ] = $list;
            $result[ 'total' ] = ceil($count/$limit);
        }
        return $result;
    }
    public function userInfo()
    {
        return $this->hasOne('User','id','user_id')->bind([
            'nickname'
        ]);
    }
}