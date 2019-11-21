<template>
	<view class="content">
		<view class='withdrawcash-top'>
			<text class='withdrawcash-title'>账户余额（元）</text>
			<text class='withdrawcash-num'>{{ userInfo.balance }}</text>
		</view>
		<view class='cell-group margin-cell-group right-img'>
			<view class='cell-item'>
				<view class='cell-item-hd' @click="navigateToHandle('./recharge')">
					<image class='cell-hd-icon' src='/static/image/topup.png'></image>
					<view class='cell-hd-title'>账户充值</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd' @click="navigateToHandle('./withdraw_cash')">
					<image class='cell-hd-icon' src='/static/image/withdraw.png'></image>
					<view class='cell-hd-title'>余额提现</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd' @click="navigateToHandle('./details')">
					<image class='cell-hd-icon' src='/static/image/detail.png'></image>
					<view class='cell-hd-title'>余额明细</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd' @click="navigateToHandle('./cashlist')">
					<image class='cell-hd-icon' src='/static/image/record.png'></image>
					<view class='cell-hd-title'>提现记录</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd' @click="navigateToHandle('./bankcard')">
					<image class='cell-hd-icon' src='/static/image/card.png'></image>
					<view class='cell-hd-title'>我的银行卡</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data () {
		return {
			userInfo: {}
		}
	},
	onShow () {
		this.getUserInfo()
	},
	methods: {
		// 获取用户信息
		getUserInfo () {
			this.$api.userInfo({}, res => {
				if (res.status) {
					this.userInfo = res.data
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		},
		// 页面跳转
		navigateToHandle (pageUrl) {
			this.$common.navigateTo(pageUrl)
		}
	}
}	
</script>

<style>
.withdrawcash-top{
  padding: 40upx 26upx;
  background-color: #FF7159;
  color: #fff;
}
.withdrawcash-title{
  font-size: 28upx;
  display: block
}
.withdrawcash-num{
  font-size: 70upx;
  display: block;
  margin-top: 20upx;
  margin-left: 50upx;
}
.margin-cell-group {
    margin: 20upx 0;
    color: #666666;
}
</style>
