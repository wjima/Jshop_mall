<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group right-img'>
				<view class='cell-item' @click="navigateToHandle('./user_info/index')">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>个人信息</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<view class='cell-item' @click="navigateToHandle('./user_info/resetpassword')">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>重置密码</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<view class='cell-item' @click="navigateToHandle('./user_info/password')">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>修改密码</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<view class='cell-item' @click="clearCache">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>清除缓存</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<view class='cell-item' @click="aboutUs">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>关于我们</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<view class='cell-item' @click="logOff">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>退出</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
			</view>
		</view>
		<view class="color-9 fsz24 agreement">
			<text @click="goAgreement()" class="color-o">《用户协议》</text> 和 <text @click="goPrivacy()" class="color-o">《隐私政策》</text>
		</view>
	</view>
</template>

<script>
export default {
  methods: {
    navigateToHandle(pageUrl) {
      this.$common.navigateTo(pageUrl)
    },
    // 清除缓存
    clearCache() {
      // 重新获取统一配置信息
      this.$api.shopConfig(res => {
        this.$store.commit('config', res)
      })
      // 删除地区缓存信息
      this.$db.del('areaList')
      setTimeout(() => {
        this.$common.successToShow('清除成功')
      }, 500)
    },
    // 关于我们
    aboutUs() {
	  let articleId = this.$store.state.config.about_article_id;
      this.$common.navigateTo('/pages/article/index?id_type=1&id=' + articleId);
    },
    // 退出登录
    logOff() {
      this.$common.modelShow('退出', '确认退出登录吗?', () => {
        this.$db.del('userToken')
        uni.reLaunch({
          url: '/pages/index/index'
        })
      })
    },
	// 跳转到用户协议
	goAgreement () {
		let articleId = this.$store.state.config.user_agreement_id;
		this.$common.navigateTo('/pages/article/index?id_type=1&id=' + articleId);
	},
	// 跳转到隐私政策
	goPrivacy () {
		let articleId = this.$store.state.config.privacy_policy_id;
		this.$common.navigateTo('/pages/article/index?id_type=1&id=' + articleId);
	}
  }
}
</script>

<style>
.agreement{
	position: fixed;
	bottom: 30rpx;
	width: 100%;
	margin: 20rpx 0;
	text-align: center;
}
.color-o{
	margin: 0 10rpx;
}
</style>
