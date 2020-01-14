<template>
	<view class="content">
		<view class='withdrawcash-top'>
			<text class='withdrawcash-title'>账户余额（元）</text>
			<text class='withdrawcash-num'>{{ userInfo.balance }}</text>
		</view>
		<view class='cell-group margin-cell-group right-img'>
			<!-- #ifndef MP-WEIXIN -->
			<view class='cell-item' v-if="platform != 'ios'" @click="navigateToHandle('./recharge')">
				<view class='cell-item-hd'>
					<image class='cell-hd-icon' src='/static/image/topup.png'></image>
					<view class='cell-hd-title'>账户充值</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<!-- #endif -->
			<view class='cell-item' @click="navigateToHandle('./withdraw_cash')">
				<view class='cell-item-hd'>
					<image class='cell-hd-icon' src='/static/image/withdraw.png'></image>
					<view class='cell-hd-title'>余额提现</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item' @click="navigateToHandle('./details')">
				<view class='cell-item-hd'>
					<image class='cell-hd-icon' src='/static/image/detail.png'></image>
					<view class='cell-hd-title'>余额明细</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item' @click="navigateToHandle('./cashlist')">
				<view class='cell-item-hd'>
					<image class='cell-hd-icon' src='/static/image/record.png'></image>
					<view class='cell-hd-title'>提现记录</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item' @click="navigateToHandle('./bankcard')">
				<view class='cell-item-hd'>
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
			userInfo: {},
			platform: 'ios'
		}
	},
	onShow () {
		this.getUserInfo();
	},
	methods: {
		// 获取用户信息
		getUserInfo () {
			let _this = this;
			uni.getSystemInfo({
				success: function (res) {
					_this.platform = res.platform;
				}
			});
			
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
