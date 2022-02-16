<template>
	<view class="zdTabBar">
		<view class="ul df">
			<template v-for="(item, index) in taBbarList">
				<view :class="['li', current == index ? 'cur' : '']" :key="index" @click="navigatorTo(item)">
					<view class="img"><image :src="current == index ? item.selectIcon : item.icon" mode="widthFix"></image></view>
					<view :class="current == index ? ' p' : 'text-active'">{{ item.name }}</view>
				</view>
			</template>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			current: 0,
			taBbarList: [
				/* { 	
						"type": 0, 
						"icon": '/static/image/index_gray.png', 
						"selectIcon": '/static/image/index_black.png',
						"name": "首页", 
						"url": '/pages/index/index', 
						"pType": '' ,
					},
					
					{
						"type": 0, 
						"icon": "/static/image/classify_gray.png",
						"selectIcon": "/static/image/classify_black.png",
						"url": "/pages/classify/classify",
						"name": "分类",
						"pType": ''
					},
					{
						"type": 0, 
						"icon": "/static/image/cart_gray.png",
						"selectIcon": "/static/image/cart_black.png",
						"url": "/pages/cart/index/index",
						"name": "购物车",
						"pType": ''
					},
					{
						"type": 0, 
						"icon": "/static/image/user_gray.png",
						"selectIcon": "/static/image/user_black.png",
						"url": "/pages/member/index/index",
						"name": "我的",
						"pType": ''
					},
					
				 */
			],
			currentPage: ''
		};
	},
	mounted() {
		this.currentPage = getCurrentPages()[getCurrentPages().length - 1].route;
		if (this.$store.state.config && this.$store.state.config.front_menu) {
			let front_menu = this.$store.state.config.front_menu;
			for (let i in front_menu) {
				this.taBbarList.push({
					type: 0,
					icon: front_menu[i].icon_url,
					selectIcon: front_menu[i].selecticon_url,
					url: front_menu[i].url,
					name: front_menu[i].name,
					pType: front_menu[i].ptype
				});
			}
		}
		for (let i in this.taBbarList) {
			if (this.taBbarList[i].url.indexOf(this.currentPage) > -1) {
				this.current = i;
				return;
			}
		}
	},
	methods: {
		navigatorTo(val) {
			// let current = getCurrentPages()[getCurrentPages().length - 1].route
			if (val.url.indexOf(this.currentPage) > -1) {
				return;
			}
			if (val.pType == 'redirectTo') {
				uni.redirectTo({
					url: val.url
				});
			} else if (val.pType == 'navigateTo') {
				uni.navigateTo({
					url: val.url
				});
			} else {
				uni.redirectTo({
					url: val.url
				});
			}
		}
	}
};
</script>
<style lang="scss" scoped>
image {
	display: block;
	width: 100%;
	height: 100%;
}

.df {
	display: flex;
}
.bgja {
	// background-image: linear-gradient(right, #00CEC1, #00B7B8);
	// background-image: -webkit-linear-gradient(right, #00CEC1, #00B7B8);
}

.zdTabBar {
	position: relative;
	display: block;
	height: 100rpx;
	.ul {
		position: fixed;
		left: 0;
		bottom: 0;
		right: 0;
		z-index: 9999;
		background: #ffffff;
		.li {
			flex: 1;
			padding-top: 5px;
			position: relative;

			.img {
				width: 24px;
				height: 24px;
				overflow: hidden;
				margin: 0 auto;
			}

			.ic-wrap {
				position: relative;
				width: 180rpx;
				height: 60rpx;
				border-radius: 50%;
				padding: 6rpx;
				text-align: center;
				margin: -10rpx auto 0px;
				color: #ffffff;
				.ic {
					width: 27px;
					height: 27px;
					border-radius: 50%;
					font-size: 20px;
					line-height: 27px;
					font-weight: bold;
				}
			}
			.p {
				margin-top: 2px;
				text-align: center;
				font-size: 11px;
				line-height: 18px;
			}

			&.cur {
				.p {
					// color: $theme-main-primary;
				}
			}
		}
	}
}

.text-active {
	// color:  $fs-h2;
	margin-top: 2px;
	text-align: center;
	font-size: 11px;
	line-height: 18px;
}

.er_code {
	width: 46px !important;
	height: 46px !important;
	position: absolute;
	top: 62%;
	left: 50%;
	transform: translate(-50%, -50%);
}
button.ic {
	padding: 0;
	&::after {
		border: none;
	}
}
</style>
