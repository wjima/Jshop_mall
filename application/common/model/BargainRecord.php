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
    const STATUS_CANCLE = '5';//活动取消

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
        $tableWhere  = $this->tableWhere($post);
        $list        = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data        = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
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
        if (isset($post['bargain_id']) && $post['bargain_id'] != "") {
            $where[] = ['bargain_id', 'eq', $post['bargain_id']];
        }
        if (isset($post['mobile']) && $post['mobile'] != "") {
            $user_id = get_user_id($post['mobile']);
            if ($user_id) {
                $where[] = ['user_id', 'eq', $user_id];
            } else {
                $where[] = ['user_id', 'eq', 99999999];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['ctime' => 'desc'];
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
            $list[$k]['nickname']    = get_user_info($v['user_id'], 'nickname');
            if ($v['status'] == self::STATUS_ING) {
                $list[$k]['lasttime'] = secondConversionArray($v['etime'] - time());
            }
            $list[$k]['stime']       = getTime($v['stime']);
            $list[$k]['etime']       = getTime($v['etime']);
            $list[$k]['ctime']       = getTime($v['ctime']);
            $list[$k]['utime']       = getTime($v['utime']);
            $list[$k]['status_name'] = config('params.bargain')['status'][$v['status']];
            $list[$k]['avatar']      = _sImage(get_user_info($v['user_id'], 'avatar'));
           
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
//            $result['msg'] = '参数错误';
            return error_code(10051);
        }
        $info = $this->where([['bargain_id', '=', $bargain_id], ['user_id', '=', $user_id], ['status', 'in', [self::STATUS_ING, self::STATUS_SUCCESS]]])->field('id')->find();
        if ($info) {
            $result['data'] = $info['id'];
            $result['msg']  = error_code(17637,true); //'您有正在进行中的砍价，请勿重复参加';
            return $result;
        }
        if (!$this->countRecord($bargain_id)) {
            $result['msg']          = error_code(17638,true);//'活动数量已满，请看看其它活动吧';
            $result['data']['code'] = 'over';
            return $result;
        }
        $bargainModel = new Bargain();
        $info         = $bargainModel->get($bargain_id);

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
        if($info['significant_interval']){
            $recData['etime']       = time() + $info['significant_interval'] * 3600;
        }else{
            $recData['etime']       = $info['etime'];
        }
        if($recData['stime'] > $recData['etime']){
            return error_code(17640);
        }

        if (!$this->save($recData)) {
            $result['msg'] = error_log(17618,true);//'发起砍价活动失败';
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

    /**
     * 取消砍价
     * @param $record_id
     * @param int $user_id
     * @return array
     */
    public function cancleBargain($record_id, $user_id = 0)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        if (!$record_id || !$user_id) {
//            $result['msg'] = '取消失败';
            return error_code(10051);
        }
        $recordModel = new BargainRecord();
        $info        = $recordModel->where([['user_id', '=', $user_id], ['id', '=', $record_id], ['status', '=', self::STATUS_ING]])->find();
        if (!$info) {
//            $result['msg'] = '无砍价活动记录，取消失败';
            return error_code(17612);
        }
        $res              = $this->where([['id', '=', $record_id]])->update(['status' => self::STATUS_CANCLE]);
        $result['status'] = true;
        $result['msg']    = '取消成功';
        return $result;
    }

    /***
     * 统计是否可以参加活动
     * @param int $bargain_id
     * @return bool
     */
    public function countRecord($bargain_id = 0)
    {
        $count        = $this->where([['bargain_id', '=', $bargain_id], ['status', 'not in', [4, 5]]])->count();
        $bargainModel = new Bargain();
        $info         = $bargainModel->field('max_goods_nums,status,goods_id')->get($bargain_id);
        if ($info['max_goods_nums'] == 0) {
            $productModel           = new Products();
            $product                = $productModel->where([['goods_id', '=', $info['goods_id']], ['is_defalut', '=', $productModel::DEFALUT_YES]])->cache(86400)->find();
            $info['max_goods_nums'] = $product['stock'] - $product['freeze_stock'];
            $info['max_goods_nums'] = ($info['max_goods_nums'] > 0) ? $info['max_goods_nums'] : 0;
        }
        if ($info && $info['status'] == $bargainModel::STATUS_ON && $info['max_goods_nums'] && $info['max_goods_nums'] > $count) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 过期的活动状态取消
     *
     */
    public function bargainCancle()
    {
        $current_time = time();
        $where        = [];
        $where[]      = ['etime', '<', $current_time];
        $where[]      = ['status', 'in', [self::STATUS_ING, self::STATUS_END]];
        $this->where($where)->update(['status' => self::STATUS_CANCLE]);
    }

}

