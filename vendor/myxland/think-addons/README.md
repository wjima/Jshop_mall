Thinkphp5.1 扩展Addon
======

## 安装
~~~
composer require myxland/think-addons
php think addons:config
~~~

## 创建插件
> 创建的插件可以在view视图中使用，也可以在php业务中使用
 
安装完成后访问系统时会在项目根目录生成名为`addons`的目录，在该目录中创建需要的插件。

下面写一个例子：

### 创建test插件
> 在addons目录中创建test目录

### 创建钩子实现类
> 在test目录中创建Test.php类文件。注意：类文件首字母需大写

```
<?php
namespace addons\test;	// 注意命名空间规范

use myxland\addons\Addons;

/**
 * 插件测试
 */
class Test extends Addons	// 需继承myxland\addons\Addons类
{
	// 该插件的基础信息
    public $info = [
        'name' => 'test',	// 插件标识
        'title' => '插件测试',	// 插件名称
        'description' => 'thinkph5插件测试',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'byron sampson',
        'version' => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function testhook($param)
    {
		// 调用钩子时候的参数信息
        print_r($param);
		// 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        print_r($this->getConfig());
		// 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('info');
    }

}
```

### 创建插件配置文件
> 在test目录中创建config.php类文件，插件配置文件可以省略。

```
<?php
return [
    'display' => [
        'title' => '是否显示:',
        'type' => 'radio',
        'options' => [
            '1' => '显示',
            '0' => '不显示'
        ],
        'value' => '1'
    ]
];
```

### 创建钩子模板文件
> 在test目录中创建info.html模板文件，钩子在使用fetch方法时对应的模板文件。

```
<h1>hello tpl</h1>

如果插件中需要有链接或提交数据的业务，可以在插件中创建controller业务文件，
要访问插件中的controller时使用addon_url生成url链接。
如下：
<a href="{:addon_url('test://Action/link')}">link test</a>
格式为：
test为插件名，Action为controller中的类名，link为controller中的方法
```

### 创建插件的controller文件
> 在test目录中创建controller目录，在controller目录中创建Action.php文件
> controller类的用法与tp5中的controller一致

```
<?php
namespace addons\test\controller;

class Action
{
    public function link()
    {
        echo 'hello link';
    }
}
```
> 如果需要使用view模板则需要继承`\myxland\addons\library\Controller`类
> 模板文件所在位置为插件目录的view中，规则与模块中的view规则一致

```
<?php
namespace addons\test\controller;

use myxland\addons\library\Controller;

class Action extends Controller
{
    public function link()
    {
        return $this->fetch();
    }
}
```

## 使用钩子
> 创建好插件后就可以在正常业务中使用该插件中的钩子了
> 使用钩子的时候第二个参数可以省略

### 模板中使用钩子

```
<div>{:hook('testhook', ['id'=>1])}</div>
```

### php业务中使用
> 只要是thinkphp5正常流程中的任意位置均可以使用

```
hook('testhook', ['id'=>1])
```

## 插件目录结构
### 最终生成的目录结构为

```
tp5
 - addons
 -- test
 --- controller
 ---- Action.php
 --- view
 ---- action
 ----- link.html
 --- config.php
 --- info.html
 --- Test.php
 - application
 - thinkphp
 - extend
 - vendor
 - public
```
