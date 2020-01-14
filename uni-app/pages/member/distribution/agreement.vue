<template>
	<view class="content">
		<view class="article">
			<view class="article-content">
				<rich-text :nodes="content"></rich-text>
			</view>
		</view>
	</view>
</template>

<script>
	import htmlParser from '@/common/html-parser'
	export default {
		data() {
			return {
				content:this.$store.state.config.distribution_agreement,
			}
		},
		onLoad(e) {
		},
		computed: {
		},
		methods: {
		}
	}
</script>

<style>
	.content {
		/*  #ifdef  H5  */
		height: calc(100vh - 90upx);
		/*  #endif  */
		/*  #ifndef  H5  */
		height: 100vh;
		/*  #endif  */
		background-color: #fff;
	}

	.article {
		padding: 20upx;
	}

	.article-title {
		font-size: 32upx;
		color: #333;
		margin-bottom: 20upx;
		position: relative;
		height: 100upx;
	}

	.article-time {
		margin-left: 20upx;
	}

	.article-content {
		font-size: 28upx !important;
		color: #666;
		line-height: 1.6;
		margin-top: 20upx;
	}

	.article-content p img {
		width: 100% !important;
	}

	.shop-logo {
		width: 60upx;
		height: 60upx;
		border-radius: 50%;
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
	}

	.shop-name {
		line-height: 100upx;
		margin-left: 80upx;
	}
</style>
