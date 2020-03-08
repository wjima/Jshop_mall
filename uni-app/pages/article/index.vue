<template>
	<view class="content">
		<view class="article">
			<view class="article-title" v-if="shopLogo && shopName">
				<img :src="shopLogo" alt="" class="shop-logo" />
				<text class="shop-name">{{ shopName }}</text>
				<text class="fsz24 color-9 article-time">{{ info.ctime }}</text>
				<text class="color-9 article-time" style="font-size:24rpx" v-if="idType != 2">
					<image src="../../static/image/yuedu.png" mode="" style="width: 30rpx;height: 30rpx;vertical-align: middle;"></image>
					{{ info.pv }}
				</text>
			</view>
			<view class="article-content"><jshopContent :content="info.content" v-if="info.content"></jshopContent></view>
		</view>
	</view>
</template>

<script>
import jshopContent from '@/components/jshop/jshop-content.vue';
export default {
	components: {
		jshopContent
	},
	data() {
		return {
			idType: 1, //1文章 2公告 3微信图文消息
			id: 0,
			info: {},
            shareUrl: '/pages/share/jump'
		};
	},
	onLoad(e) {
		this.idType = e.id_type;
		this.id = e.id;

		if (!this.idType && !this.id) {
			this.$common.errorToShow('请求出错', res => {
				uni.switchTab({
					url: '/pages/index/index'
				});
			});
		} else if (this.idType == 1) {
			this.articleDetail();
		} else if (this.idType == 2) {
			uni.setNavigationBarTitle({
				title: '公告详情'
			});
			this.noticeDetail();
		} else if (this.idType == 3) {
			uni.setNavigationBarTitle({
				title: '图文消息'
			});

			this.messageDetail();
		}
	},
	computed: {
		shopName() {
			return this.$store.state.config.shop_name;
		},
		shopLogo() {
			return this.$store.state.config.shop_logo;
		}
	},
	methods: {
		articleDetail() {
			let data = {
				article_id: this.id
			};
			this.$api.articleInfo(data, res => {
				if (res.status) {
					this.info = res.data;
					uni.setNavigationBarTitle({
						title: this.info.title
					});
				} else {
					this.$common.errorToShow(res.msg, res => {
						uni.navigateBack({
							delta: 1
						});
					});
				}
			});
		},
		noticeDetail() {
			let data = {
				id: this.id
			};
			this.$api.noticeInfo(data, res => {
				if (res.status) {
					this.info = res.data;
					uni.setNavigationBarTitle({
						title: this.info.title
					});
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
		//微信图文消息
		messageDetail() {
			let data = {
				id: this.id
			};
			this.$api.messageDetail(data, res => {
				if (res.status) {
					this.info = res.data;
					uni.setNavigationBarTitle({
						title: this.info.title
					});
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
        //获取分享URL
        getShareUrl() {
            let data = {
                client: 2,
                url: "/pages/share/jump",
                type: 1,
                page: 5,
                params: {
                    article_id: this.id,
                    article_type: this.idType
                }
            };
            let userToken = this.$db.get('userToken');
            if (userToken && userToken != '') {
            	data['token'] = userToken;
            }
            this.$api.share(data, res => {
                this.shareUrl = res.data
            });
        }
	},
    watch:{
        id: {
            handler () {
                this.getShareUrl();
            },
            deep: true
        }
    },
	//分享
	onShareAppMessage() {
		return {
			title: this.info.title,
			// #ifdef MP-ALIPAY
			//desc: this.goodsInfo.brief,
			// #endif
			//imageUrl: this.goodsInfo.album[0],
			path: this.shareUrl
		};
	}
};
</script>

<style>
.content {
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
