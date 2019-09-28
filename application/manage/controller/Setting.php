<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\Setting as SettingModel;
use Request;
use think\Console;
use think\facade\Cache;


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
            //配置保存后，清理缓存
            Cache::clear();//TODO 如果开启其他缓存，记得这里要配置缓存配置信息
            Console::call('clear', ['--cache', '--dir']);//清除缓存文件
            Console::call('clear', ['--path', ROOT_PATH . '\\runtime\\temp\\']); //清除模板缓存
            return $result;
        }else{
            $data = $settingModel->getAll();
            $this->assign('data', $data);
            return $this->fetch();
        }
    }
}