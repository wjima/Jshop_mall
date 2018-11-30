<template>
    <div class="orderdetail">
        <orderdetailheader
            :status="order.text_status"
            :remaining="order.remaining"
            :delivery="order.delivery"
        ></orderdetailheader>
        <deliveryinformation
            :status="order.text_status"
            :delivery="order.delivery"
            :express="express"
            @logistics="logistics"
        ></deliveryinformation>
        <div class="orderadd">
            <img class="orderadd-gps" src="../../../static/image/gps.png"/>
            <div class="orderadd-content" v-if="order.store">
                <div class="orderadd-top">
                    <span>提货人：{{ order.ship_name }}</span>
                    <p>{{ order.ship_mobile }}</p>
                </div>
                <div class="orderadd-bottom">
                    <p>门店地址：{{ order.ship_area_name }} {{ order.ship_address }}</p>
                </div>
            </div>
            <div class="orderadd-content" v-else>
                <div class="orderadd-top">
                    <span>收货人：{{ order.ship_name }}</span>
                    <p>{{ order.ship_mobile }}</p>
                </div>
                <div class="orderadd-bottom">
                    <p>收货地址：{{ order.ship_area_name }} {{ order.ship_address }}</p>
                </div>
            </div>
        </div>
        <div class="orderlist">
            <yd-list theme="4">
                <yd-list-item v-for="(item, index) in order.items" :key="index" @click.native="goodsDetail(item.goods_id)">
                    <img slot="img" :src="item.image_url">
                    <h3 class="goodsname" slot="title">{{ item.name }}</h3>
                    <p class="goods" slot="title">{{ item.addon }}</p>
                    <yd-list-other slot="other">
                        <div>
                            <span class="demo-list-price"><em>¥</em>{{ item.price }}</span>
                        </div>
                        <div>x{{ item.nums }}</div>
                    </yd-list-other>
                    <yd-list-other slot="other">
                        <div v-if="item.promotion_list">
                            <div v-for="(val, key) in toObj(item.promotion_list)" :key="key">
                                <yd-badge shape="square" type="danger">{{ val }}</yd-badge>
                            </div>
                        </div>
                    </yd-list-other>
                </yd-list-item>
            </yd-list>
        </div>
        <ordercell
            :goods_amount="order.goods_amount"
            :cost_freight="order.cost_freight"
            :amount="order.order_amount"
            :goods_pmt="order.goods_pmt"
            :order_pmt="order.order_pmt"
            :coupon_pmt="order.coupon_pmt"
            :point_pmt="order.point_money"
        ></ordercell>
        <orderdetailfooter
            :status="order.text_status"
            @cancel="cancel"
            @confirm="confirm"
            @afterSales="afterSales"
            @pay="pay"
            @evaluate="evaluate"
            @logistics="logistics"
        ></orderdetailfooter>
        <yd-popup v-model="showLogistics" position="center" width="80%" height="80%">
            <div class="express-info">
                <div class="express-num"></div>
                <yd-timeline>
                    <yd-timeline-item v-for="(item, index) in logisticsInfo.data" :key="index">
                        <p>{{ item.context }}</p>
                        <p style="margin-top: 10px;">{{ item.time }}</p>
                    </yd-timeline-item>
                </yd-timeline>
            </div>
        </yd-popup>
    </div>
</template>

<script>
import orderdetailheader from '../../components/OrderDetailHeader.vue'
import deliveryinformation from '../../components/DeliveryInformation.vue'
import ordercell from '../../components/OrderCell.vue'
import orderdetailfooter from '../../components/OrderDetailFooter.vue'

export default {
    components: {
        orderdetailheader, deliveryinformation, ordercell, orderdetailfooter
    },
    data () {
        return {
            order_id: this.$route.query.order_id, // 传过来的order_id
            order: [], // 订单详情
            express: {}, // 订单详情物流信息显示最后一条信息
            showLogistics: false, // 是否显示物流信息窗口
            logisticsInfo: [] // 物流信息
        }
    },
    created () {
        if (!this.$route.query.order_id) {
            this.$dialog.alert({
                mes: '订单参数丢失',
                callback: () => {
                    this.$router.go(-1)
                }
            })
        }
        this.orderDetail()
    },
    methods: {
        // json str to obj
        toObj (str) {
            let obj = {}
            obj = JSON.parse(str)
            return obj
        },
        // 查看订单详情
        orderDetail () {
            this.$api.orderDetail({order_id: this.order_id}, res => {
                if (res.status) {
                    this.order = res.data
                    if (this.order.hasOwnProperty('express_delivery')) {
                        this.express = this.order.express_delivery
                    }
                } else {
                    this.$dialog.alert({
                        mes: '未查询到该订单信息',
                        callback: () => {
                            this.$router.go(-1)
                        }
                    })
                }
            })
        },
        // 查看商品详情
        goodsDetail (id) {
            this.$router.push({path: '/goodsDetail', query: {goods_id: id}})
        },
        // 取消订单
        cancel () {
            this.$dialog.confirm({
                mes: '确定取消该订单?',
                opts: [
                    {
                        txt: '确定',
                        color: true,
                        callback: () => {
                            this.$api.cancelOrder({
                                order_ids: this.order_id
                            }, res => {
                                if (res.status) {
                                    this.$dialog.toast({mes: res.msg, icon: 'success', timeout: 1000})
                                    this.orderDetail()
                                }
                            })
                        }
                    },
                    {
                        txt: '取消',
                        color: false
                    }
                ]
            })
        },
        // 确认收货
        confirm () {
            this.$dialog.confirm({
                mes: '确认执行此操作吗?',
                opts: [
                    {
                        txt: '确定',
                        color: true,
                        callback: () => {
                            this.$api.confirmOrder({
                                order_id: this.order_id
                            }, res => {
                                if (res.status) {
                                    this.$dialog.toast({mes: res.msg, icon: 'success', timeout: 1000})
                                    this.orderDetail()
                                }
                            })
                        }
                    },
                    {
                        txt: '取消',
                        color: false
                    }
                ]
            })
        },
        pay () {
            this.$router.push({path: '/cashierdesk', query: {order_id: this.order_id}})
        },
        // 申请售后
        afterSales () {
            this.$router.push({path: '/afterservice', query: {order_id: this.order_id}})
        },
        // 评价
        evaluate () {
            this.$router.push({path: '/evaluate', query: {order_id: this.order_id}})
        },
        // 查看物流信息详情
        logistics () {
            this.$api.logistics({code: this.order.delivery[0].logi_code, no: this.order.delivery[0].logi_no}, res => {
                if (res.status) {
                    this.showLogistics = true
                    this.logisticsInfo = res.data.info
                    console.log(this.logisticsInfo)
                }
            })
        }
    }
}
</script>

<style>
    .express-info{
        background-color: #fff;
        position: relative;
    }
    .express-num{
        position: fixed;
        top: -1px;
        background-color: #e5e5e5;
        width: 100%;
        height: .8rem;
        line-height: .8rem;
        z-index: 10086;
    }
    .express-info .yd-timeline{
        margin-top: calc(.5rem - 1px);
        font-size: .1rem;
    }
</style>
