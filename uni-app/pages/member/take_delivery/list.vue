<template>
	<view class="content">
		<view class="order-list">
			<view class="goods-detail">
				<view class="order-item" v-for="(item, key) in ladingList" :key="key">
					<view class='cell-group'>
						<view class='cell-item' style="padding: 10upx 26upx 0 0;">
							<view class='cell-item-hd'>
								<view class='cell-hd-title'>提货码：{{item.id}}</view>
							</view>
							<view class='cell-item-ft'>
								<text class='cell-ft-text'>{{item.status_name}}</text>
							</view>
						</view>
					</view>
					<view class='cell-group'>
						<view class='cell-item'>
							<view class='cell-item-hd'>
								<view class='cell-hd-title'>订单编号：{{item.order_id}}</view>
							</view>
						</view>
					</view>
					<view class='img-list'>
						<view class='img-list-item' v-for="(v, k) in item.order_items" :key="k">
							<image class='img-list-item-l little-img have-none' :src='v.image_url' mode='aspectFill'></image>
							<view class='img-list-item-r little-right'>
								<view class='little-right-t'>
									<view class='goods-name list-goods-name'>{{v.name}}</view>
									<view class='goods-price'>￥{{v.price}}</view>
								</view>
								<view class='goods-item-c'>
									<view class='goods-buy'>
										<view class='goods-salesvolume' v-show="v.addon">{{v.addon}}</view>
										<view class='goods-num'>×{{v.nums}}</view>
									</view>
								</view>
							</view>
						</view>
					</view>
					<view class='order-list-button'>
						<button class='btn btn-circle btn-g' hover-class="btn-hover" v-if="item.status == 2" @click="ladingDel(item.id)">删除</button>
						<button class='btn btn-circle btn-w' hover-class="btn-hover" v-if="item.status == 1" @click="ladingWrite(item.id)">提货单核销</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data(){
		return {
			ladingList: [],
		}
	},
	onShow(){
		this.getLadingList();
	},
	methods: {
		//获取提货单列表
		getLadingList() {
			this.$api.storeLadingList({}, res => {
				this.ladingList = res.data;
			});
		},
		
		//提货单核销
		ladingWrite(id) {
			this.$common.navigateTo('./index?id=' + id);
		},

		//删除
		ladingDel(id) {
			this.$common.modelShow('提示', '删除提货单后将无法找回！', res => {
				let data = {
					'lading_id': id
				}
				this.$api.ladingDel(data, res => {
					this.$common.successToShow(res.msg, res => {
						this.getLadingList();
					});
				});
			});
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
.segmented-control-item{
	line-height: 70upx;
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
</style>