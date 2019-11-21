<template>
	<view class="content">
		<view class="type-c" v-if="list.length">
			<view class="cell-group margin-cell-group" v-for="(item, index) in list" :key="index">
				<view class="cell-item">
					<view class="cell-item-hd">
						<view class='cell-hd-title'>下单人:{{ item.buy_user }}</view>
					</view>
					<view class="cell-item-ft">
						<view class="cell-ft-p color-9">
							{{ item.ctime }}
						</view>
					</view>
				</view>
				<view class="cell-item">
					<view class="cell-item-hd">
						<view class='cell-hd-title color-9'>订单号:{{ item.order_id }}</view>
					</view>
					<view class="cell-item-ft red-price">
						{{ item.amount }}
					</view>
				</view>
			</view>
			<uni-load-more
			:status="loadStatus"
			></uni-load-more>
		</view>
		<view class="order-none" v-else>
			<image class="balance-none-img" src="/static/image/order.png" mode=""></image>
		</view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
import { goods } from '@/config/mixins.js'
	export default {
		mixins: [ goods ],
		components: {
			uniLoadMore
		},
		data() {
			return {
				startTime: 0,
				screenName: '',
				page: 1,
				limit: 10,
				list: [], // 商品浏览足迹
				loadStatus: 'more'
			};
		},
		onLoad () {
			this.getDistributionOrder()
		},
		onShow() {
			const res = uni.getSystemInfoSync();
		},
		onReachBottom () {
			if (this.loadStatus === 'more') {
				this.getDistributionOrder()
			}
		},
		methods: {
			getDistributionOrder () {
				let data = {
					page: this.page,
					limit: this.limit
				}

				this.loadStatus = 'loading'
				
				this.$api.getDistributionOrder(data, res => {
					//console.log(res);
					if (res.status) {
						let _list = res.data.list
						_list.forEach (item => {
							this.$set(item, 'slide_x', 0)
						})
						this.list = [...this.list, ..._list]
						
						if (res.data.count > this.list.length) {
							this.page ++
							this.loadStatus = 'more'
						} else {
							this.loadStatus = 'noMore'
						}
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			},
		}
	};
</script>

<style scoped>
.type-c .cell-group{
	padding: 10upx 0;
	margin-top: 0;
}
.type-c .cell-item{
	border: none;
	min-height: 70upx;
	padding: 0 26upx 0 0;
}
.type-c .cell-item .red-price{
	font-size: 50upx;
}
.type-c .cell-item .color-9{
	font-size: 24upx;
}
.order-none{
	text-align: center;
	padding: 200upx 0;
}
.balance-none-img{
	width: 274upx;
	height: 274upx;
}
</style>
