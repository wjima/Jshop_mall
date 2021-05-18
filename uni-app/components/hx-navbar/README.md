# hx-navbar 适用于 uni-app 项目的头部导航组件

导航栏组件，主要用于头部导航，组件名：hx-navbar

本组件目前兼容微信小程序、H5、5+APP。

## QQ群 954035921 
如有问题可进群发图讨论

### 本组件支持模式：
1. 普通固定顶部导航  
2. 透明导航  
3. 透明固定顶部导航 
4. 不固定普通导航
5. 背景颜色线性渐变
6. 滑动显示背景
7. 左、中、右3个插槽；可关闭左右插槽使中间插槽铺满导航，实现高度自定义的导航需求

### 使用前提

需要先安装·uniapp·官方的```uni-icons``` 图标组件，```uni-icons```官方组件下载地址：[https://ext.dcloud.net.cn/plugin?id=28](https://ext.dcloud.net.cn/plugin?id=28)

### 使用方式	
页面使用需在 ``` script ``` 中引用组件
``` javascript
import hxNavbar from "@/components/hx-navbar/hx-navbar.vue"
export default {
    components: {hxNavbar}
}
```

全局使用需在 ``` main.js ```  中注册组件
``` javascript
import hxNavbar from "./components/hx-navbar/hx-navbar.vue"
Vue.component('hx-navbar',hxNavbar)

```


### 属性
#### 基本属性 
| 名称                        | 类型            | 默认值                | 描述                                               |
| ----------------------------|--------------- | ---------------------- | ---------------------------------------------------|
| back                   	  | Boolean         | true          | 返回上一页，（设置后，```leftIcon```属性，和```click-left```事件将失效|
| height                   	  | String         | 44px          | 导航栏高度（不包含状态栏高度）|
| barPlaceholder              | String         | auto          | 导航栏占位符 显示（show），隐藏（hidden），自动（auto：如果头部为固定fixed ，则显示占位符）               |
| title                       | String         | -             | 导航标题（当设置了标题，中间插槽将失效）                                     |
| fixed                       | Boolean        | false         | 固定头部											|
| color                       | String         | #000000       | 导航文字颜色（如果需要屏幕滑动后变色，参数则为数组，例子：`['#000000','#ffffff']`）                                        |
| backgroundColor             | Array          | [255, 255, 255]          | 导航背景颜色为RGB 编号（单色背景数组为```[255,255,255]```，线性渐变背景```[[236, 0, 140],[103, 57, 182],...]```）                                      |
| pageScroll				  | Object         | {}             | 屏幕滑动距离顶部的对象```滑动渐变必要参数```                                       |
| backgroundColorLinearDeg    | String         | 45             | 导航背景线性渐变角度                                       |
| backgroundImg   			  | String         | -             | 导航背景图片（背景图片优先级高于背景颜色）  |
| transparent   			  | String         | show             | 背景透明（show 不透明,hidden 透明,auto 自动：滑动逐渐显示背景颜色，当头部固定时生效） 兼容性：头条小程序必须在页面上加 onPageScroll(e){} ，才能滑动显示背景，可参考dome7|
| shadow                      | Boolean         | false         | 导航栏阴影          |
| border                      | Boolean         | false         | 导航栏边框                           |

#### 关于状态栏的属性
| 名称                        | 类型            | 默认值                | 描述                                               |
| ----------------------------|--------------- | ---------------------- | ---------------------------------------------------|
| statusBar                   | Boolean         | true       		   | 包含状态栏												|
| statusBarFontColor          | Array,String   | #000000               | 状态栏字体颜色，只支持```#000000 ```和```#FFFFFF```（如果需要屏幕滑动变色，参数则为数组，例子：```['#000000','#ffffff']```）|
| statusBarBackground         | String         | -                     | 状态栏背景颜色，如果你想单独设置状态栏颜色，该属性是个不错的选择

#### 关于插槽的属性
| 名称                        | 类型            | 默认值                | 描述                                               |
| ----------------------------|--------------- | ---------------------- | ---------------------------------------------------|
| leftIcon                    | String         | -             | 左插槽图标，必须为 ```uni-icons``` 图标                                       |
| rightIcon   				  | String         | -             | 右插槽图标，必须为 ```uni-icons``` 图标  |
| leftSlot                    | Boolean        | true          | 开启左插槽                                        |
| rightSlot                   | Boolean        | true          | 开启右插槽                                      |
| leftSlidiSwitch             | Boolean         | false         | 屏幕滑动后 `left`插槽切换为`leftAfter`插槽                       |
| centerSlidiSwitch           | Boolean         | false         | 屏幕滑动后 `default`插槽切换为`centerAfter`插槽                            |
| rightSlidiSwitch            | Boolean         | false         | 屏幕滑动后 `right`插槽切换为`rightAfter`插槽                            |


#### 返回上一页为空时的处理属性
| 名称                        | 类型            | 默认值                | 描述                                               |
| ----------------------------|--------------- | ---------------------- | ---------------------------------------------------|
| backTabbarUrl               | String         | /pages/index/index     | 返回至指定的tabber页面（返回首页），当上一页为空时生效；全局使用推荐进组件修改`backTabbarUrl`的默认值|
| defaultBackUrl              | String         | -          			| 返回至指定的普通页面，当上一页为空时生效；`defaultBackUrl`优先级高于`backTabbarUrl`；主要应用在返回失效时|

``` html
<!-- 使用场景：假如刷新了当前页面，那么返回事件将失效。
这时用上 `defaultBackUrl` 或 `backTabbarUrl` 则能返回至指定页面-->
<hx-navbar left-text="关于" defaultBackUrl="/pages/user/setting/setting" />
```


### 插槽
| 名称                  | 描述                                                               |
| ----------------------|-------------------------------------------------------------------|   
| left                  | 左插槽 （可关闭该插槽 ```leftSlot``` 属性）                           |
| default               | 中间插槽（当设置了标题，中间插槽将失效）                               |
| right                 | 右插槽 （可关闭该插槽 ```rightSlot``` 属性）                          |
| leftAfter             | 屏幕滑动后的左插槽 （需要开启`leftSlidiSwitch`属性才生效）                  |
| centerAfter           | 屏幕滑动后的中插槽 （需要开启`centerSlidiSwitch`属性才生效）                  |
| rightAfter            | 屏幕滑动后的右插槽 （需要开启`rightSlidiSwitch`属性才生效）                  |


``` html
<hx-navbar>
    <view>标题栏（中间插槽）</view>
    <view slot="left">left（左插槽）</view>
    <view  slot="right">right（右插槽）</view>
</hx-navbar>
```


### 事件
| 名称             | 参数              | 描述                      |
| -----------------|------------------| --------------------------|
| click-left       | -                | 左侧按钮点击时触发，此事件将覆盖 `返回`          |
| click-right      | -                | 右侧按钮点击时触发          |
| scroll           | -                | 监听滚动条，回调参数为滚动距离；固定头部时生效；应用场景：如滚动到多少时触发某些事件          |

## 使用例子

### 简单使用
``` html
<hx-navbar title="我爱新疆" left-text="返回" />
```

### 背景颜色线性渐变、头部固定
``` html
<hx-navbar 
	title="颜色渐变" 
	:back="false"
	:fixed="true"
	color="#ffffff"
	:background-color="[[28, 187, 180],[141, 198, 63]]">
</hx-navbar>
```

### 滑动显示背景
``` html
<!-- 该例子取消了导航占位符 -->
<hx-navbar 
	title="颜色渐变" 
	:back="false"
	:fixed="true"
	color="#ffffff"
	barPlaceholder="hidden"
	transparent="auto"
	:background-color="[[28, 187, 180],[141, 198, 63]]"
	:pageScroll.sync="scrollData">
</hx-navbar>

```
``` javascript
data() {
	return {
		scrollData: {},
	}
},
//必须在页面加 onPageScroll(e){} ，才能滑动显示背景
onPageScroll(e){
	this.scrollData = e;
},
```

### 左中插槽演示
``` html
<hx-navbar  
	:back="false" 
	:fixed="true"
	right-icon="scan">
	<block slot="left">
		<view class="city">
			<view>新疆</view>
			<uni-icons type="arrowdown" color="#333333" size="22" />
		</view>
	</block>
	<view class="input-view">
		<uni-icons type="search" size="22" color="#666666" />
		<input confirm-type="search" class="input" type="text" placeholder="输入搜索关键词" @confirm="confirm">
	</view>
</hx-navbar>


/*css 用于演示插槽自定义样式*/
<style>
	.city{
		display: flex;flex-direction: row;
		align-items: center;
		justify-content: center;
		width: 100%;
		margin-left: 8px;
		white-space: nowrap;
	}
</style>
```

### 关闭左右插槽演示
``` html
<hx-navbar  :back="false" :fixed="true" :leftSlot='false' :rightSlot='false'>
	<view style="display: flex;">
		<view class="city">
			<view>新疆</view>
			<uni-icons type="arrowdown" size="22" />
		</view>
		<view class="input-view" style="width: 100%;">
			<uni-icons type="search" size="22" color="#666666" />
			<input confirm-type="search" class="input" type="text" placeholder="输入搜索关键词" @confirm="confirm">
		</view>
		<uni-icons type="scan" size="22" style="line-height: 44px;padding-left: 8px;"/>
	</view>
</hx-navbar>
/*css 用于演示插槽自定义样式*/
<style>
	.city{
		display: flex;flex-direction: row;
		align-items: center;
		justify-content: center;
		width: 100%;
		margin-left: 8px;
		white-space: nowrap;
	}
</style>
```

### 屏幕滑动切换显示插槽
``` html
<!-- 该例子演示中间插槽、右插槽屏幕滑动后的变换 -->
<hx-navbar  
:border="true" 
:centerSlidiSwitch="true"
:rightSlidiSwitch="true"
:fixed="true"
:pageScroll.sync="scrollData">
	<view style="text-align: center;width: 100%;">
		<text>帮助反馈</text>
	</view>
	<view slot="centerAfter" style="text-align: center;width: 100%;">
		<text>咨询</text>
	</view>
	<block slot="right">
		<uni-icons type="qq" size="30" ></uni-icons>
	</block>
	<block slot="rightAfter">
		<uni-icons type="chat" size="30" ></uni-icons>
	</block>
</hx-navbar>
```
``` javascript
data() {
	return {
		scrollData: {},
	}
},
//必须在页面加 onPageScroll(e){} ，才能滑动显示
onPageScroll(e){
	this.scrollData = e;
},
```