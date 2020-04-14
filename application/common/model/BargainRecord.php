<?php

namespace app\common\model;

use think\Validate;
use think\Model;

//砍价活动订单表
class BargainRecord extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const STATUS_ING = '1';//进行中
    const STATUS_SUCCESS = '2';//砍价成功
    const STATUS_HAVE_ORDER = '3';//已下单
    const STATUS_END = '4';//未支付砍价结束

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
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;

        return $re;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {

        $where = [];
        if (isset($post['order_id']) && $post['order_id'] != "") {
            $where[] = ['order_id', 'eq', $post['order_id']];
        }

        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            $list[$k]['nickname'] = get_user_info($v['user_id'], 'nickname');
            $list[$k]['ctime']    = getTime($v['ctime']);
            $list[$k]['utime']    = getTime($v['utime']);
            $list[$k]['avatar']   = _sImage(get_user_info($v['user_id'], 'avatar'));
        }
        return $list;
    }

    /***
     * 参加砍价活动
     * @param $bargain_id
     * @param int $user_id
     * @return array
     */
    public function addRecord($bargain_id, $user_id = 0)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => [],
        ];
        if (!$bargain_id || !$user_id) {
            $result['msg'] = '参数错误';
            return $result;
        }
        $info = $this->where([['bargain_id', '=', $bargain_id, 'user_id', '=', $user_id], ['status', '=', self::STATUS_ING]])->field('id')->find();
        if ($info) {
            $result['data'] = $info['id'];
            $result['msg']  = '您有正在进行中的砍价，请勿重复参加';
            return $result;
        }
        $bargainModel           = new Bargain();
        $info                   = $bargainModel->get($bargain_id);
        $recData['user_id']     = $user_id;
        $recData['bargain_id']  = $bargain_id;
        $recData['status']      = self::STATUS_ING;
        $recData['name']        = $info['name'];
        $recData['goods_id']    = $info['goods_id'];
        $productModel           = new Products();
        $product                = $productModel->field('id')->where([['goods_id', '=', $info['goods_id']]])->find();
        $recData['product_id']  = $product['id'];
        $recData['goods_name']  = get_goods_info($info['goods_id'], 'name');
        $recData['image_url']   = get_goods_info($info['goods_id'], 'image_id');
        $recData['start_price'] = $info['start_price'];
        $recData['end_price']   = $info['end_price'];
        $recData['price']       = $info['start_price'];
        $recData['stime']       = time();
        $recData['etime']       = time() + $info['significant_interval'] * 3600;
        if (!$this->save($recData)) {
            $result['msg'] = '发起砍价活动失败';
            return $result;
        }
        $result['data']   = $this->id;
        $result['status'] = true;
        $result['msg']    = '参加成功';
        return $result;
    }


    /**
     * 更新记录
     * @param int $record_id
     * @param array $data
     * @return int|string
     */
    public function updateRecord($record_id = 0, $data = [])
    {
        return $this->where([['id', '=', $record_id]])->update($data);
    }

    /**
     * 获取参与记录
     * @param string $fields
     * @param array $where
     * @param array $order
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getList($fields = '*', $where = [], $order = ['ctime' => 'desc'], $page = 1, $limit = 10)
    {
        $result                  = [
            'status' => true,
            'msg'    => '',
            'data'   => [],
        ];
        $list                    = $this
            ->field($fields)
            ->where($where)
            ->order($order)
            ->page($page, $limit)
            ->select();
        $total                   = $this
            ->field($fields)
            ->where($where)
            ->count();
        $list                    = $this->tableFormat($list);
        $result['data']['list']  = $list;
        $result['data']['count'] = $total;
        return $result;
    }
}

