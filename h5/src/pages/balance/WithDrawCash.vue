<template>
    <div class="withdrawcash">
        <div class="withdrawcash-top" @click="showHandler" v-if="card.id">
            <img class="banklogo" :src="card.bank_logo"/>
            <div class="bankcard">
                <span>{{ card.bank_name }}</span>
                <p>{{ card.card_number }} {{ card.card_type }}</p>
            </div>
            <img src="../../../static/image/right.png" class="right-img"/>
        </div>
        <div class="withdrawcash-top" v-else>
            <div style="text-align: center">
                <yd-button size="small" type="hollow" shape="circle" @click.native="showHandler">选择银行卡</yd-button>
            </div>
        </div>
        <div class="withdrawcash-mid">
            <p>提现金额</p>
            <div class="withdrawcash-input">
                <span>￥</span><input type="number" v-model="money"/>
            </div>
            <p v-show="!showError">可用余额 {{ available }} 元</p>
            <p v-show="showError" style="color: #f00;">金额已超过可用余额</p>
        </div>
        <div class="withdrawcash-bottom">
            <div style="margin: 15px">
                <yd-button size="large" type="danger" v-show="isSubmit" @click.native="submitHandler">确认提现</yd-button>
                <yd-button size="large" type="disabled" disabled v-show="!isSubmit">确认提现</yd-button>
            </div>
        </div>
        <!-- 用户的银行卡列表 -->
        <yd-popup v-model="showWindow" position="bottom" width="20%" height="60%">
            <div class="cardlist">
                <div v-if="cardList.length">
                    <div class="cardlist-item" v-for="(item, index) in cardList" :key="index" @click="selectHandler(index)">
                        <img class="banklogo" :src="item.bank_logo"/>
                        <div class="bankcard">
                            <yd-badge shape="square" type="primary" v-if="item.is_default === 1">默认</yd-badge>
                            <span>{{ item.bank_name }}</span>
                            <p>{{ item.card_number }} {{ item.card_type }}</p>
                        </div>
                    </div>
                </div>
                <div v-else style="text-align: center">
                    没有银行卡
                </div>
                <div style="margin: 15px">
                    <yd-button size="large" type="danger" @click.native="addCard">添加银行卡</yd-button>
                </div>
            </div>
        </yd-popup>
    </div>
</template>

<script>
export default {
    data () {
        return {
            card: [], // 默认的银行卡
            cardList: [], // 用户所有可用银行卡列表
            money: '', // 用户输入的提现金额
            showWindow: false, // 是否显示银行卡列表
            showError: false, // 当提现金额大于可用余额 显示错误提示
            isSubmit: false, // 提现点击
            available: '' // 用户可用余额 最大提现额不得超过此余额
        }
    },
    mounted () {
        this.defaultCard()
        this.getBalance()
    },
    methods: {
        // 获取默认的银行卡
        defaultCard () {
            this.$api.getDefaultBankCard({}, res => {
                this.card = res.data
            })
        },
        // 获取用户的可用余额
        getBalance () {
            this.$api.userInfo({}, res => {
                this.available = res.data.balance || 0
            })
        },
        // 用户提现 提交处理
        submitHandler () {
            if (!this.card) {
                this.$dialog.toast({mes: '请选择要提现的银行卡', timeout: 1000})
            } else if (!this.money) {
                this.$dialog.toast({mes: '请输入要提现的金额', timeout: 1000})
            } else if (Number(this.money) === 0) {
                this.$dialog.toast({mes: '提现金额不能为0', timeout: 1000})
            } else {
                this.$api.userToCash({
                    money: this.money,
                    cardId: this.card.id
                }, res => {
                    if (res.status) {
                        this.$router.go(-1)
                        this.$dialog.toast({mes: '提现成功', timout: 1000})
                    }
                })
            }
        },
        // 显示银行卡列表
        showHandler () {
            this.showWindow = true
            this.bankCardList()
        },
        // 获取银行卡列表
        bankCardList () {
            this.$api.getBankCardList({}, res => {
                this.cardList = res.data
            })
        },
        // 点击列表选择的银行卡
        selectHandler (index) {
            this.showWindow = false
            this.card = this.cardList[index]
        },
        // 添加银行卡
        addCard () {
            this.$router.push({path: '/bankcard'})
        }
    },
    watch: {
        // 监听用户输出金额 更新渲染
        money () {
            if (this.money === '') {
                this.isSubmit = false
            } else {
                this.isSubmit = true
            }
            // 比较用户的输入金额 如果大于可用金额
            if (Number(this.money) > Number(this.available)) {
                this.showError = true
                this.isSubmit = false
            } else {
                this.showError = false
            }
        }
    }
}
</script>

<style>
    .withdrawcash-top{
        background-color: #fff;
        text-align: left;
        padding: .2rem;
        position: relative;
        margin-top: .2rem;
    }
    .withdrawcash .banklogo{
        width: 2.2rem;
        height: .6rem;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
    .bankcard{
        display: inline-block;
        margin-left: .7rem;
    }
    .bankcard p{
        margin-top: .1rem;
        color: #999;
    }
    .withdrawcash-mid{
        text-align: left;
        margin-top: .2rem;
        padding: .2rem;
        background-color: #fff;
    }
    .withdrawcash-mid p{
        margin-bottom: .1rem;
        color: #666;
    }
    .withdrawcash-input{
        font-size: .8rem;
        position: relative;
    }
    .withdrawcash-input span{
        position: absolute;
        font-size: .7rem;
        top: 50%;
        transform: translateY(-50%);
    }
    .withdrawcash-input input{
        border: none;
        border-bottom: 1px solid #e5e5e5;
        padding: 0.05rem 0 .1rem .8rem;
        width: 100%;
    }
    .withdrawcash-bottom .yd-btn-block{
        height: .8rem;
        font-size: .3rem;
    }
    .cardlist{
        text-align: left;
    }
    .cardlist .cardlist-item{
        background-color: #fff;
        padding: .2rem;
        position: relative;
        border-bottom: 1px solid #e5e5e5;
    }
    .cardlist .banklogo{
        height: .6rem;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
    .bankcard{
        display: inline-block;
        margin-left: 2.4rem;
    }
    .bankcard p{
        margin-top: .1rem;
        color: #999;
    }
    .card-select{
        width: .3rem;
        height: .3rem;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: .2rem;
    }
    .cardlist .yd-btn-block{
        height: .8rem;
        font-size: .3rem;
    }
</style>
