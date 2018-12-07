// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import routers from './router/routers'
import VueRouter from 'vue-router'
import * as Api from './common/api'
/* 相当于import YDUI from 'vue-ydui/ydui.rem.js' */
import YDUI from 'vue-ydui'
import 'vue-ydui/dist/ydui.rem.css'
import Common from './common/common'
import Share from 'vue-social-share'
import 'vue-social-share/dist/client.css'
import Mui from 'vue-awesome-mui'
import VueQriously from 'vue-qriously'
import VueLazyload from 'vue-lazyload'
import store from './store'

Vue.use(VueQriously)
Vue.use(Mui)
Vue.use(Share)
Vue.use(VueRouter)
Vue.use(YDUI)
Vue.use(VueLazyload, {
    preLoad: 1.3,
    loading: './static/image/loading.gif',
    attempt: 1
})
Vue.config.productionTip = false
Vue.prototype.$api = Api
Vue.prototype.GLOBAL = Common


const router = new VueRouter({
    mode: 'hash',
    routes: routers,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        }
        return { x: 0, y: 0 }
    }
})

const that = new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>'
})

router.beforeEach((route, redirect, next) => {
    document.title = route.meta.title ? route.meta.title : ''
    // 如果将要跳转的页面需要登录 用户没有登录将跳转登录页面
    if (route.meta.isLogin) {
        if (!Common.getStorage('user_token')) {
            Common.jumpToLogin()
        }
    }
    // 如果未匹配到路由 跳转至首页 (防止用户手动输入地址出错)
    if (route.matched.length === 0) {
        redirect.name ? next({ name: redirect.name }) : next('/index')
    } else {
        next()
    }
})

export default that
