<template>
    <div>
        <yd-tab v-model="storeTab" :prevent-default="false" :item-click="storeClick">
            <yd-tab-panel label="快递配送">
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
            </yd-tab-panel>
            <yd-tab-panel label="门店自提">
                <div class="orderadd" v-if="Object.keys(store).length">
                    <img class="orderadd-gps" src="../../static/image/gps.png"/>
                    <div class="orderadd-content" @click="showStoreList">
                        <div class="orderadd-top">
                            <span>{{ store.store_name }}</span>
                            <!--<p>{{ store.mobile }}</p>-->
                        </div>
                        <div class="orderadd-bottom">
                            <p>门店地址：{{ store.all_address }}</p>
                            <img class="orderadd-right right-img" src="../../static/image/right.png"/>
                        </div>
                    </div>
                    <div style="margin-top: .5em" v-if="Object.keys(store).length">
                        <yd-cell-group>
                            <yd-cell-item>
                                <span slot="left">姓名：</span>
                                <yd-input slot="right" type="text" v-model="consignee.name" max="20" placeholder="提货人姓名"></yd-input>
                            </yd-cell-item>
                            <yd-cell-item>
                                <span slot="left">电话：</span>
                                <yd-input slot="right" type="text" v-model="consignee.mobile" placeholder="提货人联系方式"></yd-input>
                            </yd-cell-item>
                        </yd-cell-group>
                    </div>
                </div>
                <div v-else>
                    <span>暂无门店信息</span>
                </div>
            </yd-tab-panel>
        </yd-tab>
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
        <yd-popup v-model="openStore" position="bottom" width="20%" height="60%">
            <div class="orderadd-content">
                <div class="search-input">
                    <i class="search-icon" ></i>
                    <input v-model="key" type="text" placeholder="搜索门店" @keyup.enter="submitHandler"/>
                </div>
                <div v-if="storeList.length">
                    <div style="margin-top: 20px; text-align: left;" v-for="(item, index) in storeList" :key="index" @click="storeHandler(index)">
                        <div class="orderadd-top">
                            <span>{{ item.store_name }}</span>
                            <!--<p>{{ item.mobile }}</p>-->
                        </div>
                        <div class="orderadd-bottom">
                            <p>门店地址：{{ item.all_address }}</p>
                            <img class="orderadd-right right-img" src="../../static/image/right.png"/>
                        </div>
                    </div>
                </div>
            </div>
        </yd-popup>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                key: '', // 搜索关键字
                openWindow: false,
                openStore: false,
                consignee: {
                    name: '', // 提货人姓名
                    mobile: '', // 提货人联系方式
                },
                shipList: [], // 收货地址列表
                storeList: [] // 门店列表
            }
        },
        props: {
            storeTab: {
                type: Number,
                default () {
                    return 0
                }
            },
            // 用户选中||默认收货地址
            ship: {
                type: [Array, Object],
                default () {
                    return []
                }
            },
            // 默认门店信息
            store: {
                type: [Array, Object],
                default () {
                    return []
                }
            }
        },
        methods: {
            // 收货地址弹窗
            showHandler () {
                this.openWindow = true
                this.$api.userShip({}, res => {
                    this.shipList = res.data
                })
            },
            // 选中的收货地址
            shipHandler (index) {
                this.openWindow = false
                this.$emit('shipHandler', this.shipList[index])
            },
            // 选中的门店
            storeHandler (index) {
                this.openStore = false
                this.$emit('storeHandler', this.storeList[index])
            },
            // 添加收货地址
            newShipAdd () {
                this.$router.push({path: '/address'})
            },
            // tab点击切换
            storeClick (key) {
                this.$emit('storeTab', key)
            },
            // 查看门店列表
            showStoreList () {
                this.openStore = true
                this.getStoreList()
            },
            // 获取门店列表
            getStoreList (key = '') {
                let data = {key: key}
                this.$api.storeList(data, res => {
                    if (res.status) {
                        this.storeList = res.data
                    }
                })
            },
            // 门店搜索
            submitHandler () {
                this.getStoreList(this.key)
            }
        },
        watch: {
            consignee: {
                handler () {
                    this.$emit('consignee', this.consignee)
                },
                deep: true
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
