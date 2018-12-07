<template>
    <div class="ordertab">
        <div class="order-content" slot="list" v-for="(item, index) in orderList" :key="index">
            <div class="order-content-header">
                <p class="header-left">提货单号：{{ item.id }}</p>
                <p class="header-right" v-if="item.status === 1">{{ item.status_name }}</p>
                <p class="header-right" style="color: #ccc;" v-else-if="item.status === 2">{{ item.status_name }}</p>
            </div>
            <div class="order-content-header">
                <p class="header-left">订单号：{{ item.order_id }}</p>
                <p class="header-right" v-if="item.order_info.status === 1 && item.order_info.pay_status === 2 && item.order_info.ship_status === 1">待发货</p>
                <p class="header-right" v-if="item.order_info.status === 1 && item.order_info.pay_status === 2 && item.order_info.ship_status === 3 && item.order_info.confirm === 1">待收货</p>
                <p class="header-right" style="color: #e6a200" v-if="item.order_info.status === 1 && item.order_info.pay_status === 2 && item.order_info.ship_status === 3 && item.order_info.confirm === 2 && item.order_info.is_comment === 1">待评价</p>
                <p class="header-right" style="color: #0575f2" v-if="item.order_info.status === 1 && item.order_info.pay_status === 2 && item.order_info.ship_status === 3 && item.order_info.confirm === 2 && item.order_info.is_comment === 2">已评价</p>
                <p class="header-right" style="color: #379B2D;" v-if="item.order_info.status === 2">已完成</p>
                <p class="header-right" style="color: #ccc;" v-if="item.order_info.status === 3">已取消</p>
            </div>
            <yd-list theme="4" @click.native="toVerification(item.id)">
                <yd-list-item v-for="(val, key) in item.order_items" :key="key">
                    <img slot="img" :src="val.image_url">
                    <h3 class="goodsname" slot="title">{{ val.name }}</h3>
                    <p class="goods" slot="title">{{ val.addon }}</p>
                    <yd-list-other slot="other">
                        <div>
                            <span class="demo-list-price"><em>¥</em>{{ val.price }}</span>
                        </div>
                        <div>x {{ val.nums }}</div>
                    </yd-list-other>
                </yd-list-item>
            </yd-list>
            <div class="order-content-footer">
                <div class="footer-top">
                    <p class="goods" slot="title">创建时间：{{ item.ctime }}</p>
                    <p class="footer-top-left">共计 {{ item.order_items.length }} 件商品</p>
                </div>
                <div class="footer-bottom">
                    <yd-button type="hollow" shape="circle" class="right-btn" v-if="item.status === 1" @click.native="toVerification(item.id)">提货核销</yd-button>
                    <yd-button type="hollow" shape="circle" class="left-btn" v-if="item.status === 2" @click.native="orderDel(index)">删除</yd-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "StoreOrder",
    data () {
        return {
            orderList: []
        }
    },
    mounted () {
        this.getStoreOrderList()
    },
    methods: {
        getStoreOrderList () {
            this.$api.storeLadingList({}, res => {
                if (res.status) {
                    this.orderList = res.data
                }
            })
        },
        // 提货单核销
        toVerification (id) {
            this.$router.push({path: '/orderverification', query: {id: id}})
        },
        // 提货单删除
        orderDel (index) {
            this.$dialog.confirm({
                title: '提示',
                mes: '确认删除该提货单吗?',
                opts: () => {
                    this.$api.ladingDel({lading_id: this.orderList[index].id}, res => {
                        if (res.status) {
                            this.$dialog.toast({
                                mes: res.msg,
                                timeout: 1300,
                                callback: () => {
                                    this.orderList.splice(index, 1)
                                }
                            })
                        }
                    })
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
