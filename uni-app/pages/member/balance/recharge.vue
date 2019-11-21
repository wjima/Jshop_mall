<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group margin-cell-group'>
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>当前金额</view>
					</view>
					<view class='cell-item-bd'>
						<text class="cell-bd-view">￥{{ user.balance || ''}}</text>
					</view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>充值金额</view>
					</view>
					<view class='cell-item-bd'>
						<input class='cell-bd-input' placeholder='请输入要充值的金额' v-model="money" focus type="digit"></input>
					</view>
				</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-b" hover-class="btn-hover2" @click="navigateToHandle">去支付</button>
		</view>
	</view>
</template>

<script>
export default {
	data () {
		return {
			user: {}, // 用户信息
			payments: [], // 可用充值方式列表
			money: '', // 充值的金额
			orderType: 2	// 充值类型
		}
	},
	onLoad () {
		this.userInfo()
	},
	methods: {
		// 获取用户信息
		userInfo () {
			this.$api.userInfo({}, res => {
				if (res.status) {
					this.user = res.data
				}
			})
		},
		// 去充值
		navigateToHandle () {
			if (!Number(this.money)) {
				this.$common.errorToShow('请输入要充值的金额')
			} else {
				this.$common.navigateTo('/pages/goods/payment/index?recharge=' + Number(this.money) + '&type=' + this.orderType)
			}
		}
	}
}
</script>

<style>
.user-head{
	height: 100upx;
}
.user-head-img{
	height: 90upx;
	width: 90upx;
	border-radius: 50%;
}
.cell-hd-title{
	color: #333;
}
.cell-item-bd{
	color: #666;
	font-size: 26upx;
}
.button-bottom .btn {
	width: 100%;
}
</style>
