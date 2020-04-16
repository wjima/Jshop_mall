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
	},
    mutations: {
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
