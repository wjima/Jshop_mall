<template>
    <div class="address">
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">姓名：</span>
                <yd-input slot="right" v-model="name" max="20" placeholder="请输入姓名"></yd-input>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">手机号：</span>
                <yd-input slot="right" v-model="mobile" type="tel" ref="mobile" regex="mobile" placeholder="请输入手机号码"></yd-input>
            </yd-cell-item>
            <yd-cell-item arrow>
                <span slot="left">所在地区：</span>
                <input slot="right" type="text" @click.stop="show = true" v-model="area" readonly placeholder="请选择收货地址">
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">详细地址：</span>
                <yd-input slot="right" v-model="address" max="20" placeholder="请输入详细地址"></yd-input>
            </yd-cell-item>
            <yd-cell-item type="label" v-if="!shipId">
                <div slot="left">设置为默认地址：</div>
                <yd-switch slot="right" v-model="def"></yd-switch>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cityselect v-model="show" :callback="result1" :items="district"></yd-cityselect>
        <yd-button size="large" type="danger" @click.native="submit">保存</yd-button>
    </div>
</template>

<script>
import District from 'ydui-district/dist/gov_province_city_area_id'
export default {
    data () {
        return {
            shipId: this.$route.query.ship_id || 0,
            name: '', // 名称
            mobile: '', // 手机
            show: false,
            areaId: '', // 地区id
            area: '', // 地区信息
            address: '', // 详细地址
            is_def: '',
            def: true, // 是否为默认
            district: District
        }
    },
    created () {
        if (this.shipId) {
            this.$api.shipDetail({id: this.shipId}, res => {
                if (res.status) {
                    let data = res.data
                    this.name = data.name
                    this.mobile = data.mobile
                    this.areaId = data.area_id
                    this.area = data.area_name
                    this.address = data.address
                    this.is_def = data.is_def
                }
            })
        }
    },
    methods: {
        result1 (ret) {
            this.areaId = ret.itemValue3
            this.area = ret.itemName1 + ' ' + ret.itemName2 + ' ' + ret.itemName3
        },
        submit () {
            if (!this.name) {
                this.$dialog.toast({mes: '请输入名称', timeout: 1000})
            } else if (!this.mobile) {
                this.$dialog.toast({mes: '请输入手机号', timeout: 1000})
            } else if (!this.area) {
                this.$dialog.toast({mes: '请选择地区', timeout: 1000})
            } else if (!this.$refs.mobile.valid) {
                this.$dialog.toast({mes: '手机号格式不正确', timeout: 1000})
            } else if (!this.address) {
                this.$dialog.toast({mes: '请输入详细地址信息', timeout: 1000})
            } else if (this.shipId) {
                // 修改
                this.$api.editShip({
                    name: this.name,
                    area_id: this.areaId,
                    address: this.address,
                    mobile: this.mobile,
                    is_def: this.is_def,
                    id: this.shipId
                }, res => {
                    if (res.status) {
                        this.$dialog.toast({
                            mes: res.msg,
                            timeout: 1000,
                            icon: 'success',
                            callback: () => {
                                this.$router.go(-1)
                            }
                        })
                    }
                })
            } else {
                // 添加
                this.$api.saveUserShip({
                    name: this.name,
                    mobile: this.mobile,
                    area_id: this.areaId,
                    address: this.address,
                    is_def: this.def ? 1 : 2
                }, res => {
                    if (res.status) {
                        this.$dialog.toast({
                            mes: res.msg,
                            timeout: 1000,
                            icon: 'success',
                            callback: () => {
                                this.$router.go(-1)
                            }
                        })
                    }
                })
            }
        }
    }
}
</script>
