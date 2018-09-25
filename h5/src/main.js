// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import routers from './router/routers'
import VueRouter from 'vue-router'
import Api from './common/api'
/* 相当于import YDUI from 'vue-ydui/ydui.rem.js' */
import YDUI from 'vue-ydui'
import 'vue-ydui/dist/ydui.rem.css'
import Common from './common/common'
import Share from 'vue-social-share'
import 'vue-social-share/dist/client.css'
import Mui from 'vue-awesome-mui'
import VueQriously from 'vue-qriously'

Vue.use(VueQriously)
Vue.use(Mui)
Vue.use(Share)
Vue.use(VueRouter)
Vue.use(YDUI)
Vue.config.productionTip = false
Vue.prototype.$api = Api
Vue.prototype.GLOBAL = Common

const router = new VueRouter({
    mode: 'hash',
    routes: routers,
    scrollBehavior (to, from, savedPosition) {
        console.log(savedPosition)
        if (savedPosition) {
            return savedPosition
        }
        return {x: 0, y: 0}
    }
})

const that = new Vue({
    el: '#app',
    router,
    components: { App },
    template: '<App/>'
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title ? to.meta.title : ''
    // 如果将要跳转的页面需要登录 用户没有登录将跳转登录页面
    console.log(this)
    if (to.meta.isLogin) {
        if (!Common.getStorage('user_token')) {
            Common.jumpToLogin()
        }
    }
    // 如果未匹配到路由 跳转至首页 (防止用户手动输入地址出错)
    if (to.matched.length === 0) {
        from.name ? next({name: from.name}) : next('/index')
    } else {
        next()
    }
})

export default that
