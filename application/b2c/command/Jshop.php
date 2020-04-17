<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\b2c\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

/**
 * Jshop命令行
 * Class Jshop
 * @package app\b2c\command
 */
class Jshop extends Command
{
    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->setName('jshop')
            ->setDefinition(
                [
                    new Argument('method', Argument::REQUIRED, "cancle=>定时取消订单, complete=>定时自动完成, evaluate=>定时自动评价, sign=>定时自动签收, remind=>定时自动催付款, pintuan_cancle=>拼团自动取消, remove_op_log=>定期清理操作日志"),
                ]
            )
            ->setDescription('Jshop小程序商城命令行');
    }


    /**
     * 命令执行
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {
        $method = trim($input->getArgument('method'));

        switch ($method) {
            case 'cancle':
                $output->writeln(controller('b2c/Crontab')->cancle());
                break;
            case 'complete':
                $output->writeln(controller('b2c/Crontab')->complete());
                break;
            case 'evaluate':
                $output->writeln(controller('b2c/Crontab')->evaluate());
                break;
            case 'sign':
                $output->writeln(controller('b2c/Crontab')->sign());
                break;
            case 'remind':
                $output->writeln(controller('b2c/Crontab')->remind());
                break;
            case 'pintuan_cancle':
                $output->writeln(controller('b2c/Crontab')->pintuanCancle());
                break;
            case 'remove_op_log':
                $output->writeln(controller('b2c/Crontab')->removeOpLog());
                break;
            case 'bargain_cancle':
                $output->writeln(controller('b2c/Crontab')->bargainCancle());
                break;
        }
    }
}