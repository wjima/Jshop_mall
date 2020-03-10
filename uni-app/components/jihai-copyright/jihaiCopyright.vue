<template>
	<view class="cpr">
		<view class="color-9">
			吉海科技 © jihainet.com 版权所有
		</view>
		<view class="color-9 beian">
			<view v-if="shop_beian"><a href="http://beian.miit.gov.cn/" target="_blank">备案号：{{shop_beian}}</a></view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			shop_beian: this.$store.state.config.shop_beian||''
		}
	}
}
</script>

<style>
.cpr{
	text-align: center;
	font-size: 24upx;
	margin: 20upx 0;
}
.beian a{
	text-decoration: none;
	color: #999 !important;
}
</style>
