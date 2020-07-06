<template>
	<view class="bargain-list">
		<view class="img-wrap">
			<image src="/static/image/bargain.png" mode="" style="width:100%; height: 100%"></image>
		</view>
		<view class="list-wrap">
			<view class="list-item" v-for="(item, index) in list" :key="index">
				<view class="pic">
					<image :src="item.image" mode="" style="width: 100%; height: 100%;"></image>
				</view>
				<view class="cont">
					<text class="title fsz26">{{item.goods_name||''}}</text>
					<view class="desc">
						<view>
							<text class="num fsz24 origin">原价：{{item.start_price}}元</text>
							<text class="num fsz24">可砍至: <text>{{item.end_price||'0.00'}}</text>元</text>
						</view>
						<navigator :url="'/pages/bargain/index?id='+item.id">
							<text class="btn fsz24">发起砍价</text>
						</navigator>
					</view>
				</view>
			</view>
			<uni-load-more :status="loadStatus"></uni-load-more>
		</view>
	</view>
</template>

<script>
	import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		data() {
			return {
				page: 1,
				limit: 10,
				list: [],
				loadStatus: 'more',
				userInfo:{id:0}//用户信息
			}
		},
		components: {
			lvvPopup,
			uniLoadMore
		},
		mounted() {
		},
		onLoad(options) {
			this.cid = options.cid;
			this.getBargainList();
			this.getUserInfo();
		},
		onReachBottom() {
			if (this.loadStatus === 'more') {
				this.getBargainList();
			}
		},
		methods: {
			// 显示modal弹出框
			toshow(type) {
				this.$refs.lvvpopref.show();
			},
			// 关闭modal弹出框
			toclose() {
				this.$refs.lvvpopref.close();
			},
			//获取砍价列表
			getBargainList() {
				let data = {
					page: this.page,
					limit: this.limit
				};
				this.loadStatus = 'loading';
				this.$api.getBargainList(data, res => {
					if (res.status) {
						const _list = res.data.list;
						this.list = [...this.list, ..._list];
						if (res.data.count > this.list.length) {
							this.loadStatus = 'more';
							this.page++;
						} else {
							this.loadStatus = 'noMore';
						}
					} else {
						// 接口请求出错了
						this.$common.errorToShow(res.msg);
					}
				});
			},
			getUserInfo() {
				var _this = this;
				if (!this.$db.get('userToken')) {
					this.$api.userInfo({}, res => {
						if (res.status) {
							_this.userInfo = res.data;
						}
					});
				}
			},
		}
	}
</script>

<style>

	.pop-wrap {
		width: 100%;
		max-height: 804upx;
		background: #FFFFFF;
		position: absolute;
		left:0;
		bottom: 0;
	}
	.bargain-list {
		background-color: #fff !important;
	}
	.bargain-list .img-wrap {
		width: 100%;
		height: 300rpx;
	}
	.bargain-list .list-item {
		border-bottom: 2rpx solid #f3f3f3;
		padding: 20rpx;
		display: flex;
	}
	.pic {
		width: 141rpx;
		flex: 0 0 141rpx;
		height: 141rpx;
		margin-right: 25rpx;
	}
	.cont {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		flex: 1;
	}

	.cont .desc {
		display: flex;
		justify-content: space-between;
	}
	.cont .desc .num {
		color: #FF7159;
		margin-left: 10rpx;
	}
	.cont .desc .num:first-child {
		margin-left: 0;
	}
	.cont .desc .btn {
		width: 180rpx;
		height: 48rpx;
		text-align: center;
		line-height: 48rpx;
		background-color: #FF7159;
		border-radius: 4rpx;
		color: #ffffff;
	}
	.pop-wrap {
		width: 100%;
	}
	.pop-title {
		position: relative;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		border-bottom: 1rpx solid #a0a0a0;
	}
	.pop-wrap .close-btn {
		width: 19rpx;
		height: 19rpx;
		position: absolute;
		top: 50%;
		right: 20rpx;
		transform: translateY(-50%);
	}
	.pop-wrap .address {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 25rpx 0;
		border-bottom: 1rpx solid #a0a0a0;
	}
	.pop-wrap .address .add-info {
		font-size: 20rpx;
	}
	.pop-wrap .address .add-user {
		margin-bottom: 18rpx;
	}
	.pop-wrap .address .add-user text {
		font-size: 26rpx;
		margin-right: 18rpx;
	}
	.pop-wrap .address .icon1 {
		width: 71rpx;
		flex: 0 0 71rpx;
		height: 65rpx;
		margin-left: 30rpx;
		margin-right: 50rpx;
	}
	.pop-wrap .address .icon2 {
		width: 36rpx;
		flex: 0 0 36rpx;
		height: 34rpx;
		margin-left: 50rpx;
		margin-right: 40rpx;
	}
	.pop-wrap .address .add-info {
		flex: 1;
	}
	.origin {
		color: #000!important;
	}
</style>
