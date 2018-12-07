<template>
    <div class="searchpage">
        <div class="searchpage-header">
            <!--<yd-search v-model="search" placeholder="搜索商品" class="search"></yd-search>-->
            <div class="search-input">
                <input v-model="keys" type="text" placeholder="请输入提货单号、订单号、提货人手机号" @keyup.enter="searchHandler"/>
            </div>
            <button class="searchimg" @click="searchHandler"><img src="../../../static/image/search.png"/></button>
        </div>
        <div class="searchpage-body">
            <div v-if="Object.keys(info).length">
                <p>提货人: {{info.name }}</p>
                <p>联系电话: {{ info.mobile }}</p>
                <yd-list theme="4">
                    <yd-list-item v-for="(val, key) in info.goods" :key="key">
                        <img slot="img" :src="val.image_url">
                        <h3 class="goodsname" slot="title">{{ val.name }}</h3>
                        <p class="goods" slot="title">{{ val.addon }}</p>
                        <p class="goods" slot="title">BN: {{ val.bn }}</p>
                        <p class="goods" slot="title">SN: {{ val.sn }}</p>
                        <yd-list-other slot="other">
                            <div>
                                <span class="demo-list-price"><em>¥</em>{{ val.price }}</span>
                            </div>
                            <div>x {{ val.nums }}</div>
                        </yd-list-other>
                    </yd-list-item>
                </yd-list>
            </div>
        </div>
        <yd-button size="large" type="danger" :disabled="isDisabled" @click.native="verification(info.id)">确认核销</yd-button>

    </div>
</template>

<script>
export default {
    name: "OrderVerification",
    data () {
        return {
            id: this.$route.query.id,
            keys: '', // 提货单查询关键字
            info: {}, // 提货单详情
            disabled: false
        }
    },
    mounted () {
        if (this.id) {
            this.keys = this.id
            this.getLadingInfo()
        }
    },
    computed: {
        isDisabled () {
            if (!this.keys || !Object.keys(this.info).length) {
                return true
            } else {
                return false
            }
        }
    },
    methods: {
        // 获取提货单详情
        getLadingInfo () {
            this.$api.ladingInfo({key: this.keys}, res => {
                this.info = res.data
                if (!res.status) {
                    this.$dialog.alert({mes: '提货单不存在!'})
                }
            })
        },
        // 提货单搜索
        searchHandler () {
            if (!this.keys) {
                this.$dialog.toast({
                    mes: '请输入提货单号、订单号、提货人手机号进行查询',
                    timeout: 1300
                })
                return false
            }
            this.getLadingInfo()
        },
        // 指定提货操作
        verification (id) {
            this.$dialog.confirm({
                title: '提示',
                mes: '确认执行提货单核销操作吗?',
                opts: () => {
                    this.$api.ladingExec({lading_id: this.info.id}, res => {
                        if (res.status) {
                            this.$dialog.toast({
                                mes: res.msg,
                                timeout: 1300,
                                callback: () => {
                                    this.getLadingInfo()
                                }
                            })
                        }
                    })
                }
            })
        }
    }
}
</script>

<style scoped>
    .searchpage{
        overflow: hidden;
    }
    .searchpage .search-input{
        position: relative;
    }
    .searchpage .search-input i{
        position: absolute;
        top: .32rem;
        left: .4rem;
        z-index: 1000;
    }
    .searchpage .search-input .search-icon:after{
        font-size: .35rem;
    }
    .searchpage .search-input input{
        height: .7rem;
        width: 85%;
        border: none;
        background-color: #fff;
        border-radius: 2rem;
        position: absolute;
        top: .15rem;
        left: .2rem;
        opacity: .7;
        padding: 0 .7rem;
    }
    .searchpage-body{
        margin-top: 1rem;
    }
</style>
