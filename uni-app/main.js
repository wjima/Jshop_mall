import Vue from 'vue'
import App from './App'
import * as Api from './config/api.js'

import * as Common from './config/common.js'
import * as Db from './config/db.js'
import * as Config from './config/config.js'
import store from './store'
import './common/uni-H5Api'
// #ifdef H5
import * as wx from './static/js/wx.js'
// #endif
Vue.config.productionTip = false
Vue.prototype.$api = Api;
Vue.prototype.$common = Common;
Vue.prototype.$db = Db;
Vue.prototype.$config = Config;
Vue.prototype.$store = store;
// #ifdef H5
Vue.prototype.$wx = wx;
// #endif

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
