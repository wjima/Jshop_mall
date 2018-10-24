<?php
/**
 * 定时任务配置文件
 */
return [
    //key为任务名，多任务下名称必须唯一
    'order_cancel'=>[
        'callback'=>['app\\crontab\\Order','cancle'],//订单自动取消
        //指定任务进程最大内存  系统默认为512M
        'worker_memory'      =>'50M',
        //开启任务进程的多线程模式
        'worker_pthreads'   =>false,
        //任务的进程数 系统默认1
        'worker_count'=>1,
        //crontad格式 :秒 分 时 天 月 年 周
        'crontab'     =>'1 * * * * * *',
    ],
    'order_complete'=>[
        'callback'=>['app\\crontab\\Order','complete'],//订单自动完成
        //指定任务进程最大内存  系统默认为512M
        'worker_memory'      =>'50M',
        //开启任务进程的多线程模式
        'worker_pthreads'   =>false,
        //任务的进程数 系统默认1
        'worker_count'=>1,
        //crontad格式 :秒 分 时 天 月 年 周
        'crontab'     =>'1 * * * * * *',
    ],
    'order_evaluate'=>[
        'callback'=>['app\\crontab\\Order','evaluate'],//订单自动评价
        //指定任务进程最大内存  系统默认为512M
        'worker_memory'      =>'50M',
        //开启任务进程的多线程模式
        'worker_pthreads'   =>false,
        //任务的进程数 系统默认1
        'worker_count'=>1,
        //crontad格式 :秒 分 时 天 月 年 周
        'crontab'     =>'1 * * * * * *',
    ],
    'order_sign'=>[
        'callback'=>['app\\crontab\\Order','sign'],//订单自动签收
        //指定任务进程最大内存  系统默认为512M
        'worker_memory'      =>'50M',
        //开启任务进程的多线程模式
        'worker_pthreads'   =>false,
        //任务的进程数 系统默认1
        'worker_count'=>1,
        //crontad格式 :秒 分 时 天 月 年 周
        'crontab'     =>'1 * * * * * *',
    ]
];