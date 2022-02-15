import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		config: {}, // 店铺配置信息
		orderTab: 0, // 选中的订单tab页
		redirectPage: '',
		uuid:'',//当前客户端
		searchStyle: '',
		searchFixed:false,//搜索框样式
		userShip: {}, //地区信息
		invoice: {}, //发票信息
		shopAddress: {}, // 选择门店地址
		skin: `
			--nav-bg:#42b983;
			--nav-color:#ffffff;
		`
	},
    mutations: {
		// 皮肤更换
		skinPeeler(state,skin = []){
			//console.log('skin', skin);
			// 将皮肤配置JSON转为以 ; 分割的字符串（style 值）
			let style = skin.map((item,index)=>{
				return `${item.name}:${item.value}`
			}).join(";");
			state.skin = style;
			// console.log(skin[0].value);
			let backgroundColor = `${skin[0].value}`
			uni.setNavigationBarColor({
				frontColor: '#ffffff',
				backgroundColor
			})
		},
		config (state, payload) {
			state.config = payload
		},
		orderTab (state, tab) {
			state.orderTab = tab
		},
		redirect (state, payload) {
			state.redirectPage = payload.page
		},
		searchStyle (state, style) {
			state.searchStyle = style
		},
		searchFixed (state, payload) {
			state.searchFixed = payload
		},
		userShip (state, userShip) {
			state.userShip = userShip
		},
		invoice (state, invoice) {
			state.invoice = invoice
		},
		changeAddress (state, value) {
			state.shopAddress = value
		}
	},
	actions: {

	},
	getters: {
		shopConfig: state => state.config,
		uuid: state	=>	state.uuid,
	}
})

export default store
