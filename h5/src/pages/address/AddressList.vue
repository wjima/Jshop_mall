<template>
    <div class="orderadd-content addresslist" style="margin: auto;width: 100%;">
        <div v-if="list.length">
            <div class="orderadd" v-for="(item, index) in list" :key="index">
                <div class="orderadd-header">
                    <yd-badge type="danger" v-if="item.is_def === 1">默认地址</yd-badge>
                    <yd-badge type="primary" v-else @click.native="setDef(item.id)">设为默认</yd-badge>
                    <yd-button size="small" type="hollow" @click.native='remove(item.id)'>删除</yd-button>
                </div>
                <div class="orderadd-content"  @click="showDetail(item.id)">
                    <div class="orderadd-top">
                        <span>收货人：{{ item.name }}</span>
                        <p>{{ item.mobile }}</p>
                    </div>
                    <div class="orderadd-bottom">
                        <p>收货地址：{{ item.area_name }}{{ item.address }}</p>
                        <img class="orderadd-right right-img" src="../../../static/image/right.png"/>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            暂无收货地址
        </div>
        <yd-button size="large" @click.native="add" type="danger">新增收货地址</yd-button>
    </div>
</template>

<script type="text/babel">
export default {
    data () {
        return {
            list: []
        }
    },
    created () {
        this.shipList()
    },
    methods: {
        shipList () {
            this.$api.userShip({}, res => {
                if (res.data.length) {
                    this.list = res.data
                }
            })
        },
        // 添加新地址
        add () {
            this.$router.push({path: '/address'})
        },
        // 查看地址详情
        showDetail (id) {
            this.$router.push({path: '/address', query: {ship_id: id}})
        },
        // 设置默认地址
        setDef (id) {
            this.$api.setDefShip({id: id}, res => {
                if (res.status) {
                    this.shipList()
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
                }
            })
        },
        // 收货地址删除
        remove (id) {
            this.$dialog.confirm({
                mes: '确认删除该收货地址?',
                opts: [
                    {
                        txt: '取消',
                        color: false
                    },
                    {
                        txt: '确定',
                        color: true,
                        callback: () => {
                            this.$api.removeShip({id: id}, res => {
                                if (res.status) {
                                    this.$dialog.toast({mes: res.msg, icon: 'success', timeout: 1000})
                                    this.shipList()
                                }
                            })
                        }
                    }
                ]
            })
        }
    }
}
</script>
