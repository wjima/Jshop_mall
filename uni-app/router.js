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
	if (systemStatus === 200) { // 已经登录
		next();
	} else {
		console.log("系统出错了");
	}
}

const router = createRouter({
	platform: process.env.VUE_APP_PLATFORM,
	routes: [...ROUTES]
});

//全局路由前置守卫
router.beforeEach((to, from, next) => {
	appOnLaunch++;
	let userToken = uni.getStorageSync('userToken');
	let setArea = uni.getStorageSync('setArea');
	if (userToken && !setArea) {
		getArea();
	}
	if (appOnLaunch === 1) { //第一次启动
		getConfig(to, from, next);
	} else {
		//console.log('跳转前')
		next();
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
