<?php
namespace app\common\model;


/**
 * 商品收藏表
 * Class GoodsCollection
 * @package app\common\model
 *
 */
class GoodsCollection extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';


    /**
     * 如果收藏了，就取消收藏，如果没有收藏，就收藏
     * @param $userId
     * @param $sellerId
     * @param $goodsId
     * @return array
     */
    public function toDo($userId, $sellerId, $goodsId)
    {
        $where['user_id'] = $userId;
        $where['seller_id'] = $sellerId;
        $where['goods_id'] = $goodsId;
        $collectionInfo = $this->where($where)->find();
        if($collectionInfo){
            return $this->toDel($userId, $sellerId, $goodsId);
        }else{
            return $this->toAdd($userId, $sellerId, $goodsId);
        }

    }

    /**
     * 检查是否收藏了此商品
     * @param $userId
     * @param $sellerId
     * @param $goodsId
     * @return array
     */
    public function check($userId, $sellerId, $goodsId)
    {
        $where['user_id'] = $userId;
        $where['seller_id'] = $sellerId;
        $where['goods_id'] = $goodsId;
        $collectionInfo = $this->where($where)->find();
        if($collectionInfo){
            return true;
        }else{
            return false;
        }

    }

    /**
     * 取消收藏
     * @param $userId
     * @param $sellerId
     * @param $goodsId
     * @return array
     */
    private function toDel($userId, $sellerId, $goodsId)
    {
        $result = array(
            'status' => true,
            'data' => '',
            'msg' => '取消收藏成功'
        );
        $where['user_id'] = $userId;
        $where['seller_id'] = $sellerId;
        $where['goods_id'] = $goodsId;
        $re = $this->where($where)->delete();

        return $result;
    }

    /**
     * 收藏
     * @param $userId
     * @param $sellerId
     * @param $goodsId
     * @return array
     */
    private function toAdd($userId, $sellerId, $goodsId)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $goodsModel = new Goods();
        $goodsInfo = $goodsModel->where(array('id'=>$goodsId))->find();
        if(!$goodsInfo){
            $result['msg'] = '没有此商品';
            return $result;
        }
        $data['user_id'] = $userId;
        $data['seller_id'] = $sellerId;
        $data['goods_id'] = $goodsId;
        $data['goods_name'] = $goodsInfo['name'];

        $this->save($data);

        $result['status'] = true;
        $result['msg'] = '收藏成功';
        return $result;
    }
    public function getList($user_id, $seller_id,$page = 1,$limit = 10,$where = [], $order = "id asc")
    {
        $result = array(
            'status' => true,
            'data' => [],
            'msg' => ''
        );
        $where[] = array('seller_id', 'eq', $seller_id);
        $where[] = array('user_id', 'eq', $user_id);

        $list = $this::with('goods')
            ->where($where)
            ->order($order)
            ->limit(($page-1) * $limit, $limit)
            ->select();
        if(!$list->isEmpty()){
            $list = $list->hidden(['goods'=>['isdel']]);
        }

        $count = $this->where($where)->count();

        foreach($list as $k => $v){
            if($v['goods']){
                $list[$k]['goods']['image_url'] = _sImage($v['goods']['image_id']);
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