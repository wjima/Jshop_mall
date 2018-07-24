<?php
namespace app\common\model;

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
     * @param $seller_id
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
    public function getListSeller($seller_id = false, $page = 1, $limit = 10, $order_id = '', $evaluate = 'all', $display = 'all', $mobile = false)
    {
        $goods_ids = model('common/Goods')->getGoodsIdBySellerId($seller_id);
        $where[] = ['goods_id', 'in', $goods_ids];
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
            $v['seller_name'] = getSellerInfoById($v['goods']['seller_id'], 'seller_name');
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
}