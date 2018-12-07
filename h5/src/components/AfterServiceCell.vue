<template>
    <yd-cell-group>
        <label><input type="radio" v-model="checkedRadio" value="1"/>仅退款</label>
        <label v-if="!delivery"><input type="radio" v-model="checkedRadio" value="2" disabled/>退货退款</label>
        <label v-else><input type="radio" v-model="checkedRadio" value="2"/>退货退款</label>
        <yd-cell-item>
            <span slot="left">商品价格</span>
            <span slot="right">
            </span>
        </yd-cell-item>
        <yd-cell-item>
            <span slot="left">退款金额</span>
            <span slot="right" v-if="checkedRadio === '1'">{{ price }}元</span>
            <yd-input slot="right" type="number" v-model="custPrice" v-else-if="checkedRadio === '2'" placeholder="请输入退款金额"></yd-input>
        </yd-cell-item>
    </yd-cell-group>
</template>

<script>
export default {
    data () {
        return {
            checkedRadio: '1',
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
        checkedRadio () {
            this.$emit('type', this.checkedRadio)
        },
        custPrice () {
            this.$emit('custPrice', this.custPrice)
        }
    }
}
</script>

<style>
</style>
