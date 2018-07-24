<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/4/19
 * Time: 下午11:23
 */

namespace app\common\model;

use think\facade\Cache;
use app\common\model\Addons;
/**
 * 商户订购插件模型
 * Class Area
 * @package app\common\model
 * @author keinx
 */
class SellerAddons extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    const STATUS_ENABLE = 1;//启用
    const STATUS_DISENABLE = 2;//停用
    /**
     * 获取对应插件
     * @return \think\model\relation\HasOne
     */
    public function addon()
    {
        return $this->hasOne('Addons', 'id', 'addons_id');
    }

    public function getSellerAddons($seller_id = 0)
    {

        if (!$seller_id) {
            return [];
        }
        $filter[] = ['s.seller_id', 'eq', $seller_id];
        $filter[] = ['s.status', 'eq', self::STATUS_ENABLE];//只获取启用数据
        $filter[] = ['s.etime', '>=', time()];

        $addons = $this->alias('s')
            ->join('addons a', 's.addons_id = a.id')
            ->where($filter)
            ->select();


        if (!$addons->isEmpty()) {
            return $addons->toArray();
        } else {
            return [];
        }
    }


    /**
     * 订购插件
     * @param int $id
     * @param int $seller_id
     * @return bool
     */
    public function addAddons($id = 0, $seller_id = 0)
    {
        if (!$id || !$seller_id) {
            return false;
        }
        $addonModel = new Addons();
        $addon      = $addonModel->where(['id' => $id])->find();

        if (!$addon) {
            return false;
        }
        $selfAddon = $this->where(['id' => $id, 'seller_id' => $seller_id])->find();
        if ($selfAddon) {
            $endTime = $selfAddon['etime'] + $addon['unit'] * 86400;
            $iData   = [
                'addons_id' => $id,
                'seller_id' => $seller_id,
                'etime'     => $endTime,//到期时间
            ];
            return $this->save($iData, ['id' => $selfAddon['id']]);
        } else {
            $endTime = $addon['unit'] * 86400;
            $iData   = [
                'addons_id' => $id,
                'seller_id' => $seller_id,
                'etime'     => $endTime,//到期时间
            ];
            return $this->save($iData);
        }
    }

    public function doSetting($params,$seller_id=0)
    {
        if (!$params['addons_id'] || !$seller_id) {
            return false;
        }
        $selfAddon = $this->where(['addons_id' => $params['addons_id'], 'seller_id' => $seller_id])->find();
        if(!$selfAddon){
            return false;
        }
        unset($params['addons_id']);
        $uData = [
            'setting'=>json_encode($params['setting']),
        ];
        $res =  $this->save($uData,[
            'id'=>$selfAddon['id']
        ]);
        if($res!==false){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取配置信息
     * @param $addons_id
     * @param int $seller_id
     * @return bool|mixed
     */
    public function getSetting($addons_id,$seller_id = 0){
        if (!$seller_id) {
            return false;
        }

        $key = 'seller_addon_'.$addons_id.'_'.$seller_id;
        $addonInfo = Cache::get($key);
        if($addonInfo){
            return json_decode($addonInfo,true);
        }else{
            $filter = ['addons_id' => $addons_id,
                       'status'    => self::STATUS_ENABLE,
                       'seller_id' => $seller_id];
            $selfAddon = $this->where($filter)->find();
            if(!$selfAddon){
                return false;
            }
            Cache::set($key,$selfAddon['setting']);
            return json_decode($selfAddon['setting'],true);
        }

    }

    /**
     * 插件启用，停用
     * @param $addons_id
     * @param $seller_id
     * @return bool
     */
    public function changeStatus($addons_id,$seller_id)
    {
        if(!$addons_id||!$seller_id){
            return false;
        }
        $selfAddon = $this->field('id,status')->where(['addons_id' => $addons_id, 'seller_id' => $seller_id])->find();
        if(!$selfAddon){
            return false;
        }
        if($selfAddon['status']==self::STATUS_ENABLE){
            $uData['status'] = self::STATUS_DISENABLE;
        }else{
            $uData['status'] = self::STATUS_ENABLE;
        }
        $res = $this->save($uData,['id'=>$selfAddon['id']]);
        if($res!==false){
            //刷新cache
            $key = 'seller_addon_'.$addons_id.'_'.$seller_id;
            Cache::set($key,false);
            return true;
        }else{
            return false;
        }
    }
}