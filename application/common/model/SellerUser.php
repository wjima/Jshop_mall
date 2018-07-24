<?php
namespace app\common\model;

/**
 * 店铺和用户相关信息表
 * Class SellerUser
 * @package app\common\model
 */
class SellerUser extends Common
{
    const STATUS_NORMAL = 1;        //用户状态 正常
    const STATUS_DISABLE = 2;       //用户状态 停用

//    const SEX_BOY = 1;
//    const SEX_GIRL = 2;
//    const SEX_OTHER = 3;

    /**
     * 用户信息
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne('User','id','user_id');
    }

    /**
     * 获取店铺用户信息
     * @param $user_id
     * @param $seller_id
     * @param string $field
     * @return array|null|\PDOStatement|string|\think\Model|static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($user_id, $seller_id, $field = '*')
    {
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['seller_id', 'eq', $seller_id];
        $info = $this->field($field)
            ->where($where)
            ->find();
        if(!$info)
        {
            //没有信息创建信息
            $info = $this->addInfo($user_id, $seller_id);
        }
        return $info;
    }


    /**
     * 判断是否存在
     * @param $user_id
     * @param $seller_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isExist($user_id, $seller_id)
    {
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['seller_id', 'eq', $seller_id];
        $res = $this->where($where)
            ->find();
        return $res;
    }


    /**
     * 添加用户和商家对应信息并返回
     * @param $user_id
     * @param $seller_id
     * @return SellerUser|array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addInfo($user_id, $seller_id,$pid=0)
    {
        $res = $this->isExist($user_id, $seller_id);
        if($res)
        {
            return $res;
        }
        else
        {
            $data['user_id'] = $user_id;
            $data['seller_id'] = $seller_id;
            $data['pid'] = $pid;
            $id = $this->insertGetId($data);
            $r = $this->get($id);
            return $r;
        }
    }


    /**
     * 获取全部
     * @param $seller_id
     * @param int $page
     * @param int $limit
     * @param $user_where
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllInfo($seller_id, $page = 1, $limit = 20, $user_where)
    {
        $where[] = ['s.seller_id', 'eq', $seller_id];
        if($user_where['mobile'])
        {
            $where[] = ['u.mobile', 'like', '%'.$user_where['mobile'].'%'];
        }
        if($user_where['sex'])
        {
            $where[] = ['u.sex', 'eq', $user_where['sex']];
        }
        if($user_where['birthday'])
        {
            $where[] = ['u.birthday', 'eq', $user_where['birthday']];
        }
        if($user_where['nickname'])
        {
            $where[] = ['u.nickname', 'eq', $user_where['nickname']];
        }
        if($user_where['status'])
        {
            $where[] = ['u.status', 'eq', $user_where['status']];
        }

        $data['data'] = $this->alias('s')
            ->join('user u', 's.user_id = u.id', 'left')
            ->where($where)
            ->page($page, $limit)
            ->select();

        $data['count'] = $this->alias('s')
            ->join('user u', 's.user_id = u.id', 'left')
            ->where($where)
            ->count();
        return $data;
    }


    /**
     * 获取数据
     * @param $input
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adminPointList($input)
    {
        $data = $this->getAllInfo($input['seller_id'], $input['page'], $input['limit'], $input);
        $return_data = [
            'status' => true,
            'count' => $data['count'],
        ];
        if($data['count'] > 0)
        {
            foreach($data['data'] as &$v)
            {
                $v['sex'] = config('params.user')['sex'][$v['sex']];
                $v['status'] =config('params.user')['status'][$v['status']];
            }
            $return_data['data'] = $data['data'];
            $return_data['msg'] = '查询成功';
        }
        else
        {
            $return_data['msg'] = '暂无数据';
        }

        return $return_data;
    }


    /**
     * 判断用户积分是否满足
     * @param $user_id
     * @param $seller_id
     * @param $num
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isPointBalance($user_id, $seller_id, $num)
    {
        $return = [
            'status' => false,
            'msg' => '用户积分余额不足'
        ];

        $info = $this->getInfo($user_id, $seller_id, 'point');
        if($info['point'] - $num >= 0)
        {
            $return['status'] = true;
            $return['msg'] = '用户积分余额充值';
        }

        return $return;
    }


    /**
     * 订单最大使用多少积分
     * @param $seller_id
     * @param $user_id
     * @param $money
     * @param int $point
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyByPoint($seller_id, $user_id, $money, $point = 0)
    {
        $return = [
            'status' => false,
            'msg' => '',
            'data' => [
                'point' => $point,
                'money' => 0
            ]
        ];
        $point_discounted_proportion = getShopSetting($seller_id, 'point_discounted_proportion');
        $orders_point_proportion = getShopSetting($seller_id, 'orders_point_proportion');

        //这个订单可以用积分兑换多少钱
        $max_money = $money*($orders_point_proportion/100);
        //这个钱等于多少积分
        $max_point = $point_discounted_proportion*$max_money;

        //这个用户有多少积分
        $user_point = $this->getInfo($user_id, $seller_id, 'point');

        if($point > $max_point)
        {
            $return['msg'] = '使用的积分大于这比订单最大允许使用的积分';
            return $return;
        }

        if($point > $user_point)
        {
            $return['msg'] = '使用的积分大于账号积分余额';
            return $return;
        }

        if($point != 0)
        {
            $return['data']['money'] = sprintf("%.2f", $point/$point_discounted_proportion);
        }
        else
        {
            $u_point = $user_point>$max_point ? $max_point : $user_point;
            $return['data']['money'] = sprintf("%.2f", $u_point/$point_discounted_proportion);
        }

        $return['status'] = true;
        return $return;
    }
}