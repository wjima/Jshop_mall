<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\Setting as SettingModel;
use Request;


class Setting extends Manage
{
    public function index()
    {
        $settingModel = new SettingModel();
        if(Request::isAjax()){
            foreach(input('param.') as $k => $v){
                $result = $settingModel->setValue($k, $v);
                //如果出错，就返回，如果是没有此参数，就默认跳过
                if(!$result['status'] && $result['data'] != 10008){
                    return $result;
                }
            }
            $result = array(
                'status' => true,
                'data' => [],
                'msg' => '保存成功'
            );
            return $result;
        }else{
            $data = $settingModel->getAll();
            $this->assign('data', $data);
            return $this->fetch();
        }
    }
}