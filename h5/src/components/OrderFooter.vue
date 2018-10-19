<template>
    <div class="orderfooter">
        <div class="orderfooter-left">
            <p>需付：</p><span>￥{{ this.GLOBAL.formatMoney(amount, 2, '') }}</span>
        </div>
        <div class="orderfooter-right">
            <yd-button type="danger" @click.native="pay">立即支付</yd-button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        amount: [Number, String],
        require: true
    },
    methods: {
        pay () {
            this.$emit('payment')
        }
    }
}
</script>
