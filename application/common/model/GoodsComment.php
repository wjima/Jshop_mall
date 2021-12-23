<?php
namespace app\common\model;

use think\Db;
use think\facade\Cache;

/**
 * 商品评价表
 * Class GoodsComment
 * @package app\common\model
 */
class GoodsComment extends Common
{
    const DISPLAY_SHOW = 1;
    const DISPLAY_HIDE = 2;

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';

    /**
     * 获取用户信息
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne('User','id','user_id');
    }


    /**
     * 商品表
     * @return \think\model\relation\HasOne
     */
    public function goods()
    {
        return $this->hasOne('Goods','id','goods_id');
    }


    /**
     * 获取商品评价
     * @param $goods_id
     * @param int $page
     * @param int $limit
     * @param string $display
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($goods_id, $page = 1, $limit = 10, $display = 'all',$order = 'ctime')
    {
        $where[] = ['goods_id', 'eq', $goods_id];
        if ($display != 'all') {
            $where[] = ['display', 'eq', $display];
        }
        if ($order == 'score') {
            $order = "score desc";
        } else {
            $order = "ctime desc";
        }
        $res = $this::with('user')->where($where)
            ->order('ctime desc')
            ->order($order)
            ->page($page, $limit)
            ->select();
        foreach ($res as $k => $v) {
            if ($v['user']) {
                $res[$k]['user']['avatar'] = _sImage($v['user']['avatar']);
                $res[$k]['user']['mobile'] = format_mobile($v['user']['mobile']);
            } else {
                $res[$k]['user'] = [
                    'avatar'   => _sImage(),
                    'nickname' => '匿名用户',
                    'id'       => 0,
                ];
            }
            if ($v['images']) {
                $imagesArr = explode(',', $v['images']);
                if (count($imagesArr) > 0) {
                    foreach ($imagesArr as $kk => &$vv) {
                        $vv = _sImage($vv);
                    }
                    $res[$k]['images_url'] = $imagesArr;
                }
            }
            if(!$v['addon']){       //nullb变成空,否则前端显示有偶问题。
                $res[$k]['addon'] = "";
            }
        }
        $count = $this->where($where)
            ->count();

        if ($res !== false) {
            $res->hidden(['goods_id', 'images', 'user_id', 'user' => ['id', 'isdel', 'password', 'status', 'username', 'ctime', 'utime','balance','point','pid']]);
            $data = [
                'status' => true,
                'msg'    => '获取成功',
                'data'   => [
                    'list'  => $res,
                    'count' => $count,
                    'page'  => $page,
                    'limit' => $limit
                ]
            ];
        } else {
            $data = error_code(10025);
        }
        return $data;
    }


    /**
     * 获取商家全部的评论列表
     * @param int $page
     * @param int $limit
     * @param string $order_id
     * @param string $evaluate
     * @param string $display
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListComments($page = 1, $limit = 10, $order_id = '', $evaluate = 'all', $display = 'all', $mobile = false)
    {
        if($order_id != '')
        {
            $where[] = ['order_id', 'eq', $order_id];
        }
        if($evaluate != 'all')
        {
            $where[] = ['score', 'eq', $evaluate];
        }
        if($display != 'all')
        {
            $where[] = ['display', 'eq', $display];
        }
        if($mobile)
        {
            $where[] = ['user_id', 'eq', get_user_id($mobile)];
        }

        $res = $this::with('user,goods')->where($where)
            ->order('ctime desc')
            ->page($page, $limit)
            ->select();
        foreach($res as $k => &$v)
        {
            $imagesArr = explode(',', $v['images']);
            foreach($imagesArr as $kk => &$vv)
            {
                $vv = _sImage($vv);
            }

            $v['images_url'] = $imagesArr;
            $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
        }

        $count = $this->where($where)
            ->count();

        if($res !== false)
        {
            $res->hidden(['images','user'=>['id','isdel','password','status','username','ctime','utime']]);
            $data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => [
                    'list' => $res,
                    'count' => $count,
                    'page' => $page,
                    'limit' => $limit
                ]
            ];
        }
        else
        {
            $data = error_code(10025);
        }
        return $data;
    }


    /**
     * 商家回复
     * @param $id
     * @param $content
     * @return array
     */
    public function sellerComment($id, $content)
    {
        $where[] = ['id', 'eq', $id];
        $data['seller_content'] = $content;
        $res = $this->where($where)->update($data);
        if($res !== false)
        {
            $return_data = [
                'status' => true,
                'msg' => '保存成功',
                'data' => $res
            ];
        }
        else
        {
            $return_data = error_code(10004);
        }
        return $return_data;
    }


    /**
     * 修改评价显示
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setDisplay($id)
    {
        $where[] = ['id', 'eq', $id];
        $res = $this->where($where)->find();
        $data['display'] = $res['display']==1?2:1;
        $result = $this->where($where)->update($data);
        if($result !== false)
        {
            $return_data = [
                'status' => true,
                'msg' => '修改成功',
                'data' => $res
            ];
        }
        else
        {
            $return_data = error_code(10024);
        }
        return $return_data;
    }


    /**
     * 获取单条评价
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCommentInfo($id)
    {
        $where[] = ['id', 'eq', $id];
        $res = $this->where($where)->find();
        if($res)
        {
            $return = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        }
        else
        {
            $return = error_code(10025);
        }
        return $return;
    }

    /**
     * 添加评价
     * @param $order_id
     * @param $items
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addComment($order_id, $items, $user_id)
    {
        $lock_key = 'user_add_comment_' . $user_id;//防止高并发重复问题
        if (!Cache::has($lock_key)) {
            Cache::set($lock_key, '1', 3);

            $orderModel      = new Order();
            $orderItemsModel = new OrderItems();

            //判断这个订单是否可以评价
            $res = $orderModel->isOrderComment($order_id, $user_id);
            if (!$res['status']) {
                Cache::rm($lock_key);
                //已经评价或者存在问题
                return $res;
            }

            Db::startTrans();
            try {
                //插入商品评价
                $goods_data = $gid = [];
                foreach ($items as $k => $v) {
                    //判断此条记录是否是此订单下面的
                    $item_info = $orderItemsModel->where(['id' => $k, 'order_id' => $order_id])->find();
                    if (!$item_info) {
                        continue;       //说明没有此条记录，就不需要评论了
                    }

                    //如果是赠品，就跳过
                    if (strstr($item_info['name'], $orderModel::GIVEAWAY_STR) !== false) {
                        continue;
                    }


                    $score = 5;
                    if ($v['score'] >= 1 && $v['score'] <= 5) {
                        $score = $v['score'];
                    }
                    $images = '';
                    if ($v['images']) {
                        foreach ($v['images'] as $kk => $vv) {
                            $images .= $vv['id'] . ',';
                        }
                    }
                    $images       = rtrim($images, ",");
                    $goods_data[] = [
                        'comment_id' => 0,
                        'score'      => $score,
                        'user_id'    => $user_id,
                        'goods_id'   => $item_info['goods_id'],
                        'product_id' => $item_info['product_id'],   // 货品ID
                        'order_id'   => $order_id,
                        'images'     => $images,
                        'content'    => htmlentities($v['textarea']),
                        'name'       => $item_info['name'],       // 商品名称
                        'addon'      => $item_info['addon']
                    ];

                    if (isset($gid[$item_info['goods_id']])) {
                        $gid[$item_info['goods_id']] += 1;
                    } else {
                        $gid[$item_info['goods_id']] = 1;
                    }
                }
                $this->saveAll($goods_data);
                //商品表更新评论数量
                foreach ($gid as $goods_id => $inc) {
                    $goodsModel = new Goods();
                    $goodsModel->where('id', $goods_id)->setInc('comments_count', $inc);
                }
                //修改评价状态
                $order_data['is_comment'] = 2;
                $orderModel->save($order_data, ['order_id' => $order_id]);
                Db::commit();
                $orderLog = new OrderLog();
                $orderLog->addLog($order_id, $user_id, $orderLog::LOG_TYPE_EVALUATION, '用户评价订单', $items);
                $return_data = [
                    'status' => true,
                    'msg'    => '评价成功',
                    'data'   => []
                ];
                Cache::rm($lock_key);
            } catch (\Exception $e) {
                Cache::rm($lock_key);
                Db::rollback();
                $return_data = [
                    'status' => false,
                    'msg'    => $e->getMessage(),
                    'data'   => $goods_data
                ];
            }
        } else {
            $return_data['msg'] = '请勿重复提交';
        }
        return $return_data;
    }


    /**
     * 获取商品评价数量
     * @param $goods_id
     * @return float|int|string
     */
    public function getCommentCount($goods_id)
    {
        $where[] = ['goods_id', 'eq', $goods_id];
        $num = $this->where($where)->count();
        return $num?$num:0;
    }

    
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
        if (isset($post['page'])) {
            $page = $post['page'];
        } else {
            $page = 1;
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this
            ->field($tableWhere['field'])
            ->where($tableWhere['where'])
            ->order($tableWhere['order'])
            ->page($page, $limit)
            ->select();
        $count = $this
            ->where($tableWhere['where'])
            ->count();

        $data = $this->tableFormat($list->toArray());

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $count;
        $re['data'] = $data;

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
        if (isset($post['goods_id']) && $post['goods_id'] != "" && $post['goods_id'] != "0") {
            $where[] = ['goods_id', '=',  $post['goods_id']];
        }

        if (isset($post['goods_name']) && $post['goods_name'] != "") {
            $where[] = ['name', 'like',  '%' . $post['goods_name'] . '%'];
        }
        if (isset($post['order_id']) && $post['order_id'] != "") {
            $where[] = ['order_id', 'like',  '%' . $post['order_id'] . '%'];
        }
        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['display', '=',  $post['status'] ];
        }
        if (isset($post['mobile']) && $post['mobile'] != "") {
            $userModel = new User();
            $user_ids = $userModel->where('username|nickname|mobile', 'like', '%' . $post['mobile'] . '%')->column('id');
            if ($user_ids) {
                $where[] = ['user_id', 'in',  $user_ids];
            }
        }
        if (!empty($post['date'])) {
            $date_string = $post['date'];
            $date_array = explode(' 到 ', urldecode($date_string));
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $where[] = ['ctime', ['>=', $sdate], ['<=', $edate], 'and'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "ctime desc";
        return $result;
    }
    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list //array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => &$v) {
            $imagesArr = explode(',', $v['images']);
            foreach ($imagesArr as $kk => &$vv) {
                $vv = _sImage($vv);
            }

            $v['images_url'] = $imagesArr;
            $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
            $v['username'] = get_user_info($v['user_id'], 'showname');
        }
        return $list;
    }
}