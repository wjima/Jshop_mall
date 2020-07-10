<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use org\Curl;
use Request;
use app\common\model\Area as AreaModel;
use think\Db;

/**
 * 地区管理
 * Class Area
 * @package app\seller\controller
 */
class Area extends Manage
{
    /**
     * 地区列表
     * @return array|mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $type = input('type');
            $id   = input('id');

            $result = model('common/Area')->getAreaList($type, $id);
            if ($result) {
                $return_data = array(
                    'status' => true,
                    'msg'    => '获取成功',
                    'data'   => $result
                );
            } else {
                $return_data = array(
                    'status' => false,
                    'msg'    => error_code(10025, true),
                    'data'   => $result
                );
            }
            return $return_data;
        }

        return $this->fetch();
    }


    /**
     * 添加地区
     * @return array
     */
    public function add()
    {
        $areaModel         = new AreaModel();
        $data['name']      = trim(input('name'));
        $data['postal_code'] = trim(input('postal_code'));
        $id                = $areaModel->max('id');
        $data['id']        = $id + 1;
        $data['parent_id'] = input('parent_id');
        $data['depth']     = input('depth');
        $data['sort']      = input('sort');
        if ($data['parent_id']) {
            $parentAreaInfo = $areaModel->get($data['parent_id']);
            $data['depth']  = $parentAreaInfo['depth'] + 1;
        }
        return $areaModel->add($data);
    }


    /**
     * 编辑地区
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        $areaModel = new AreaModel();

        if (Request::isPost()) {
            $id           = input('id');
            $data['name'] = trim(input('name'));
            $data['postal_code'] = trim(input('postal_code'));
            $data['sort'] = input('sort');
            return $areaModel->edit($id, $data);
        }

        $id   = input('id');
        $info = $areaModel->getAreaInfo($id);
        if ($info) {
            $return_data = array(
                'status' => true,
                'msg'    => '获取成功',
                'data'   => $info
            );
        } else {
            //$return_data = array(
            //    'status' => false,
            //    'msg'    => error_code(10025, true),
            //   'data'   => $info
            //);
            return error_code(10025);
        }
        return $return_data;
    }


    /**
     * 删除地区
     * @return mixed
     */
    public function del()
    {
        $id     = input('id');
        $result = model('common/Area')->del($id);
        return $result;
    }

    public function getlist()
    {
        if (Request::isAjax()) {
            $areaModel = new AreaModel();
            $filter    = input('request.');
            if (!isset($filter['parent_id']) || !$filter['parent_id']) {
                $filter['parent_id'] = 0;
            }
            return $areaModel->tableData($filter);
        }
        return $this->fetch();
    }

    /**
     * 获取地区信息，暂时不删除
     */
    public function geCity()
    {
        header('content-type:text/html;charset=utf-8');
        /*$content = file_get_contents('http://www.jshop.com/city.text');
        $content = json_decode($content,true);
        foreach($content as $key=>$val){
            $iData['id']=$key;
            $iData['area']=$val['name'];
            $iData['path']=1;
            $iData['pid']=0;
            Db::name('d_area')->insert($iData);
            if(isset($val['child'])){
                foreach ($val['child'] as $ck=>$cv){
                    $iData['id']=$ck;
                    $iData['area']=$cv['name'];
                    $iData['pid']=$key;
                    $iData['path']=2;

                    Db::name('d_area')->insert($iData);
                    if(isset($cv['child'])){
                        foreach ($cv['child'] as $cck=>$ccv){
                            if(isset($ccv['name'])){
                                $iData['area']=$ccv['name'];
                            }else{
                                $iData['area']=$ccv;
                            }
                            $iData['id']=$cck;
                            $iData['pid']=$ck;
                            $iData['path']=3;

                            Db::name('d_area')->insert($iData);
                            if(isset($ccv['child'])){
                                foreach ($ccv['child'] as $ccck=>$cccv){
                                    if(isset($cccv['name'])){
                                        $iData['area']=$cccv['name'];
                                    }else{
                                        $iData['area']=$cccv;
                                    }
                                    $iData['id']=$ccck;
                                    $iData['pid']=$cck;
                                    $iData['path']=4;
                                    Db::name('d_area')->insert($iData);
                                }
                            }
                        }
                    }
                }
            }
        }*/
        /*$allzhixia = Db::name('d_area')->where('area','like','市辖区')->select();
        foreach($allzhixia as $key=>$val){
            $parent = Db::name('d_area')->where('id','eq',$val['pid'])->find();
            $uData = $val;
            $uData['area'] = $parent['area'];
            Db::name('d_area')->where('id','eq',$val['id'])->update($uData);
        }*/
        /*$curl = new Curl();
        //查询邮编
        $allzhixia = Db::name('d_area')->where([])->limit(0,50)->select();
        foreach($allzhixia as $key=>$val){
            $url = 'http://opendata.baidu.com/post/s?wd='.urlencode(iconv('utf-8', 'gb2312', $val['name'])).'&p=mini&rn=1';

            $res = $curl::get($url);
            $res = iconv('gb2312','utf-8',$res);
            preg_match_all("@".$val['name']."</em>  (.*?)</a></li><!--@is", $res, $match);
            if($val['name']=='东城区'){
                print_r($res);die();

            }
            if($match[1][0]){
                $udata['postal_code'] = $match[1][0];
                Db::name('d_area')->where('id','eq',$val['id'])->update($udata);
            }else{
                print_r($match[1][0]);die();

            }

        }*/
        //die('ok');
        /*SELECT * FROM `area` WHERE ( `parent_id` LIKE "____00" ) and ( `id` LIKE "____01" ) and `depth` =3 and name like '%市';

        SELECT * FROM `area` WHERE ( `parent_id` LIKE "____00" ) and ( `id` LIKE "____01" ) and `depth` =3 and name like '%市'

        UPDATE area SET `name` = '市辖区' WHERE ( `parent_id` LIKE "____00" ) and ( `id` LIKE "____01" ) and `depth` =3 and name like '%市';*/

        //省级
        //Db::name('d_area')->where('id LIKE "__0000"')->update(array('pid'=>0));
        /*Db::name('area')->where('id LIKE "____00"')->select();
        print_r(Db::name('d_area')->getLastSql());die();*/
    }

    /**
     * 生成缓存
     */
    public function generateCache()
    {

        $return   = [
            'status' => true,
            'msg'    => '生成成功',
            'data'   => []
        ];
        $model    = new AreaModel();
        $pid      = 0;
        $data     = $model->getAllChild($pid);
        $areaData = [];
        foreach ($data as $key => $val) {
            $areaData[$key]['label'] = $val['name'];
            $areaData[$key]['value'] = $val['id'];
            $childrens               = $model->getAllChild($val['id']);
            $children                = [];
            if ($childrens) {
                foreach ($childrens as $skey => $sval) {
                    $children[$skey]['label'] = $sval['name'];
                    $children[$skey]['value'] = $sval['id'];
                    $lastchildrens            = $model->getAllChild($sval['id']);
                    if ($lastchildrens) {
                        $lchildren = [];
                        foreach ($lastchildrens as $lkey => $lval) {
                            $lchildren[$lkey]['label'] = $lval['name'];
                            $lchildren[$lkey]['value'] = $lval['id'];
                        }
                        $children[$skey]['children'] = $lchildren;
                    }
                }
                $areaData[$key]['children'] = $children;
            }
        }
        $return['data'] = $areaData;
        $area           = config('jshop.area_list');

        @file_put_contents($area, json_encode($return, JSON_UNESCAPED_UNICODE));
        $return['data'] = '';
        return $return;
    }
}
