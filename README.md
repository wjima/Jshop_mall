# Jshop小程序商城


#### 项目介绍
Jshop小程序商城，是一款开源的电商系统，为中小企业提供最佳的移动电子商务解决方案。后台采用Thinkphp5.1框架开发，执行效率、扩展性、稳定性值得信赖，前台H5采用VUE开发，增加用户体验。

#### 功能介绍

 + 商品管理，单规格、多规格商品管理，品牌、分类管理、商品评价
 + 订单管理，订单支付、发货、取消、售后等
 + 会员管理，会员列表，消息管理等
 + 运营管理，广告管理、文章管理
 + 促销管理，商品促销、订单促销、优惠券等
 + 财务管理，支付单、退款单管理、提现管理、账户资金管理
 + 控制面板，计划任务、插件、图片、地区、消息、店铺配置、支付方式、配送方式等
 + 门店管理，门店列表

#### H5端说明
H5端采用Vue开发，数据和页面分离，可以使前端页面访问效果更好，封装APP时体验效果也更好。H5端可以放在任意地方，比如单独服务器、电脑桌面、PAD、手机、广告机等，没有任何约束。

提供APP打包方案，可支持调起微信、支付宝付款、分享、第三方快捷登录等。

#### 项目演示
- 前台H5演示：[https://b2c.jihainet.com/](https://b2c.jihainet.com/)
- 后台演示：[https://b2c.jihainet.com/manage/](https://b2c.jihainet.com/manage/)
- QQ交流群：823732583
- 小程序体验二维码

![输入图片说明](https://images.gitee.com/uploads/images/2018/1018/184408_a1c0d706_8503.jpeg "gh_03bc4364b4dc_344.jpg")

- 安卓APP体验二维码

![输入图片说明](https://images.gitee.com/uploads/images/2018/1026/163026_31132341_8503.png "1540542519.png")
 


#### 项目截图
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/104933_383a7831_8503.png "img(1).png")
#### 后台截图
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/104952_d154e8b4_8503.png "首页.png")
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/105206_a2dfa9e2_8503.png "商品列表.png")
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/105222_7caa99b6_8503.png "添加商品.png")
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/105317_82da6a34_8503.png "添加优惠券.png")


#### 目录结构
初始的目录结构如下：
~~~
wwwroot  WEB部署目录（或者子目录）
├─addons                应用插件目录
├─application           应用目录
│  ├─api                api接口模块目录
│  ├─b2c                前台模块
│  ├─common             公共模块目录
│  ├─crontab            定时任务目录
│  ├─job                任务队列目录
│  ├─manage             后台管理目录
│  ├─wechat             接收微信消息目录
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─tags.php           应用行为扩展定义文件
│
├─config                配置文件目录
├─h5                    前台H5源码
├─public                WEB目录（对外访问目录）
│  ├─install            自动安装目录
│  ├─static             前台静态文件
│  ├─wap                前台手机端运行目录
│  ├─index.php          入口文件
│  └─.htaccess          用于apache的重写
│
├─thinkphp              thinkphp框架系统目录
├─update                版本升级包
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─wechat_app            微信小程序源码
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
├─crontab               定时任务命令行入口文件
~~~

#### 更新说明

2018-11-2 更新内容，本次更新有风险，请先备份数据库。
更新文件目录：update
1. 配送方式优化，增加商品满多少免运费
2. 优化添加和编辑商品分类，允许只选择一级分类
3. 优惠券图片问题修复
4. 修复商品类型列表中添加时无显示属性问题
5. 修复商品类型添加时属性重复问题
6. 增加阿里云短信插件
7. 物流信息查询改为快递100
8. 修复PHP7.1以及mysql5.5以上版本报错问题
9. 增加完善积分功能
10. 修复添加促销时无法删除促销条件bug以及指定品牌和商品bug
11. 增加微信消息模板插件
12. 优化自动安装程序
13. 评价功能修复
14. 修复商品详情页图片排序问题
15. 修复H5商品列表页样式超出问题
16. 增加H5自定义站点名称
17. 修复线下支付退款bug
18. 优化后台弹窗
19. 地区信息删除重复数据
20. 优化支付证书位置
21. 修复规格过多小程序和H5显示问题

2018-10-26 更新内容，本次无数据库更新，直接覆盖或更新代码即可。
1. 修复广告位问题
2. 调整登录页样式
3. 配置文件调整
4. 修复管理员子账号问题
5. 修复图片在线管理图片列表无数据时问题
6. 增加在线管理图片列表名称显示
7. 促销方式bug修复
8. 修复后台登录日志，操作日志
9. 修复编辑商品分类时问题
10. 修复小程序收货地址管理
11. 修复编辑多规格商品切换时多出一条货品问题
12. 优化自动安装程序
13. 短信通道配置
14. 修复H5 Safari浏览器下搜索框问题
15. 商品详情页增加返回操作
16. 增加H5 在线客服
#### 环境要求
- Nginx/Apache/IIS
- PHP5.6+
- MySQL5.5+

建议使用环境：Linux + Nginx1.14 + PHP7 + MySQL5.6

#### 部署说明

[安装部署](https://gitee.com/hnjihai/jshop_mall/wikis/%E5%AE%89%E8%A3%85%E9%83%A8%E7%BD%B2?sort_id=881805)

#### nginx 伪静态配置
~~~
    location / {
        if (!-e $request_filename){
            rewrite  ^(.*)$  /index.php?s=$1  last;   break;
        }
    }
~~~
#### 安全&缺陷
如果你发现了一个安全漏洞或缺陷，请发送邮件到 jima@jihainet.com。所有的安全漏洞都将及时得到解决。


#### License

Jshop小程序商城遵循JPPL（吉海科技Jshop系列付费产品许可）协议。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2018 by 吉海科技 (https://www.jihainet.com)

All rights reserved。

吉海科技Jshop系列付费产品许可协议详情请参阅 [LICENSE.txt](LICENSE.txt)