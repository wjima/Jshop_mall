<template>
	<yd-navbar slot="navbar" :title="title">
        <div slot="left" @click="back">
            <yd-navbar-back-icon></yd-navbar-back-icon>
        </div>
        <!--<router-link to="/user" slot="right">
            <yd-icon name="ucenter-outline" size="25px" color="#777"></yd-icon>
        </router-link>-->
    </yd-navbar>
</template>

<script>
export default {
    props: ['title'],
    methods: {
        back () {
            if (window.history.length <= 1) {
                this.$router.push({path:'/'})
                return false
            } else {
                this.$router.go(-1)
            }
            // 上面都没执行就说明卡在当前页不是最后一条， histroy记录数量大于1，又没有回退记录，只能返回首页，
            // 如果上面都执行了 页面都跳走了，这个也就不用管了
            // setTimeout(() => {
            //     this.$router.push({path:'/'})
            // },500)
        }
    }
}
</script>

<style>
</style>
