<template>
    <div class="user">
        <userheader
            :avatar="user.avatar"
            :name="user.nickname"
            :balance="user.balance"
            :point="user.point"
            :isOpenIntegral="isOpenIntegral"
            @upload="uploadAvatar"
        ></userheader>
        <yd-cell-group>
            <yd-cell-item arrow>
                <img slot="icon" src="../../static/image/orderform.png">
                <span slot="left">我的订单</span>
                <router-link slot="right" tag="span" to="/allorder">查看全部订单</router-link>
            </yd-cell-item>
            <yd-grids-group :rows="4">
                <yd-grids-item :link="{path: '/allorder', query: {tab: 1}}">
                    <yd-badge slot="else" bgcolor="#fc0000" v-if="orderNum[1]">{{ orderNum[1] }}</yd-badge>
                    <img slot="icon" src="../../static/image/unpaid.png">
                    <span slot="text">待付款</span>
                </yd-grids-item>
                <yd-grids-item :link="{path: '/allorder', query: {tab: 2}}">
                    <yd-badge slot="else" bgcolor="#fc0000" v-if="orderNum[2]">{{ orderNum[2] }}</yd-badge>
                    <img slot="icon" src="../../static/image/pendingdelivery.png">
                    <span slot="text">待发货</span>
                </yd-grids-item>
                <yd-grids-item :link="{path: '/allorder', query: {tab: 3}}">
                    <yd-badge slot="else" bgcolor="#fc0000" v-if="orderNum[3]">{{ orderNum[3] }}</yd-badge>
                    <img slot="icon" src="../../static/image/shipped.png">
                    <span slot="text">待收货</span>
                </yd-grids-item>
                <yd-grids-item link="/allafterservice">
                    <img slot="icon" src="../../static/image/returns.png">
                    <span slot="text">退换货</span>
                </yd-grids-item>
            </yd-grids-group>
        </yd-cell-group>
        <yd-cell-group>
            <yd-cell-item v-if="isOpenIntegral" type="div" @click.native="signIn">
                <img slot="icon" src="../../static/image/signin.png">
                <span slot="left">积分签到</span>
            </yd-cell-item>
            <yd-cell-item href="/coupon" type="link">
                <img slot="icon" src="../../static/image/coupon.png">
                <span slot="left">我的优惠券</span>
            </yd-cell-item>
            <yd-cell-item href="/cart" type="link">
                <img slot="icon" src="../../static/image/member-cart.png">
                <span slot="left">我的购物车</span>
            </yd-cell-item>
            <yd-cell-item href="/balance" type="link">
                <img slot="icon" src="../../static/image/remainingsum.png">
                <span slot="left">我的余额</span>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cell-group>
            <yd-cell-item href="/collect" type="link">
                <img slot="icon" src="../../static/image/heart.png">
                <span slot="left">我的关注</span>
            </yd-cell-item>
            <yd-cell-item href="/history" type="link">
                <img slot="icon" src="../../static/image/footer.png">
                <span slot="left">我的足迹</span>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cell-group>
            <yd-cell-item href="/share" type="link">
                <img slot="icon" src="../../static/image/me-ic-invite.png">
                <span slot="left">邀请好友</span>
            </yd-cell-item>
            <yd-cell-item href="/addresslist" type="link">
                <img slot="icon" src="../../static/image/me-ic-site.png">
                <span slot="left">收货地址</span>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cell-group v-if="isClerk">
            <yd-cell-item href="/storeorder" type="link">
                <img slot="icon" src="../../static/image/coupon.png">
                <span slot="left">提货单列表</span>
            </yd-cell-item>
            <yd-cell-item href="/orderverification" type="link">
                <img slot="icon" src="../../static/image/ship.png">
                <span slot="left">提货单核销</span>
            </yd-cell-item>
        </yd-cell-group>
    </div>
</template>

<script>
import userheader from '../components/UserHeader.vue'
export default {
    data () {
        return {
            user: [],
            orderNum: [],
            isOpenIntegral: false, // 用户是否开启积分
            isClerk: false // 是否是店员并且开启店铺自提
        }
    },
    components: {
        userheader
    },
    mounted () {
        this.getUserInfo()
        this.getOrderSum()
        this.isOpenPoint()
    },
    methods: {
        // 获取用户昵称头像等信息
        getUserInfo () {
            this.$api.userInfo({}, res => {
                if (res.status) {
                    this.user = res.data
                    this.storeUser(res.data)
                }
            })
        },
        // 获取订单不同状态的数量
        getOrderSum () {
            this.$api.getOrderStatusSum({}, res => {
                this.orderNum = res.data
            })
        },
        // 判断是否开启积分
        isOpenPoint () {
            this.$api.isPoint({}, res => {
                if (res.status) {
                    res.data === 1 ? this.isOpenIntegral = true : this.isOpenIntegral = false
                }
            })
        },
        // 判断是否是店员
        storeUser (userId) {
            this.$api.isStoreUser({user_id: userId}, res => {
                if (res.status) {
                    this.isClerk = res.flag
                }
            })
        },
        // 用户头像上传
        uploadAvatar (e) {
            let file = e.target.files[0]
            let data = new FormData()
            data.append('upfile', file, file.name)
            this.$api.uploadFile(data, res => {
                if (res.status) {
                    let avatar = res.data.url // 上传成功的图片地址
                    // 执行头像修改
                    this.$api.changeAvatar({
                        avatar: avatar
                    }, res => {
                        if (res.status) {
                            this.user.avatar = res.data.avatar
                            this.$dialog.toast({
                                mes: res.msg,
                                timeout: 1300
                            })
                        }
                    })
                }
            })
        },
        // 用户签到
        signIn () {
            this.$api.sign({}, res => {
                if (res.status) {
                    this.$dialog.toast({
                        mes: '签到成功!',
                        timeout: 1300,
                        callback: () => {
                            this.getUserInfo()
                        }
                    })
                }
            })
        }
    }
}
</script>

<style>
    .user .yd-btn-block{
        height: .8rem;
    }
	.user .yd-cell-item:not(:last-child):after{
		border-bottom: 1px solid #e9e9e9;
	}
</style>
