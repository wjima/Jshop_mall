<template>
	<view class="content">
		<uni-segmented-control :current="tab" :values="items" @clickItem="onClickItem" style-type="text" active-color="#333"></uni-segmented-control>
		<view class="order-list">
			<view class="goods-detail" v-if="list.length">
				<view class="order-item" v-for="(item, index) in list" :key="index">
					<view class='cell-group'>
						<view class='cell-item' @click="orderDetail(item.order_id)">
							<view class='cell-item-hd'>
								<view class='cell-hd-title'>订单编号：{{ item.order_id }}</view>
							</view>
							<view class='cell-item-ft'>
								<text class='cell-ft-text'>{{ item.order_status_name }}</text>
							</view>
						</view>
					</view>
					<view class='img-list'>
						<view class='img-list-item' v-for="(goods, key) in item.items" :key="key">
							<image class='img-list-item-l little-img have-none' :src='goods.image_url' mode='aspectFill'></image>
							<view class='img-list-item-r little-right'>
								<view class='little-right-t'>
									<view class='goods-name list-goods-name' @click="orderDetail(item.order_id)">{{ goods.name }}</view>
									<view class='goods-price'>￥{{ goods.price }}</view>
								</view>
								<view class="romotion-tip">
									<view class="romotion-tip-item" v-for="(promotion, k) in goods.promotion_list" :key="k">
										{{ promotion }}
									</view>
								</view>
								<view class='goods-item-c'>
									<view class='goods-buy'>
										<view class='goods-salesvolume' v-if="goods.addon !== null">{{ goods.addon }}</view>
										<view class='goods-num'>× {{ goods.nums }}</view>
									</view>
								</view>
							</view>
						</view>
					</view>
					<view class='cell-group'>
						<view class='cell-item'>
							<view class='cell-item-ft goods-num'>
								<text class='cell-ft-text'>合计<text class="red-price">￥ {{ item.order_amount }}</text></text>
								<text class='cell-ft-text'>共 {{ item.items.length }} 件</text>
							</view>
						</view>
					</view>
					<view class='order-list-button'>
						<button class='btn btn-circle btn-g' hover-class="btn-hover" @click="orderDetail(item.order_id)">查看详情</button>
						<button class='btn btn-circle btn-w' hover-class="btn-hover" v-if="item.status === 1 && item.pay_status === 1" @click="toPay(item.order_id)">立即支付</button>
						<button class='btn btn-circle btn-w' hover-class="btn-hover" v-if="item.status === 1 && item.pay_status >= 2 && item.ship_status >= 3 && item.confirm === 1" @click="tackDelivery(index)">确认收货</button>
						<button class='btn btn-circle btn-w' hover-class="btn-hover" v-if="item.status === 1 && item.pay_status >= 2 && item.ship_status >= 3 && item.confirm >= 2 && item.is_comment === 1" @click="toEvaluate(item.order_id)">立即评价</button>
					</view>
				</view>
				<uni-load-more :status="loadStatus"></uni-load-more>
			</view>
			<view class="order-none" v-else>
				<image class="order-none-img" src="/static/image/order.png" mode=""></image>
			</view>
		</view>
	</view>
</template>

<script>
import uniSegmentedControl from "@/components/uni-segmented-control/uni-segmented-control.vue"
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
import { orders, goods } from '@/config/mixins.js'
export default {
	mixins: [orders, goods],
	components: {
		uniSegmentedControl, uniLoadMore
	},
	data() {
		return {
			items: [
				'全部',
				'待付款',
				'待发货',
				'已发货',
				'待评价',
			],
			list: [],
			page: 1,
			limit: 5, // 每页订单显示数量
			loadStatus: 'more',
			status: [0, 1, 2, 3, 4]	,// 订单状态 0全部 1待付款 2待发货 3待收货 4待评价
			isReload: false, // 页面是否刷新?重载
		}
	},
	onLoad () {
		this.initData()
	},
	onShow () {
		// #ifdef MP-ALIPAY || MP-TOUTIAO
		let order_user_ship = this.$db.get('order_user_ship', true);
		if (order_user_ship) {
			this.isReload = order_user_ship;
			this.$db.del('order_user_ship', true);
		}
		let order_user_evaluate = this.$db.get('order_user_evaluate', true);
		if (order_user_evaluate) {
			this.isReload = order_user_evaluate;
			this.$db.del('order_user_evaluate', true);
		}
		// #endif
		if (this.isReload) {
			this.initData()
		}
	},
	computed: {
		// 获取订单列表tab
		tab () {
			return this.$store.state.orderTab
		}
	},
	methods: {
		// 初始化数据并获取订单列表
		initData (page = 1) {
			this.page = page
			this.list = []
			this.orderList()
		},
		// 订单状态切换
		onClickItem(index) {
			if (this.tab !== index) {
				this.$store.commit('orderTab', index)
				this.initData()
			}
		},
		// 获取订单列表
		orderList () {
			let data = {
				page: this.page,
				limit: this.limit,
				status: this.status[this.tab]
			}
			this.loadStatus = 'loading'
			this.$api.orderList(data, res => {
				if (res.status) {
					let _list = res.data.list
					if (res.data.status == this.status[this.tab]) {
						this.list = [...this.list, ...this.formatOrderStatus(_list)]
						// 判断所有数据是否请求完毕
						if (res.data.count > this.list.length) {
							this.page ++
							this.loadStatus = 'more'
						} else {
							this.loadStatus = 'noMore'
						}
					}
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
			if (this.isReload) {
				this.isReload = false
			}
		},
		// 确认收货
		tackDelivery (index) {
			this.$common.modelShow('提示', '确认执行收货操作吗?', () => {
				let data = {
					order_id: this.list[index].order_id
				}
				this.$api.confirmOrder(data, res => {
					if (res.status) {
						this.$common.successToShow('确认收货成功', () => {
							if (this.tab !== 0) {
								this.list.splice(index, 1)
							} else {
								this.initData()
							}
						})
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			})
		},
		// 订单状态统一在这处理
		formatOrderStatus (orderList) {
			orderList.forEach (item => {
				switch (item.status) {
					case 1:
						if (item.pay_status === 1) {
							this.$set(item, 'order_status_name', '待付款')
						} else if (item.pay_status >= 2 && item.ship_status === 1){
							this.$set(item, 'order_status_name', '待发货')
						} else if (item.pay_status >= 2 && item.ship_status === 2){
							this.$set(item, 'order_status_name', '部分发货')
						} else if (item.pay_status >= 2 && item.ship_status >= 3 && item.confirm === 1) {
							this.$set(item, 'order_status_name', '已发货')
						} else if (item.pay_status >= 2 && item.ship_status >= 3 && item.confirm >= 2 && item.is_comment === 1) {
							this.$set(item, 'order_status_name', '待评价')
						} else if (item.pay_status >= 2 && item.ship_status >= 3 && item.confirm >= 2 && item.is_comment >= 2) {
							this.$set(item, 'order_status_name', '已评价')
						}
						break
					case 2:
						this.$set(item, 'order_status_name', '完成')
						break
					case 3:
						this.$set(item, 'order_status_name', '取消')
						break
				}
			});
			for(let i in orderList){
				for(let j in orderList[i].items){
					orderList[i].items[j].promotion_list = JSON.parse(orderList[i].items[j].promotion_list);
				}
			}
			return orderList
		}
	},
	// 页面下拉到底部触发
	onReachBottom () {
		if (this.loadStatus == 'more') {
			this.orderList()
		}
	}
}
</script>

<style>
.segmented-control {
	/*  #ifdef  H5  */
	top: 44px;
	/*  #endif  */
	/*  #ifndef  H5  */
	top: 0;
	/*  #endif  */
	width: 100%;
	background-color: #fff;
	position: fixed;
	
	z-index: 999;
}
.segmented-control.text{
	width: 100% !important;
}
.segmented-control-item{
	line-height: 70upx;
}
.order-list{
	margin-top: 64upx;
}
.order-item{
	margin-bottom: 20upx;
}
.img-list{
	margin-top: 2upx;
}
.cell-group,.img-list-item {
	background-color: #fff;
}
.cell-hd-title{
	font-size: 22upx;
	color: #666;
}
.cell-ft-text{
	top: 0;
	font-size: 22upx;
	color: #333;
}
.order-list-button{
	width: 100%;
	background-color: #fff;
	text-align: right;
	padding: 10upx 26upx;
}
.order-list-button .btn{
	height: 50upx;
	line-height: 50upx;
}
.order-list-button .btn-w{
	margin-left: 20upx;
}
.goods-num .cell-ft-text{
	color: #999;
	line-height: 32upx;
}
.goods-num .cell-ft-text:first-child{
	margin-left: 10upx;
}
.order-none{
	text-align: center;
	padding: 200upx 0;
}
.order-none-img{
	width: 274upx;
	height: 274upx;
}
.goods-name{
	min-height: 74upx;
}
</style>