<template>
    <div>
        <div class="orderadd" v-if="Object.keys(ship).length">
            <img class="orderadd-gps" src="../../static/image/gps.png"/>
            <div class="orderadd-content" @click="showHandler">
                <div class="orderadd-top">
                    <span>收货人：{{ ship.name }}</span>
                    <p>{{ ship.mobile }}</p>
                </div>
                <div class="orderadd-bottom">
                    <p>收货地址：{{ ship.area_name }}{{ ship.address }}</p>
                    <img class="orderadd-right right-img" src="../../static/image/right.png"/>
                </div>
            </div>
        </div>
        <div class="orderadd" v-else>
            <yd-button size="small" type="danger" style="margin-top: 20px" @click.native="newShipAdd">新增收货地址</yd-button>
        </div>
        <yd-popup v-model="openWindow" position="bottom" width="20%" height="60%">
            <div class="orderadd-content">
                <div v-if="shipList.length">
                    <div style="margin-top: 20px; text-align: left;" v-for="(item, index) in shipList" :key="index" @click="shipHandler(index)">
                        <div class="orderadd-top">
                            <yd-badge type="primary" v-if="item.is_def === 1">默认</yd-badge>
                            <span>收货人：{{ item.name }}</span>
                            <p>{{ item.mobile }}</p>
                        </div>
                        <div class="orderadd-bottom">
                            <p>收货地址：{{ item.area_name }}{{ item.address }}</p>
                            <img class="orderadd-right right-img" src="../../static/image/right.png" @click.native="showAdd"/>
                        </div>
                    </div>
                </div>
                <yd-button size="small" type="danger" style="margin-top: 20px" @click.native="newShipAdd">新增收货地址</yd-button>
            </div>
        </yd-popup>
    </div>
</template>

<script>
export default {
    data () {
        return {
            openWindow: false,
            shipList: []
        }
    },
    props: {
        // 用户选中||默认收货地址
        ship: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    },
    methods: {
        showHandler () {
            this.openWindow = true
            this.$api.userShip({}, res => {
                this.shipList = res.data
            })
        },
        shipHandler (index) {
            this.openWindow = false
            this.$emit('shipHandler', this.shipList[index])
        },
        newShipAdd () {
            this.$router.push({path: '/address'})
        }
    }
}
</script>

<style>
    .orderadd>button{
        margin-top: 0;
        font-size: .26rem;
    }
</style>
