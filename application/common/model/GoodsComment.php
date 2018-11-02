<?php
namespace app\common\model;

use think\Db;

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
    public function getList($goods_id, $page = 1, $limit = 10, $display = 'all')
    {
        $where[] = ['goods_id', 'eq', $goods_id];
        if($display != 'all')
        {
            $where[] = ['display', 'eq', $display];
        }
        $res = $this::with('user')->where($where)
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
        }

        $count = $this->where($where)
            ->count();

        if($res !== false)
        {
            $res->hidden(['goods_id','images','user_id','user'=>['id','isdel','password','status','username','ctime','utime']]);
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
            $data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => []
            ];
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
            $data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => []
            ];
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
            $return_data = [
                'status' => false,
                'msg' => '保存失败',
                'data' => $res
            ];
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
            $return_data = [
                'status' => false,
                'msg' => '修改失败',
                'data' => $res
            ];
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
            $return = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res
            ];
        }
        return $return;
    }
    /**
     * 添加评价
     * @param $order_id
     * @param $goods
     * @param $user_id
     * @return array
     */
    public function addComment($order_id, $items, $user_id)
    {
        $orderModel = new Order();
        $orderItemsModel = new OrderItems();

        //判断这个订单是否可以评价
        $res = $orderModel->isOrderComment($order_id, $user_id);
        if(!$res['status'])
        {
            //已经评价或者存在问题
            return $res;
        }


        Db::startTrans();
        try{
            //插入商品评价
            $goods_data = [];
            foreach($items as $k => $v)
            {
                //判断此条记录是否是此订单下面的
                $item_info = $orderItemsModel->where(['id'=>$k,'order_id'=>$order_id])->find();
                if(!$item_info){
                    continue;       //说明没有此条记录，就不需要评论了
                }

                $score = 5;
                if($v['score'] >= 1 &&   $v['score'] <= 5){
                    $score = $v['score'];
                }
                $images = '';
                if ($v['images'])
                {
                    foreach($v['images'] as $kk => $vv)
                    {
                        $images .= $vv['id'].',';
                    }
                }
                $images = rtrim($images, ",");
                $goods_data[] = [
                    'comment_id' => 0,
                    'score' => $score,
                    'user_id' => $user_id,
                    'goods_id' => $item_info['goods_id'],
                    'order_id' => $order_id,
                    'images' => $images,
                    'content' => $v['textarea'],
                    'addon' => $item_info['addon']
                ];
            }
            $this->saveAll($goods_data);
            //修改评价状态
            $order_data['is_comment'] = 2;
            $orderModel->save($order_data, ['order_id' => $order_id]);
            Db::commit();
            $return_data = [
                'status' => true,
                'msg' => '评价成功',
                'data' => []
            ];
        }catch(\Exception $e){
            Db::rollback();
            $return_data = [
                'status' => false,
                'msg' => '评价失败11.'.$e->getMessage(),
                'data' => $goods_data
            ];
        }
        return $return_data;
    }
}