<template>
    <yd-cell-group>
        <yd-cell-item>
            <span slot="left">商品价格</span>
            <span slot="right">
                <yd-radio-group v-model="radio" color="#ff3b44">
                    <yd-radio val="1">仅退款</yd-radio>
                    <yd-radio val="2" disabled v-if="!delivery">退货退款</yd-radio>
                    <yd-radio val="2" v-else>退货退款</yd-radio>
                </yd-radio-group>
            </span>
        </yd-cell-item>
        <yd-cell-item>
            <span slot="left">退款金额</span>
            <span slot="right" v-if="radio === '1'">{{ price }}元</span>
            <yd-input slot="right" type="number" v-model="custPrice" v-else-if="radio === '2'" placeholder="请输入退款金额"></yd-input>
        </yd-cell-item>
    </yd-cell-group>
</template>

<script>
export default {
    data () {
        return {
            radio: '1',
            custPrice: ''
        }
    },
    props: {
        delivery: {
            type: Boolean,
            default () {
                return true
            }
        },
        price: {
            type: [String, Number],
            default () {
                return ''
            }
        }
    },
    watch: {
        radio () {
            this.$emit('type', this.radio)
        },
        custPrice () {
            this.$emit('custPrice', this.custPrice)
        }
    }
}
</script>
