

### 运动插件使用说明

#### 1. 安装部署流程

* 将Werun解压到`addons`目录下。

* 将`./uni-app/pedometer`文件夹移动到`uni-app/pages`下。

* 将`./uni-app/my-sign-calendar`文件夹移动到`uni-app/components`下。

* 修改`uni-app/page.json`，将下面的内容复制到`pages:[{},{},{},...]`

	```json
			{
				"path": "pages/pedometer/list",
				"style": {
					"navigationBarTitleText": "我的",
					"enablePullDownRefresh": true	// 下拉刷新
				}
			},
			{
				"path": "pages/pedometer/index",
				"style": {
					"navigationBarTitleText": "步数统计"
				}
			},
			{
				"path": "pages/pedometer/change",
				"style": {
					"navigationBarTitleText": "步数兑换",
					"enablePullDownRefresh": true	// 下拉刷新
				}
			}
	```

	

* 修改`uni-app/config/api.js`。

	```js
	// 1. 微信运动的接口,都需要登录。放到methodsToken中。
	'api.today.Werun',
	'api.update.Werun',
	'api.log.Werun',
	'api.statistic.Werun',
	'api.point.Werun',
	'api.collect.Werun',
	        
	// 2. 放到文件的最后
	// 同步步数
	export const werunUpdate = (data, callback) => post('api.update.Werun', data, callback);
	// 今日步数、天气、排行榜
	export const werunToday = (data, callback) => post('api.today.Werun', data, callback);
	// 统计页面
	export const werunStatistic = (data, callback) => post('api.statistic.Werun', data, callback);
	// 获取待兑换积分
	export const werunCollect = (data, callback) => post('api.collect.Werun', data, callback);
	// 兑换积分
	export const werunPoint = (data, callback) => post('api.point.Werun', data, callback);
	// 兑换记录
	export const werunLog = (data, callback) => post('api.log.Werun', data, callback);
	        
	```

* 将图片资源文件`./uni-app/my`文件夹移动到`uni-app/static/images/`下。

* 重新编译微信小程序，并上传。

* 在后台页面管理中添加一个按钮可跳转到步数记录的页面或在个人页面新增一个按钮跳转（路径：`/pages/pedometer/list`）