<template>
    <div class="deliveryinformation">
        <img class="delivery-car" src="../../static/image/car-green.png"/>
        <div class="deliveryinformation-content" v-if="status === 'pending_payment'">
            <div class="deliveryinformation-top">
                <span>付款后将为你安排发货</span>
            </div>
        </div>
        <div class="deliveryinformation-content" v-else-if="status === 'pending_delivery'">
            <div class="deliveryinformation-top">
                <span>正在为你忙碌打包中</span>
            </div>
        </div>
        <div class="deliveryinformation-content" v-else-if="status === 'pending_receipt'">
            <div v-if="Object.keys(express).length">
                <div class="deliveryinformation-top">
                    <span>{{ express.context }}</span>
                </div>
                <div class="deliveryinformation-bottom">
                    <p>{{ express.time }}</p>
                    <img class="orderadd-right right-img" src="../../static/image/right.png" @click="logistics"/>
                </div>
                </div>
            <div v-else>
                <div class="deliveryinformation-top">
                    <span>已为你发货，请注意查收</span>
                </div>
                <div class="deliveryinformation-bottom">
                    <p>{{ this.GLOBAL.timeToDate(delivery[0].ctime) }}</p>
                    <img class="orderadd-right right-img" src="../../static/image/right.png"/>
                </div>
            </div>
        </div>
        <div class="deliveryinformation-content" v-else-if="status === 'cancel'">
            <div class="deliveryinformation-top">
                <span>订单已取消</span>
            </div>
        </div>
        <div class="deliveryinformation-content" v-else>
            <div class="deliveryinformation-top">
                <span>你已签收</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        // 状态
        status: {
            type: [Array, String],
            default () {
                return []
            }
        },
        // 发货快递单号信息
        delivery: {
            type: [Array, Object],
            default () {
                return []
            }
        },
        // 物流信息
        express: {
            type: Object,
            default () {
                return {}
            }
        }
    },
    methods: {
        logistics () {
            this.$emit('logistics')
        }
    }
}
</script>

<style>
</style>
