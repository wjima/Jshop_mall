// router.js
let [systemStatus, appOnLaunch] = [null, 0];
import {
	RouterMount,
	createRouter
} from 'uni-simple-router';

import {
	apiBaseUrl
} from './config/config.js'
import store from './store'


//获取系统配置
const isGetConfig = async function() {
	return uni.request({
		url: apiBaseUrl + '/api/common/jshopConf'
	})
}

//获取地址库
const getArea = function() {
	uni.setStorageSync('setArea', true);
	uni.request({
		url: apiBaseUrl + '/api.html&method=user.getarealist'
	}).then((res) => {
		uni.setStorageSync('areaList', res[1].data.data);
	})
}

const getConfig = async function(to, from, next) {
	if (systemStatus == null) {
		const data = await isGetConfig();
		systemStatus = data[1].statusCode;
		store.commit('config', data[1].data)
	}
	//console.log('systemStatus',systemStatus);
	if (systemStatus === 200) { // 已经登录
		next();
	} else {
		console.log("系统出错了");
	}
}


const router = createRouter({
	platform: process.env.VUE_APP_PLATFORM,
	routes: [...ROUTES],
	//debugger: true,
	beforeProxyHooks: { //2.0.8版本生效，如果有bug，需要还原至2.0.7
		onLoad: function(options, next, router) {
			let query = {};
			//url参数还原
			Object.keys(router.currentRoute.query).forEach(function(key) {
				query[key] = decodeURIComponent(router.currentRoute.query[key]);
			});
			next([query]);
		},
		onShow: function(options, next, router) {
			const args = options || router.currentRoute.query;
			next([args]);
		}
	}
});

//全局路由前置守卫
router.beforeEach((to, from, next) => {
	appOnLaunch++;
	//to.fullPath = decodeURI(to.fullPath);

	let userToken = uni.getStorageSync('userToken');
	let setArea = uni.getStorageSync('setArea');
	if (userToken && !setArea) {
		getArea();
	}
	if (appOnLaunch === 1) { //第一次启动
		getConfig(to, from, next);
	} else {
		console.log(to);
		next();
		//next();
	}
});
// 全局路由后置守卫
router.afterEach((to, from) => {

	//console.log('跳转结束')
})

export {
	router,
	RouterMount
}
