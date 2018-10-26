<template>
    <div class="ordercell">
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">商品价格</span>
                <span slot="right" class="demo-list-price">￥{{ this.GLOBAL.formatMoney(goods_amount, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">商品优惠</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(goods_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">订单优惠</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(order_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">快递运费</span>
                <span slot="right" class="demo-list-price">+ ￥{{ this.GLOBAL.formatMoney(cost_freight, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">优惠金额</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(coupon_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">订单价格</span>
                <span slot="right" class="demo-list-price">￥{{ this.GLOBAL.formatMoney(amount, 2, '') }}</span>
            </yd-cell-item>
        </yd-cell-group>
    </div>
</template>

<script>
export default {
    props: {
        // 商品价格
        goods_amount: {
            type: [Number, String],
            require: true
        },
        // 快递运费
        cost_freight: {
            type: [Number, String],
            require: true
        },
        // 总价/订单价格
        amount: {
            type: [Number, String],
            require: true
        },
        // 商品优惠
        goods_pmt: {
            type: [Number, String],
            require: true
        },
        order_pmt: {
            type: [Number, String],
            require: true
        },
        coupon_pmt: {
            type: [Number, String],
            require: false
        }
    }
}
</script>

<style>
</style>
