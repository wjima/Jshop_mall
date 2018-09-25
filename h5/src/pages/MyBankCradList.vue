<template>
    <div class="mycardlist">
        <div class="card-detail" v-if="cardList.length">
            <div class="cardlist-item" v-for="(item, index) in cardList" :key="index">
                <img class="banklogo" :src="item.bank_logo"/>
                <div class="bankcard">
                    <!--<span>{{ item.bank_name }}</span>-->
                    <p>{{ item.card_type }}</p>
                    <p>{{ item.card_number }}</p>
                </div>
                <div class="bankcard-btn">
                    <span class="default-bankcard" v-if="item.is_default === 1">默认</span>
                    <button class="install-bankcard" v-if="item.is_default === 2" @click="setDefault(item.id)">设为默认</button>
                    <button class="delete-bankcard" @click="delCard(item.id)">删除</button>
                </div>
            </div>
        </div>
        <div v-else>
            没有银行卡
        </div>
        <div style="margin: 15px">
            <yd-button size="large" type="danger" @click.native="addCard">添加银行卡</yd-button>
        </div>
    </div>
</template>

<script>
export default{
    data () {
        return {
            cardList: [], // 我的银行卡列表
            isShow: false
        }
    },
    mounted () {
        this.bankCardList()
    },
    methods: {
        // 获取银行卡列表
        bankCardList () {
            this.$api.getBankCardList({}, res => {
                this.cardList = res.data
            })
        },
        // 新添加银行卡
        addCard () {
            this.$router.push({path: '/bankcard'})
        },
        // 设置默认银行卡
        setDefault (id) {
            this.$api.setDefaultBankCard({id: id}, res => {
                if (res.status) {
                    this.$dialog.toast({
                        mes: res.msg,
                        timeout: 1000,
                        callback: () => {
                            this.bankCardList()
                        }
                    })
                }
            })
        },
        // 删除银行卡
        delCard (id) {
            this.$dialog.confirm({
                mes: '确定删除该银行卡?',
                opts: [
                    {
                        txt: '取消',
                        color: false
                    },
                    {
                        txt: '确定',
                        color: true,
                        callback: () => {
                            this.$api.removeBankCard({id: id}, res => {
                                if (res.status) {
                                    this.bankCardList()
                                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
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

<style>
    .card-detail{
        padding: .2rem;
    }
    .mycardlist .cardlist-item{
        width: 100%;
        background-color: #fff;
        border-radius: .2rem;
        margin: 0 auto;
        margin-bottom: .2rem;
        position: relative;
        padding: .7rem .4rem 1rem;
    }
    .mycardlist .cardlist-item img{
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: .3rem;
    }
    .mycardlist .cardlist-item .bankcard{
        display: inline-block;
        margin-left: 2.7rem;
    }
    .mycardlist .cardlist-item .bankcard span{
        font-size: .3rem;
        margin-bottom: .1rem;
        display: block;
    }
    .mycardlist .cardlist-item .bankcard p{
        font-size: .3rem;
        margin-bottom: .15rem;
        color: #666;
    }
    .mycardlist .yd-btn-block{
        height: .8rem;
        font-size: .3rem;
    }
    .bankcard-btn{
        position: absolute;
        bottom: .2rem;
        right: .2rem;
    }
    .bankcard-btn button{
        border: none;
        padding: .08rem .1rem;
        background: #ef4f4f;
        color: #fff;
        border-radius: .1rem;
        margin-left: .2rem;
    }
    .default-bankcard{
        color: #fff;
        border-radius: .1rem;
        margin-left: .2rem;
        padding: .08rem .1rem;
        background-color: #ccc !important;
    }
</style>
