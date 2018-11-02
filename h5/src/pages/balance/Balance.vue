<template>
    <div class="balance">
        <div class="balance-top">
            <p>账户余额（元）</p>
            <h3>{{ balance }}</h3>
        </div>
        <div class="balance-bottom">
            <yd-cell-group>
                <!--<yd-cell-item href="/collect" type="link">
                    <img slot="icon" src="../../static/image/heart.png">
                    <span slot="left">充值</span>
                </yd-cell-item>-->
                <yd-cell-item href="/withdrawcash" type="link">
                    <img slot="icon" src="../../../static/image/tixian.png">
                    <span slot="left">提现</span>
                </yd-cell-item>
                <yd-cell-item href="/balancelist" type="link">
                    <img slot="icon" src="../../../static/image/mingxi.png">
                    <span slot="left">余额明细</span>
                </yd-cell-item>
                <yd-cell-item href="/cashlist" type="link">
                    <img slot="icon" src="../../../static/image/jilu.png">
                    <span slot="left">提现记录</span>
                </yd-cell-item>
            </yd-cell-group>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            balance: ''
        }
    },
    mounted () {
        this.getBalance()
    },
    methods: {
        getBalance () {
            this.$api.userInfo({}, res => {
                if (res.status) {
                    this.balance = res.data.balance
                }
            })
        }
    }
}
</script>

<style>
    .balance-top{
        padding: .3rem .2rem;
        text-align: left;
        background-color: #f95b62;
        color: #fff;
    }
    .balance-top h3{
        font-size: .7rem;
        margin: .5rem 0 0;
    }
</style>
