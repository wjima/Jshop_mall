<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsBrowsing extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';


    public function toAdd($userId, $sellerId, $goodsId)
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
        return $result;
    }

    /**
     * 删除浏览记录
     * @param $userId
     * @param $sellerId
     * @param $goodsId
     * @return array
     */
    public function toDel($userId, $sellerId, $goods_ids)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $where[] = ['user_id','eq',$userId];
        $where[] = ['seller_id','eq',$sellerId];
        $where[] = ['goods_id','in',$goods_ids];

        $info = $this->where($where)->select();
        foreach($info as $v){
            $v->delete();               //为什么这么删，因为软删除只能这样删
        }
        $result['data'] = count($info);
        $result['msg'] = "删除成功";
        $result['status'] = true;
        return $result;
    }

    //取用户的浏览记录,倒叙，并去重
    public function getList($user_id, $seller_id, $page = 1,$limit = 10)
    {
        $result = array(
            'status' => true,
            'data' => [],
            'msg' => ''
        );
        $where[] = array('seller_id', 'eq', $seller_id);
        $where[] = array('user_id', 'eq', $user_id);

        $field = ['id','goods_id','goods_name','ctime','seller_id','user_id'];
        $list = $this::with('goods')
            ->field($field)
            ->where($where)
            ->group('goods_id')
            ->limit(($page-1) * $limit, $limit)
            ->order('ctime desc')
            ->select();
        if(!$list->isEmpty()){
            $list = $list->hidden(['goods'=>['isdel','intro'],'isdel']);
        }
        $count = $this->where($where)->group('goods_id')->count();

        //取关联图片和是否收藏
        foreach($list as $k => $v){
            if($v['goods']){
                $list[$k]['goods']['image_url'] = _sImage($v['goods']['image_id']);
            }
            $list[$k]['isCollection'] = model('common/GoodsCollection')->check($v['user_id'], $v['seller_id'], $v['goods_id']);
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