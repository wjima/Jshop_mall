<template>
    <div class="">
        <yd-cell-group>
            <yd-cell-item href="/mybankcardlist" type="link">
                <img slot="icon" src="../../../static/image/bankcard.png">
                <span slot="left">我的银行卡</span>
            </yd-cell-item>
            <yd-cell-item href="/recommendlist" type="link">
                <img slot="icon" src="../../../static/image/coupon.png">
                <span slot="left">推荐记录</span>
            </yd-cell-item>
            <yd-cell-item href="/datasetting" type="link">
                <img slot="icon" src="../../../static/image/set.png">
                <span slot="left">资料设置</span>
            </yd-cell-item>
        </yd-cell-group>
        <div style="margin: 15px">
            <yd-button size="large" type="danger" @click.native="logout">退出登录</yd-button>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        logout () {
            this.$dialog.confirm({
                mes: '确认退出吗?',
                opts: [
                    {
                        txt: '取消',
                        color: false
                    },
                    {
                        txt: '确定',
                        color: true,
                        callback: () => {
                            this.$api.logout({}, res => {
                                if (res.status) {
                                    this.GLOBAL.removeStorage('user_token')
                                    this.GLOBAL.removeStorage('login-type')
                                    this.$router.replace('/login')
                                }
                            })
                        }
                    }
                ]
            })
        }
    }
}
</script>

<style scoped>

</style>
