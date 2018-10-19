# Jshop小程序商城


#### 项目介绍
Jshop小程序商城，是一款开源的电商系统，为中小企业提供最佳的新零售解决方案。后台采用Thinkphp5.1框架开发，执行效率、扩展性、稳定性值得信赖，前台H5采用VUE开发，增加用户体验。

#### 功能介绍

 + 商品管理，单规格、多规格商品管理，品牌、分类管理、商品评价
 + 订单管理，订单支付、发货、取消、售后等
 + 会员管理，会员列表，消息管理等
 + 运营管理，广告管理、文章管理
 + 促销管理，商品促销、订单促销、优惠券等
 + 财务管理，支付单、退款单管理、提现管理、账户资金管理
 + 控制面板，计划任务、插件、图片、地区、消息、店铺配置、支付方式、配送方式等
 + 门店管理，门店列表

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


#### 项目演示
- 前台H5演示：[https://b2c.jihainet.com/](https://b2c.jihainet.com/)
- 后台演示：[https://b2c.jihainet.com/manage/](https://b2c.jihainet.com/manage/)
- QQ交流群：823732583

![输入图片说明](https://images.gitee.com/uploads/images/2018/1018/184408_a1c0d706_8503.jpeg "gh_03bc4364b4dc_344.jpg")

#### 项目截图
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/104933_383a7831_8503.png "img(1).png")
#### 后台截图
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/104952_d154e8b4_8503.png "首页.png")
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/105206_a2dfa9e2_8503.png "商品列表.png")
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/105222_7caa99b6_8503.png "添加商品.png")
![输入图片说明](https://images.gitee.com/uploads/images/2018/1019/105317_82da6a34_8503.png "添加优惠券.png")

#### 环境要求
- Nginx/Apache/IIS
- PHP5.6+
- MySQL5.5+

建议使用环境：Linux + Nginx1.14 + PHP7 + MySQL5.6

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
Jshop小程序商城授权协议，请参阅 [LICENSE.txt](LICENSE.txt)