<template>
    <div>
        <yd-cell-group>
            <yd-cell-item v-if="showType">
                <span slot="left">卡类型：</span>
                <yd-input slot="right" v-model="fullName" placeholder="银行卡类型" readonly disabled></yd-input>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">银行卡号：</span>
                <yd-input slot="right" v-model="cardCode" ref="code" regex="bankcard" placeholder="请输入银行卡号"></yd-input>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">持卡人：</span>
                <yd-input slot="right" v-model="name" :on-focus="checkCard" placeholder="请输入持卡人姓名"></yd-input>
            </yd-cell-item>
            <yd-cell-item arrow>
                <span slot="left">开户地区：</span>
                <input slot="right" type="text" @click.stop="show=true" v-model="area" readonly placeholder="请选择开户地区">
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">开户行：</span>
                <yd-input slot="right" v-model="accountBank" placeholder="请输入开户银行"></yd-input>
            </yd-cell-item>
            <yd-cell-item type="label">
                <div slot="left">设为默认：</div>
                <yd-switch slot="right" v-model="isDefault"></yd-switch>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cityselect v-model="show" :callback="result" :items="district"></yd-cityselect>
        <yd-button size="large" type="danger" @click.native="submitHandler" style="height: .8rem;width: 80%;margin: 0 auto;">保存</yd-button>
    </div>
</template>

<script>
import District from 'ydui-district/dist/gov_province_city_area_id'
export default {
    data () {
        return {
            show: false,
            area: '',
            name: '', // 持卡人姓名
            cardCode: '', // 银行卡号
            showType: false, // 类型显示
            bankName: '', // 银行名称
            cardType: '', // 卡类型
            cardTypeName: '', // 卡类型名称
            bankCode: '', // 银行缩写
            areaId: '', // 银行卡开户地区id,
            accountBank: '', // 开户银行
            isDefault: true, // 默认开关
            district: District
        }
    },
    mounted () {
        // 页面加载完成自动设置input 银行卡号 为焦点
        this.$refs.code.setFocus()
    },
    computed: {
        // 计算银行名称 卡片类型
        fullName () {
            return this.bankName + this.cardTypeName
        }
    },
    methods: {
        // 获取选中的地区信息/id
        result (ret) {
            this.areaId = ret.itemValue3
            this.area = ret.itemName1 + ' ' + ret.itemName2 + ' ' + ret.itemName3
        },
        // 提交
        submitHandler () {
            if (!this.cardCode) {
                this.$dialog.toast({mes: '请输入银行卡号', timeout: 1000})
            } else if (!this.bankName && !this.cardType && !this.bankCode) {
                this.$dialog.toast({mes: '请输入正确的银行卡号', timeout: 1000})
            } else if (!/^[\u4E00-\u9FA5]{2,4}$/.test(this.name)) {
                this.$dialog.toast({mes: '请输入正确的持卡人名称', timeout: 1000})
            } else if (!this.areaId) {
                this.$dialog.toast({mes: '请选择开户行地区信息', timeout: 1000})
            } else if (!this.accountBank) {
                this.$dialog.toast({mes: '请选择开户银行', timeout: 1000})
            } else {
                this.$api.addBankCard({
                    bankName: this.bankName,
                    areaId: this.areaId,
                    accountBank: this.accountBank,
                    accountName: this.name,
                    bankCode: this.bankCode,
                    cardNumber: this.cardCode,
                    cardType: this.cardType,
                    isDefault: this.isDefault ? 1 : 2
                }, res => {
                    if (res.status) {
                        this.$dialog.toast({
                            mes: res.msg,
                            timeout: 1000,
                            callback: () => {
                                this.$router.go(-1)
                            }
                        })
                    }
                })
            }
        },
        // 暂通过持卡人input进入焦点事件请求api获取银行信息
        checkCard () {
            this.$api.getBankCardOrganization({card_code: this.cardCode}, res => {
                if (res.status) {
                    const _data = res.data
                    this.bankName = _data.name
                    this.cardType = _data.type
                    this.bankCode = _data.bank_code
                    this.cardTypeName = _data.type_name
                    this.showType = true
                } else {
                    this.bankName = this.cardType = this.bankCode = this.cardTypeName = ''
                    this.showType = false
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
