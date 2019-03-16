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
import {mapGetters} from 'vuex'

export default {
    components: {
        navbar, tabbar
    },
    computed: {
        ...mapGetters([
            'shopName',
            'shopDesc',
            'shopLogo',
			'statistics'
        ])
    },
    beforeMount () {
        this.getShopSetting();
    },
    methods: {
        // 获取店铺配置 存入vuex
        getShopSetting () {
            this.$api.shopConfig().then(res => {
                this.$store.dispatch('shopConfig', res)
                if (this.$route.path === '/index') {
                    document.title = this.shopName
                }
                //百度统计
                if(res.statistics){
                    var script=document.createElement("script");
                    script.innerHTML = res.statistics;
                    document.getElementsByTagName("body")[0].appendChild(script);
                }
            });
        }
    },
    watch: {
        '$route': {
            handler () {
                document.title = this.$route.path === '/index' ? this.shopName : this.$route.meta.title
            }
        }
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
