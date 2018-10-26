import Vue from 'vue'
import Router from 'vue-router'
import Index from '@/pages/Index'
import Classify from '../pages/Classify'
import Cart from '../pages/Cart'
import User from '../pages/User'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Index',
            component: Index
        },
        {
            path: '/classify',
            name: 'Classify',
            component: Classify
        },
        {
            path: '/cart',
            name: 'Cart',
            component: Cart
        },
        {
            path: '/user',
            name: 'User',
            component: User
        }
    ]
})
