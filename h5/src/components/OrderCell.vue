<template>
    <div class="ordercell">
        <yd-cell-group>
            <yd-cell-item v-if="parseFloat(goods_amount)">
                <span slot="left">商品价格</span>
                <span slot="right" class="demo-list-price">￥{{ this.GLOBAL.formatMoney(goods_amount, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item v-if="parseFloat(goods_pmt)">
                <span slot="left">商品优惠</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(goods_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item v-if="parseFloat(order_pmt)">
                <span slot="left">订单优惠</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(order_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item v-if="parseFloat(cost_freight)">
                <span slot="left">快递运费</span>
                <span slot="right" class="demo-list-price">+ ￥{{ this.GLOBAL.formatMoney(cost_freight, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item v-if="parseFloat(coupon_pmt)">
                <span slot="left">优惠券</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(coupon_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item v-if="parseFloat(point_pmt)">
                <span slot="left">积分抵扣</span>
                <span slot="right" class="demo-list-price">- ￥{{ this.GLOBAL.formatMoney(point_pmt, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">订单价格</span>
                <span slot="right" class="demo-list-price">￥{{ this.GLOBAL.formatMoney(amount, 2, '') }}</span>
            </yd-cell-item>
            <yd-cell-item v-if="open_point && usable_point"  type="checkbox">
                <span slot="left">积分抵扣</span>
                <span slot="right">可用{{ usable_point }}积分 抵扣￥{{ this.GLOBAL.formatMoney(point_money, 2, '') }}&nbsp;</span>
                <input slot="right" type="checkbox" v-model="checked" />
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
        // 订单优惠
        order_pmt: {
            type: [Number, String],
            require: true
        },
        // 优惠券优惠金额
        coupon_pmt: {
            type: [Number, String],
            require: false
        },
        // 订单页积分抵扣
        point_pmt: {
            type: String,
            default () {
                return ''
            }
        },
        // 是否开启积分抵扣
        open_point: {
            type: Boolean,
            default: () => {
                return false
            }
        },
        // 总积分
        point_sum: {
            type: Number,
            default: () => {
                return 0
            }
        },
        // 可抵扣的积分
        usable_point: {
            type: Number,
            default: () => {
                return 0
            }
        },
        // 积分抵扣的金额
        point_money: {
            type: Number,
            default: () => {
                return 0
            }
        },
        use_point: {
            type: Boolean,
            default () {
                return false
            }
        }
    },
    data () {
        return {
            checked: false
        }
    },
    watch: {
        checked () {
            this.$emit('isUsePoint', this.checked)
        }
    }
}
</script>

<style>
    .orderscore{
        background-color: #fff;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
</style>
