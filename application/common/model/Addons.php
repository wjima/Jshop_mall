<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/4/19
 * Time: 下午11:23
 */
namespace app\common\model;
use think\facade\Cache;

/**
 * 插件模型
 * Class Area
 * @package app\common\model
 * @author keinx
 */
class Addons extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    /**
     * 数据转换
     * @param array $data
     * @param int $seller_id
     * @return array
     */
    public function listData($data = [],$seller_id=0)
    {
        if(!$seller_id){
            return $data;
        }
        $sellerAddonsModel = new SellerAddons();
        $tempData = [];
        if(!$data->isEmpty()){
            foreach($data->toArray() as $key=>$val){
                $filter = ['addons_id'=>$val['id'],'seller_id'=>$seller_id];
                $sellerAddons = $sellerAddonsModel->where($filter)->find();
                $tempData[$key] = $val;
                $tempData[$key]['buy'] =  $sellerAddons?true:false;
                $tempData[$key]['seller_addon_status'] =  $sellerAddons['status']?$sellerAddons['status']:$sellerAddonsModel::STATUS_ENABLE;
            }
        }
        return $tempData;
    }

    /**
     * 获取插件信息
     * @param $id
     * @return array
     */
    public function getAddonInfo($id)
    {
        $info=$this->where(['id'=>$id])->find();
        if($info){
            return $info->toArray();
        }else{
            return [];
        }
    }
    /**
     * 根据名称获取插件信息
     */
    public function getAddonByName($name='')
    {
        $addonInfo = Cache::get('addon_'.$name);

        if($addonInfo){
            return json_decode($addonInfo,true);
        }else{
            $info=$this->where(['name'=>$name])->find();
            if($info){
                $tmp_info = $info->toArray();
                Cache::set('addon_'.$name,json_encode($tmp_info));
                return $tmp_info;
            }else{
                return [];
            }
        }
    }

}