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
        $return = [
            'status' => false,
            'msg' => '添加失败',
            'data' => ''
        ];

        $data['id'] = get_sn(9);
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
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => [],
            'count' => 0
        ];

        $where = [];
        if($post['id'])
        {
            $where[] = ['id', 'like', '%'.$post['id'].'%'];
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
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        $where[] = ['id|order_id|mobile', 'eq', $key];
        $return['data'] = $this->with('orderInfo,storeInfo')
            ->where($where)
            ->find();
        if($user_id)
        {
            $clerkModel = new Clerk();
            $store_ids = $clerkModel->getClerkStoreIds($user_id);
            if(!in_array($return['data']['store_id'], $store_ids))
            {
                $return['data'] = [];
                $return['msg'] = '你无权查看该提货单';
                return $return;
            }
        }

        if($return['data'] !== false)
        {
            $return['data']['status_name'] = config('params.bill_lading.status')[$return['data']['status']];
            $return['data']['ptime'] = $return['data']['ptime']?getTime($return['data']['ptime']):'';
            if($return['data']['clerk_id'])
            {
                $userModel = new User();
                $userInfo = $userModel->get($return['data']['clerk_id']);
                $return['data']['clerk'] = $userInfo['nickname']?$userInfo['nickname'].'('.$userInfo['mobile'].')':format_mobile($userInfo['mobile']).'('.$userInfo['mobile'].')';
            }
            else
            {
                $return['data']['clerk'] = '后台管理员';
            }

            //获取订单商品详情
            $orderItemsModel = new OrderItems();
            $wheres[] = ['order_id', 'eq', $return['data']['order_id']];
            $return['data']['goods'] = $orderItemsModel->where($wheres)->select();

            $return['status'] = true;
            $return['msg'] = '获取成功';
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
        $return = [
            'status' => false,
            'msg' => '编辑失败',
            'data' => ''
        ];

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
        $return = [
            'status' => false,
            'msg' => '删除失败',
            'data' => ''
        ];
        $result = $this->get($id);
        if($result['status'] == self::STATUS_NO)
        {
            $return['msg'] = '未提货的提货单不能删除';
            return $return;
        }

        if($user_id)
        {
            $clerkModel = new Clerk();
            $store_ids = $clerkModel->getClerkStoreIds($user_id);
            if(!in_array($result['store_id'], $store_ids))
            {
                $return['msg'] = '你无权删除该提货单';
                return $return;
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
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];

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
     * @param $id
     * @param $user_id
     * @return array
     */
    public function ladingOperating($id, $user_id)
    {
        $return = [
            'status' => false,
            'msg' => '操作失败',
            'data' => ''
        ];

        $data['clerk_id'] = $user_id;
        $data['ptime'] = time();
        $data['status'] = self::STATUS_YES;
        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->save($data, $where);

        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '操作成功';
        }
        return $return;
    }
}