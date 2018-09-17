<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\crontab;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use think\facade\Config;

//用户目录
define("START_PATH", ROOT_PATH);
// 载入taskphp入口文件
require_once ROOT_PATH . '/vendor/taskphp/taskphp/src/taskphp/base.php';

class Crontab extends Command{

    protected function get_config(){
        $task = Config::get('crontab.');
        if(!$task){
            echo '定时任务配置文件不存在';die();
        }
        return [
            //任务列表
            'task_list'=>$task,
            //系统日志配置
            'log'=>[
                'path'=>ROOT_PATH.DS.'runtime'.DS.'crontab',
                //错误日志开关 true开启  false关闭
                'error'=>true,
                //调试日志开关 true开启  false关闭
                'debug'=>true,
            ],
            'db'=>[
                'type'          =>  'mysql',
                'username'      =>  'root',
                'password'      =>  'root',
                'host'      =>  '127.0.0.1',
                'port'      =>  '3306',
                'name'      =>  'task',
                // 数据库编码默认采用utf8
                'charset'       => 'utf8',
                // 数据库表前缀
                'prefix'        => 'test_',
                // 开启断线重连
                'break_reconnect'=>true,
            ],
        ];
    }

    protected function configure(){
		if(CRONTAB == TRUE){
			$this->addArgument('param', Argument::OPTIONAL);
			// 设置命令名称
			$this->setName($_SERVER['argv'][1])->setDescription('this is a taskphp!');
		}else{
			$this->setName('crontab')->setDescription('this is a cron!');
		}
        
    }

    protected function execute(Input $input, Output $output){
        //系统配置
        $config= $this->get_config();
        //加载配置信息
        \taskphp\Config::load($config);
        //定义启动文件入口标记
        //define("START_PATH", ROOT_PATH);
        //运行框架
        \taskphp\App::run();
    }
}