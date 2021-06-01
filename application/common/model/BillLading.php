<?php
namespace app\common\model;
use think\model\concern\SoftDelete;

/**
 * 提货单
 * Class BillLading
 * @package app\common\model
 */
class BillLading extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    use SoftDelete;
    protected $deleteTime = 'isdel';

    const STATUS_NO = 1; //没有提货
    const STATUS_YES = 2; //已经提货


    /**
     * 添加提货单
     * @param $order_id
     * @param $store_id
     * @param $name
     * @param $mobile
     * @return array
     */
    public function addData($order_id, $store_id, $name, $mobile)
    {
        $return = error_code(10038);

        $data['id'] = $this->generateId();
        $data['order_id'] = $order_id;
        $data['store_id'] = $store_id;
        $data['name'] = $name;
        $data['mobile'] = $mobile;
        $data['status'] = self::STATUS_NO;

        $return['data'] = $this->save($data);
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '添加成功';
        }

        return $return;
    }


    /**
     * 生成唯一提货单号
     * @return string
     */
    public function generateId()
    {
        do {
            $id = get_sn(9);
            $where[] = ['id', 'eq', $id];
            $res = $this->where($where)->count();
        } while ($res != 0);

        return $id;
    }


    /**
     * 获取列表
     * @param $post
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($post, $page = 1, $limit = 20)
    {
        $return =  error_code(10025);

        $where = [];
        if($post['id'])
        {
            $where[] = ['id', 'like', '%'.$post['id'].'%'];
        }
        if($post['date'])
        {
            $date = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0].' 00:00:00', $date[1].' 23:59:59']];
        }
        if($post['order_id'])
        {
            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
        }
        if($post['store_id'])
        {
            $where[] = ['store_id', 'eq', $post['store_id']];
        }
        if($post['name'])
        {
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if($post['mobile'])
        {
            $where[] = ['mobile', 'like', '%'.$post['mobile'].'%'];
        }
        if($post['status'])
        {
            $where[] = ['status', 'eq', $post['status']];
        }

        $return['data'] = $this->where($where)
            ->page($page, $limit)
            ->select();

        $return['count'] = $this->where($where)
            ->count();

        if($return['data'] !== false)
        {
            $storeModel = new Store();
            foreach($return['data'] as &$v)
            {
                if($v['store_id'])
                {
                    $v['store_name'] = $storeModel->getStoreName($v['store_id']);
                }
                else
                {
                    $v['store_name'] = '';
                }
                if($v['status'])
                {
                    $v['status_name'] = config('params.bill_lading.status')[$v['status']];
                }
                else
                {
                    $v['status_name'] = '未知';
                }
                if($v['ctime'])
                {
                    $v['ctime'] = getTime($v['ctime']);
                }
                else
                {
                    $v['ctime'] = '';
                }
            }
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }

        return $return;
    }


    /**
     * 获取详情
     * @param $key
     * @param bool $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($key, $user_id = false)
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        $where[] = ['id|order_id|mobile', 'eq', $key];

        // 获取订单详情 可能包含多个提货单
        $data = $this->with('orderInfo,storeInfo')
            ->where($where)
            ->select();

        if (!$data->isEmpty()) {

            $data = $data->toArray();
            // 判断店员是否有权限查看该提货单信息
            if($user_id)
            {
                $clerkModel = new Clerk();
                $store_ids = $clerkModel->getClerkStoreIds($user_id);
                foreach ($data as $k => $v) {
                    if (!in_array($v['store_id'], $store_ids)) {
                        unset($data[$k]);
                    }
                }

                $data = array_values($data);
            }


            foreach ($data as &$v)
            {
                $v['status_name'] = config('params.bill_lading.status')[$v['status']];
                $v['ptime'] = $v['ptime'] ? getTime($v['ptime']) : '';
                if($v['clerk_id'])
                {
                    $userModel = new User();
                    $userInfo = $userModel->get($v['clerk_id']);
                    $v['clerk'] = $userInfo['nickname']
                        ? $userInfo['nickname'].'('.$userInfo['mobile'].')'
                        : format_mobile($userInfo['mobile']).'('.$userInfo['mobile'].')';
                }
                else
                {
                    $v['clerk'] = $v['status'] == 2 ? '(后台管理员)' : '';
                }

                //获取订单商品详情
                $orderItemsModel = new OrderItems();
                $wheres = [];
                $wheres[] = ['order_id', 'eq', $v['order_id']];
                $v['goods'] = $orderItemsModel->where($wheres)->select();

            }

            $return['data'] = $data;

        } else {
//            $return['msg'] = '提货单不存在';
//            $return['status'] = false;
            return  error_code(13312);
        }

        return $return;
    }


    /**
     * 编辑
     * @param $id
     * @param $store_id
     * @param $name
     * @param $mobile
     * @return array
     */
    public function edit($id, $store_id, $name, $mobile)
    {
        $return = error_code(10016);

        $data['store_id'] = $store_id;
        $data['name'] = $name;
        $data['mobile'] = $mobile;
        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->save($data, $where);

        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '编辑成功';
        }

        return $return;
    }


    /**
     * 删除
     * @param $id
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del($id, $user_id = false)
    {
        $return =  error_code(10023) ;
        $result = $this->get($id);
        if($result['status'] == self::STATUS_NO)
        {
//            $return['msg'] = '未提货的提货单不能删除';
            return error_code(13313);
        }

        if($user_id)
        {
            $clerkModel = new Clerk();
            $store_ids = $clerkModel->getClerkStoreIds($user_id);
            if(!in_array($result['store_id'], $store_ids))
            {
//                $return['msg'] = '你无权删除该提货单';
                return error_code(13314);
            }
        }

        $return['data'] = $this->destroy($id);
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '删除成功';
        }
        return $return;
    }


    /**
     * 订单信息
     * @return \think\model\relation\HasOne
     */
    public function orderInfo()
    {
        return $this->hasOne('Order', 'order_id', 'order_id');
    }


    /**
     * 门店信息
     * @return \think\model\relation\HasOne
     */
    public function storeInfo()
    {
        return $this->hasOne('Store', 'id', 'store_id');
    }


    /**
     * 获取店铺提货单列表
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreLadingList($user_id)
    {
        $return = error_code(10025);

        $clerkModel = new Clerk();
        $store_ids = $clerkModel->getClerkStoreIds($user_id);

        $where[] = ['store_id', 'in', $store_ids];
        $return['data'] = $this->with('orderInfo')
            ->where($where)
            ->select();

        if($return['data'] !== false)
        {
            $storeModel = new Store();
            $orderItemsModel = new OrderItems();
            foreach($return['data'] as &$v)
            {
                unset($wheres);
                $wheres[] = ['order_id', 'eq', $v['order_id']];
                $v['order_items'] = $orderItemsModel->where($wheres)->select();
                $v['store_name'] = $storeModel->getStoreName($v['store_id']);
                $v['status_name'] = config('params.bill_lading.status')[$v['status']];
                $v['ctime'] = getTime($v['ctime']);
            }
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }

        return $return;
    }


    /**
     * 提货操作
     * @param $ids
     * @param $user_id
     * @return array
     */
    public function ladingOperating($ids, $user_id)
    {
        $return = error_code(10018);

        $where[] = ['id', 'in', $ids];

        $lists = $this->where($where)->select();

        $data = [];
        foreach ($lists as $key => $val)
        {
            $data[$key]['id'] = $val['id'];
            $data[$key]['clerk_id'] = $user_id;
            $data[$key]['ptime'] = time();
            $data[$key]['status'] = self::STATUS_YES;
        }

        if (count($data) > 0) {
            $res = $this->saveAll($data);
            if($res !== false)
            {
                $return['status'] = true;
                $return['msg'] = '操作成功';
            }
        } else {
//            $return['msg'] = '没有可提货的订单';
            return error_code(13315);
        }

        return $return;
    }

    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id' => 'id',
                'desc' => '提货单号',
                'modify'=>'convertString'
            ],
            [
                'id' => 'order_id',
                'desc' => '订单号',
                'modify'=>'convertString'
            ],
            [
                'id' => 'store_name',
                'desc' => '提货门店',
            ],
            [
                'id' => 'name',
                'desc' => '提货人名',
            ],
            [
                'id' => 'mobile',
                'desc' => '提货电话',
                'modify'=>'convertString'
            ],
            [
                'id' => 'status_name',
                'desc' => '提货状态',
            ],
            [
                'id' => 'ctime',
                'desc' => '下单时间',

            ]
        ];
    }
    /**
     * 获取csv数据
     * @param $post
     * @return array
     */
    public function getCsvData($post)
    {
        $result = error_code(10083);
        $header = $this->csvHeader();
        $userData = $this->getExportList($post);

        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body = [];
            $i = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                foreach ($header as $hk => $hv) {
                    if (isset($val[$hv['id']]) && $val[$hv['id']] && isset($hv['modify'])) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) &&!empty($val[$hv['id']])) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }
            }
            $result['status'] = true;
            $result['msg'] = '导出成功';
            $result['data'] = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }
    /**
     * 导出验证
     * @param array $params
     * @return array
     */
    public function exportValidate(&$params = [])
    {
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '验证成功',
        ];
        return $result;
    }

    //导出格式
    public function getExportList($post = [])
    {
        $return =  error_code(10025);

        $where = [];
        if($post['id'])
        {
            $where[] = ['id', 'like', '%'.$post['id'].'%'];
        }
        if($post['idss'])
        {
            $where[] = ['id', 'in', $post['idss']];
        }
        if($post['order_id'])
        {
            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
        }
        if($post['store_id'])
        {
            $where[] = ['store_id', 'eq', $post['store_id']];
        }
        if($post['name'])
        {
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if($post['mobile'])
        {
            $where[] = ['mobile', 'like', '%'.$post['mobile'].'%'];
        }
        if($post['status'])
        {
            $where[] = ['status', 'eq', $post['status']];
        }
        if($post['date'])
        {
            $date = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0].' 00:00:00', $date[1].' 23:59:59']];
        }
        $return['data'] = $this->where($where)
            ->order('ctime desc')
            ->select();

        if($return['data'] !== false)
        {
            $storeModel = new Store();
            foreach($return['data'] as &$v)
            {
                if($v['store_id'])
                {
                    $v['store_name'] = $storeModel->getStoreName($v['store_id']);
                }
                else
                {
                    $v['store_name'] = '';
                }
                if($v['status'])
                {
                    $v['status_name'] = config('params.bill_lading.status')[$v['status']];
                }
                else
                {
                    $v['status_name'] = '未知';
                }
                if($v['ctime'])
                {
                    $v['ctime'] = getTime($v['ctime']);
                }
                else
                {
                    $v['ctime'] = '';
                }
                if($v['ptime'])
                {
                    $v['ptime'] = getTime($v['ptime']);
                }
                else
                {
                    $v['ptime'] = '';
                }
            }

            $return['status'] = true;
            $return['msg'] = '获取成功';
            $count = $this->where($where)->count();
            $return['count'] =$count;
        }

        return $return;
    }
}