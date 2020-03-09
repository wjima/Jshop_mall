<template>
	<view class="content">
		<uni-segmented-control :current="current" :values="items" @clickItem="onClickItem" style-type="text" active-color="#333"></uni-segmented-control>
		<view class="" v-if="listData.length > 0">
			<view class="coupon-c-item" v-for="(item, key) in listData" :key="key">
				<view class="cci-l">
					<view class="cci-l-c color-f" v-if="current == 0">
						coupon
					</view>
					<view class="cci-l-c color-f color-b" v-if="current != 0">
						coupon
					</view>
				</view>
				<view class="cci-r">
					<view class="cci-r-c">
						<view class="ccirc-t color-9">
							{{item.name}}
						</view>
						<view class="ccirc-b">
							<view class="ccirc-b-l">
								<view class="ccirc-b-tip">
									{{ item.expression1 + item.expression2 }}
								</view>
								<view class="ccirc-b-time color-9">
									有效期：{{item.stime}} - {{item.etime}}
								</view>
							</view>
							<view class="ccirc-b-r color-f" @click="goIndex" v-if="current == 0">
								立即使用
							</view>
						</view>
					</view>
				</view>
			</view>
			<uni-load-more :status="loadStatus"></uni-load-more>
		</view>
		<view class="coupon-none" v-else>
			<image class="coupon-none-img" src="/static/image/order.png" mode=""></image>
		</view>
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
	import uniSegmentedControl from "@/components/uni-segmented-control/uni-segmented-control.vue";
	export default {
		components: {
			uniSegmentedControl,uniLoadMore
		},
		data() {
			return {
				items: ['未使用','已使用','已失效'],
				current: 0,
				page: 1,
				limit: 10,
				listData: [],
				loadStatus: 'more'
			}
		},
		onLoad() {
			this.getData();
		},
		onReachBottom () {
			if (this.loadStatus === 'more') {
				this.getData();
			}
		},
		methods: {
			// tab点击切换
			onClickItem(index) {
				if (this.current !== index) {
					this.current = index;
					this.page = 1;
					this.listData = [];
					this.getData();
				}
			},
			//获取优惠券列表
			getData() {
				this.loadStatus = 'loading'
				let data = {
					page: this.page,
					limit: this.limit
				}
				if (this.current == 0) {
					data['display'] = 'no_used';
				}
				if (this.current == 1) {
					data['display'] = 'yes_used';
				}
				if (this.current == 2) {
					data['display'] = 'invalid';
				}
				this.$api.userCoupon(data, res => {
					if (res.status) {
						let now_type = 'no_used';
						if (this.current == 1) {
							now_type = 'yes_used';
						}
						if (this.current == 2) {
							now_type = 'invalid';
						}
						if (now_type == res.data.q_type) {
							if (res.data.page >= this.page) {
								let newList = this.listData.concat(res.data.list);
								this.listData = newList;
								if (res.data.count > this.listData.length) {
									this.page ++
									this.loadStatus = 'more'
								} else {
									this.loadStatus = 'noMore'
								}
							}
						}
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			},
			//跳转首页
			goIndex() {
				uni.switchTab({
					url: '/pages/index/index'
				});
			}
		}
	}
</script>

<style>
.coupon-c-item{
	margin: 30upx 50upx;
	height: 150upx;
	margin-bottom: 20upx;
}
.cci-l{
	width: 60upx;
	height: 100%;
	background-color: #FF7159;
	font-size: 32upx;
	display: inline-block;
	box-sizing: border-box;
	float: left;
	border-top-left-radius: 16upx;
	border-bottom-left-radius: 16upx;
}
.cci-l-c{
	height: 60upx;
	line-height: 44upx;
	width: 150upx;
	text-align: center;
	transform-origin: 30upx 30upx;
	transform: rotate(90deg);
}
.cci-r{
	position: relative;
	height: 150upx;
	width: calc(100% - 70upx);
	margin-left: 10upx;
	display: inline-block;
	background-color: #fff;
}
.cci-r-img{
	position: absolute;
	width: 100%;
	height: 100%;
	background-color: #fff;
}
.cci-r-c{
	position: relative;
	z-index: 99;
}
.ccirc-t{
	font-size: 24upx;
	padding: 10upx 20upx;
}
.ccirc-b{
	padding: 10upx;
	position: relative;
}
.ccirc-b-l{
	display: inline-block;
	max-width: 400upx;
}
.ccirc-b-tip{
	font-size: 28upx;
	width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
.ccirc-b-tip text{
	font-size: 34upx;
}
.ccirc-b-time{
	font-size: 24upx;
}
.ccirc-b-r{
	display: inline-block;
	background-color: #FF7159;
	font-size: 26upx;
	padding: 4upx 10upx;
	border-radius: 4upx;
	position: absolute;
	right: 20upx;
	bottom: 16upx;
}
.color-b{
	background-color: #e5e5e5;
	border-bottom-right-radius: 12upx;
	border-bottom-left-radius: 12upx;
	color: #fff;
}
.coupon-none{
	text-align: center;
	padding: 200upx 0;
}
.coupon-none-img{
	width: 274upx;
	height: 274upx;
}
</style>