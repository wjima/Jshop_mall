<template>
    <div v-if="payments.length">
        <div class="payment" v-for="(item, index) in payments" :key="index"  @click="payment(item.code)">
            <img class="payment-img" :src="item.img"/>
            <div class="payment-left">
                <h3>{{ item.name }}</h3>
                <p>{{ item.memo }}</p>
            </div>
            <img class="right-img" src="../../static/image/right.png"/>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        payments: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    },
    methods: {
        payment (code) {
            this.$emit('pay', code)
        }
    }
}
</script>

<style>
    .payment{
        background-color: #fff;
        text-align: left;
        padding: .35rem .2rem;
        position: relative;
        margin-bottom: 1px;
    }
    .payment-img{
        width: 1rem;
        height: auto;
        padding-right: .15rem;
    }
    .payment-left{
        display: inline-block;
        padding-left: .2rem;
        border-left: 1px solid #F2F2F2;
    }
    .payment-left h3{
        margin-bottom: .15rem;
        font-size: .28rem;
    }
    .payment .right-img{
        right: .2rem;
    }
</style>
