<?php
namespace app\common\model;
use think\Db;

/**
 * 商家评价表
 * Class SellerComment
 * @package app\common\model
 */
class SellerComment extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    const ALREADY_COMMENT = 2;              //已经评价

    /**
     * 添加评价订单
     * @param $order_id
     * @param $seller
     * @param $goods
     * @param $seller_id
     * @param $user_id
     * @return array
     */
    public function addComment($order_id, $seller, $goods, $seller_id, $user_id)
    {
        $data = [
            'seller_id' => $seller_id,
            'order_id' => $order_id,
            'star_one' => $seller['starOne'],
            'star_two' => $seller['starTwo'],
            'star_three' => $seller['starThree'],
            'user_id' => $user_id
        ];
        Db::startTrans();
        try{
            //插入商家评价
            $this->save($data);
            //插入商品评价
            $goods_data = [];
            foreach($goods as $k => $v)
            {
                $score = 0;
                if($v['evaluate']['praise'])
                {
                    $score = 1;
                }
                elseif($v['evaluate']['difference'])
                {
                    $score = -1;
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
                $addon = model('common/OrderItems')->getAddon($v['product']);
                $goods_data[] = [
                    'comment_id' => 0,
                    'score' => $score,
                    'user_id' => $user_id,
                    'goods_id' => $k,
                    'order_id' => $order_id,
                    'images' => $images,
                    'content' => $v['textarea'],
                    'addon' => $addon
                ];
            }
            model('common/goodsComment')->saveAll($goods_data);
            //修改评价状态
            $order_data['is_comment'] = self::ALREADY_COMMENT;
            model('common/Order')->save($order_data, ['order_id' => $order_id]);
            Db::commit();
            $result = true;
        }catch(\Exception $e){
            Db::rollback();
            $result = false;
        }

        if($result)
        {
            $return_data = [
                'status' => true,
                'msg' => '评价成功',
                'data' => []
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '评价失败',
                'data' => []
            ];
        }
        return $return_data;
    }
}