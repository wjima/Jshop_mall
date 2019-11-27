<template>
	<view class="content">
		<view v-if="listData.length > 0">
			<view class="invoice-item" v-for="(item, index) in listData" :key="index">
				<view class="invoice-left">
					<image src="/static/image/invoice.png" class="left-ico"></image>
				</view>
				<view class="invoice-right">
					<view class="invoice-amount">￥{{item.amount}} <text :class="item.status == 1?'status_no':'status_yes'">{{item.status_text}}</text></view>
					<view class="invoice-title">{{item.title}}</view>
					<view class="invoice-tax_number" v-if="item.tax_number">{{item.tax_number}}</view>
					<view class="invoice-time">{{item.ctime_text}}</view>
				</view>
			</view>
			<uni-load-more :status="loadStatus"></uni-load-more>
		</view>
		<view class="invoice-none" v-else>
			<image class="invoice-none-img" src="/static/image/order.png" mode=""></image>
		</view>
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				id: 0,
				page: 1,
				limit: 10,
				listData: [],
				loadStatus: 'more'
			}
		},
		onLoad(e) {
			if(e.id){
				this.id = e.id;
			}
			this.getData();
		},
		onReachBottom () {
			if (this.loadStatus === 'more') {
				this.getData();
			}
		},
		methods: {
			//获取我的发票列表
			getData() {
				this.loadStatus = 'loading'
				let data = {
					page: this.page,
					limit: this.limit
				}
				if(this.id != 0){
					data['id'] = this.id;
				}
				this.$api.myInvoiceList(data, res => {
					if (res.status) {
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
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			}
		}
	}
</script>

<style>
.invoice-item{
	margin: 30upx 50upx;
	margin-bottom: 20upx;
	background-color: #ffffff;
	padding: 30upx;
	border-radius: 10upx;
	box-shadow: 0 0 10upx #eeeeee;
	overflow: auto;
}
.invoice-left{
	height: 90upx;
	width: 90upx;
	overflow: hidden;
	float: left;
}
.left-ico{
	height: 100%;
	width: 100%;
}
.invoice-right{
	width: calc(100% - 120upx);
	float: right;
}
.invoice-amount{
	font-size: 32upx;
	margin-bottom: 20upx;
}
.invoice-title{
	font-size: 24upx;
	color: #888888;
}
.invoice-tax_number{
	font-size: 24upx;
	color: #888888;
}
.invoice-time{
	border-top: 2upx #eeeeee dashed;
	margin-top: 20upx;
	padding-top: 20upx;
	font-size: 24upx;
	color: #F44336;
}
.status_no{
	margin-left: 20upx;
	font-size: 24upx;
	color: #F44336;
}
.status_yes{
	margin-left: 20upx;
	font-size: 24upx;
	color: #0d9e13;
}
.invoice-none{
	text-align: center;
	padding: 200upx 0;
}
.invoice-none-img{
	width: 274upx;
	height: 274upx;
}
</style>