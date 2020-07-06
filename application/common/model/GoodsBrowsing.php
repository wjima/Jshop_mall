<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsBrowsing extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';


    public function toAdd($userId, $goodsId)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $goodsModel = new Goods();
        $goodsInfo = $goodsModel->where(array('id'=>$goodsId))->find();
        if(!$goodsInfo)
        {
//            $result['msg'] = '没有此商品';
            return error_code(12700);
        }
        $data['user_id'] = $userId;
        $data['goods_id'] = $goodsId;
        $data['goods_name'] = $goodsInfo['name'];

        $this->save($data);

        $result['status'] = true;
        return $result;
    }

    /**
     * 删除浏览记录
     * @param $userId
     * @param $goods_ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function toDel($userId, $goods_ids)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $where[] = ['user_id','eq',$userId];
        $where[] = ['goods_id','in',$goods_ids];

        $info = $this->where($where)->select();
        foreach($info as $v)
        {
            $v->delete();               //为什么这么删，因为软删除只能这样删
        }
        $result['data'] = count($info);
        $result['msg'] = "删除成功";
        $result['status'] = true;
        return $result;
    }

    /**
     * 取用户的浏览记录,倒叙，并去重
     * @param $user_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($user_id, $page = 1,$limit = 10)
    {
        $result = array(
            'status' => true,
            'data' => [],
            'msg' => ''
        );
        $where[] = array('user_id', 'eq', $user_id);

        $field = ['id','goods_id','goods_name','ctime','user_id'];
        $list = $this::with('goods')
            ->field($field)
            ->where($where)
            ->group('goods_id')
            ->limit(($page-1) * $limit, $limit)
            ->order('ctime desc')
            ->select();
        if(!$list->isEmpty())
        {
            $list = $list->hidden(['goods'=>['isdel','intro'],'isdel']);
        }
        $count = $this->where($where)->group('goods_id')->count();

        //取关联图片和是否收藏
        if(!$list->isEmpty()){
            $list = $list->toArray();
            foreach($list as $k => $v)
            {
                if($v['goods'])
                {
                    $list[$k]['goods']['image_url'] = _sImage($v['goods']['image_id']);
                }
                else
                {
                    //商品被删除时
                    $list[$k]['goods']['price'] = 0;
                    $list[$k]['goods']['image_url'] = _sImage();
                }
                $list[$k]['isCollection'] = model('common/GoodsCollection')->check($v['user_id'], $v['goods_id']);
            }
        }

        
        $result['data']['list'] = $list;
        $result['data']['count'] = $count;
        $result['data']['limit'] = $limit;
        $result['data']['page'] = $page;

        return $result;
    }


    public function goods()
    {
        return $this->hasOne('Goods', 'id','goods_id');
    }

}