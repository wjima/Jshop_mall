<template>
    <yd-layout id="app">
        <navbar slot="navbar" :title="$route.meta.title" v-show="$route.meta.navShow"></navbar>
        <transition name="router-fade" mode="out-in">
            <keep-alive>
                <router-view v-if="$route.meta.keepAlive" class="top"></router-view>
            </keep-alive>
        </transition>
        <transition name="router-fade" mode="out-in">
            <router-view v-if="!$route.meta.keepAlive" class="top"></router-view>
        </transition>
        <tabbar slot="tabbar" v-show="$route.meta.tabShow"></tabbar>
    </yd-layout>
</template>

<script>
import navbar from './components/NavBar.vue'
import tabbar from './components/TabBar.vue'

export default {
    components: {
        navbar, tabbar
    },
    methods: {
        getShopName () {
            var shop_name = ''
            this.$api.getSetting({key: 'shop_name'}, res => {
                if (res.data !== '') {
                    this.GLOBAL.setStorage('shop_name', res.data)
                }
                shop_name = res.data
            })
            return shop_name
        }
    },
    watch: {
        '$route' :{
            handler () {
                if (this.$route.path === '/index') {
                    document.title = this.GLOBAL.getStorage('shop_name')
                    ? this.GLOBAL.getStorage('shop_name')
                    : this.getShopName()
                }
            }
        }
    },
    beforeDestroy() {
        this.GLOBAL.removeStorage('shop_name')
    }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  background-color: #F9F9F9;
}
#scrollView{
    margin-top: 0;
}
.router-fade-enter-active, .router-fade-leave-active {
    transition: opacity .3s;
}
.router-fade-enter, .router-fade-leave-active {
    opacity: 0;
}
</style>
