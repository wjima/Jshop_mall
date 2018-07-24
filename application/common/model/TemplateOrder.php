<?php
/**
 * 模板订购表
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/19
 * Time: 下午12:00
 */

namespace app\common\model;

class TemplateOrder extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    /**
     * 保存订单模板信息
     * @param array $data
     * @return int|string
     */
    public function doAdd($data = [])
    {
        $result = $this->save($data);
        if ($result) {
            return $this->getLastInsID();
        }
        return $result;
    }

    /**
     * 判断是否购买过模板
     * @param int $seller_id
     * @param string $appid
     * @param int $template_id
     * @return bool|mixed
     */
    public function getTempOrder($seller_id = 0, $appid = '', $template_id = 0){
        if (!$seller_id || !$appid) {
            return false;
        }
        $filter=[
            'seller_id' => $seller_id,
            'appid' => $appid,
        ];
        if($template_id!=false){
            $filter['template_id'] = $template_id;
        }
        $order = $this->field('id')->where($filter)->find();
        if ($order != false) {
            return $order['id'];
        }
        return false;
    }

    /**
     * 获取所有模板
     * @param int $seller_id
     * @return array
     */
    public function getAllTemp($seller_id=0)
    {
        $list=$this->where(['seller_id'=>$seller_id])->select();
        if(!$list->isEmpty()){
            foreach($list as $key=>$val)
            {
                $val->template;
            }
            return $list->toArray();
        }
        return $list;
    }

    /**
     * 取出对应模板
     * @return \think\model\relation\HasOne
     */
    public function template()
    {
        return $this->hasOne('Template','id','template_id');
    }
}