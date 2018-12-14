<template>
    <yd-cell-group>
        <yd-cell-item>
            <span slot="left">售后类型</span>
            <span slot="right">
                <div class="service-type">
                    <input type="radio" v-model="checkedRadio" id="tk" value="1"/>
                    <label for="tk">仅退款</label>
                </div>
                <div v-if="!delivery" class="service-type disabled-input">
                    <input type="radio" v-model="checkedRadio" id="thtk" value="2" disabled/>
                    <label for="thtk">退货退款</label>
                </div>
                <div v-else class="service-type">
                    <input type="radio" v-model="checkedRadio" id="thtk2" value="2"/>
                    <label for="thtk2">退货退款</label>
                </div>
            </span>
        </yd-cell-item>

        <yd-cell-item>
            <span slot="left">退款金额</span>
            <!--<span slot="right" v-if="checkedRadio === '1'">{{ price }}元</span>-->
            <yd-input slot="right" type="number" v-if="checkedRadio === '1'" :placeholder="price + '元'" readonly></yd-input>
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
    .service-type{
        display: inline-block;
    }
    .service-type input[type="radio"] + label::before{
        content: "\a0"; /*不换行空格*/
        display: inline-block;
        vertical-align: top;
        font-size: 18px;
        width: .8em;
        height: .8em;
        margin-right: .2em;
        border-radius: 50%;
        border: 1px solid rgb(255, 59, 68);
        text-indent: .15em;
        line-height: 1;
        position: relative;
        top: 3px;
    }
    .service-type input[type="radio"]:checked + label::before {
        background-color: rgb(255, 59, 68);
        background-clip: content-box;
        padding: .1em;
    }
    .disabled-input input[type="radio"] + label::before{
        background-color: #ccc;
        background-clip: content-box;
        padding: .1em;
        border: 1px solid #ccc;

    }
</style>
