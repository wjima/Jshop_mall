<template>
    <div class="tabbar" ref="tabbar" slot="tabbar">
        <router-link tag="div" class="tabbar-item" :id="item.active" v-for="(item, index) in tab" :to="item.link" :key="index">
            <i :class="item.type"></i>
            <span>{{item.title}}</span>
        </router-link>
    </div>
</template>

<style type="text/css">
    .tabbar{
        background-color: #fff;
        border-top: 1px solid #e1e1e1;
        z-index: 1000;
    }
    .tabbar-item{
        display: inline-block;
        width: 25%;
        box-sizing: border-box;
        padding: .15rem 0 .05rem;
    }
    .tabbar-item>i{
        font-size: .5rem;
        color: #999;
    }
    .tabbar-item>span{
        display: block;
        color: #999;
    }
    #true>i, #true>span{
        color: #FF3B44;
    }
</style>

<script type="text/babel">
export default {
    data () {
        return {
            tab: [
                {title: '首页', link: '/index', type: 'iconfont icon-shouyeshouye1', active: true},
                {title: '全部分类', link: '/classify', type: 'iconfont icon-fenlei1', active: false},
                {title: '购物车', link: '/cart', type: 'iconfont icon-gouwuche', active: false},
                {title: '个人中心', link: '/user', type: 'iconfont icon-icongerenzhongxin', active: false}
            ]
        }
    },
    // 首次进入页面显示页面title及选中
    mounted () {
        if (this.$route.meta.title) {
            document.title = this.$route.meta.title
        }
        for (let k in this.tab) {
            if (this.tab[k].link === this.$route.path) {
                this.tab[k].active = true
            } else {
                this.tab[k].active = false
            }
        }
    },
    // 监听路由变化 修改选中
    watch: {
        '$route' () {
            for (let k in this.tab) {
                if (this.tab[k].link === this.$route.path) {
                    this.tab[k].active = true
                } else {
                    this.tab[k].active = false
                }
            }
        }
    }
}
</script>
