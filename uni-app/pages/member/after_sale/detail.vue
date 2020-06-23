<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group margin-cell-group'>
				<view class='cell-item add-title-item'>
					<view class='cell-item-bd cell-item-bd-block'>
						<view class="cell-bd-view black-text">
							<text class="cell-bd-text color-3">退款单状态</text>
						</view>
						<view class="cell-bd-view">
							<text class="cell-bd-text color-9">{{status_name}} {{refund_name}} {{reship_name}}...</text>
						</view>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group'>
				<view class='cell-item right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>商品信息</view>
					</view>
				</view>
				<view class="img-list" v-for="(item, key) in goodsInfo" :key="key">
					<view class="img-list-item">
						<image class="img-list-item-l little-img have-none" :src="item.image_url" mode="aspectFill"></image>
						<view class="img-list-item-r little-right">
							<view class="little-right-t">
								<view class="goods-name list-goods-name">{{item.name}}</view>
							</view>
							<view class="goods-item-c" v-if="item.addon">
								<view class="goods-buy">
									<!-- 商品规格 -->
									<view class="goods-salesvolume mr5">
										{{item.addon}}
									</view>
								</view>
							</view>
							<view class="goods-item-c">
								<view class="goods-buy">
									<!-- 商品规格 -->
									<view class="goods-salesvolume mr5">
										退货数量:{{item.nums}}
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group'>
				<view class='cell-item'>
					<view class='cell-item-hd'><view class='cell-hd-title'>商品状态</view></view>
					<view class='cell-item-ft'><view class="cell-ft-p">{{type_name}}</view></view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-hd'><view class='cell-hd-title'>退款金额</view></view>
					<view class='cell-item-ft'><view class="cell-ft-p red-price">{{refund}}元</view></view>
				</view>
			</view>
			<view class='cell-group margin-cell-group' v-if="images.length > 0">
				<view class='cell-item right-img'><view class='cell-item-hd'><view class='cell-hd-title'>图片凭证</view></view></view>
				<view class="">
					<view class="evaluate-c-b">
						<view class="goods-img-item" v-for="(item, key) in images" :key="key">
							<image :src="item.url" mode="aspectFit" @click="clickImg(item.url)"></image>
						</view>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group'>
				<view class='cell-item right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>问题描述</view>
					</view>
				</view>
				<view class="cell-textarea">
					<text v-if="reason">{{reason}}</text>
					<text v-else>暂无描述</text>
				</view>
			</view>
			<view class='cell-group margin-cell-group' v-show="status == 2 && reship_status == 1">
				<view class='cell-item right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>退货邮寄信息</view>
					</view>
				</view>
				<!-- <view class='cell-item add-title-item right-img'>
					<view class='cell-item-hd' style="min-width: 50rpx;">
						<image class='cell-hd-icon' src='/static/image/location.png'></image>
					</view>
					<view class='cell-item-bd' style="padding-right: 0;">
						<view class="cell-bd-view">
							<text class="cell-bd-text">收货人：{{ reship_info.reship_name }}</text>
							<text class="cell-bd-text-right">{{ reship_info.reship_mobile }}</text>
						</view>
						<view class="cell-bd-view">
							<text class="cell-bd-text address">{{ reship_info.reship_area + reship_info.reship_address }}</text>
						</view>
					</view>
				</view> -->
				<view class="cell-group margin-cell-group" style="margin-top: 0;">
					<view class="cell-item add-title-item right-img cell-item-mid">
						<view class="cell-item-hd">
							<image class="cell-hd-icon" src="/static/image/location.png"></image>
						</view>
						<view class="cell-item-bd cell-item-bd-block" style="width: 100%;padding-right: 0;">
							<view class="cell-bd-view" style="justify-content: space-between;">
								<text class="cell-bd-text">收货人：{{ reship_info.reship_name }}</text>
								<text class="cell-bd-text-right">{{ reship_info.reship_mobile|| '' }}</text>
							</view>
							<view class="cell-bd-view">
								<text class="cell-bd-text address">{{ reship_info.reship_area + reship_info.reship_address }}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group' v-show="status == 2 && reship_status == 1">
				<view class='cell-item right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>请填写回邮商品物流信息</view>
					</view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title fsz26'>快递公司</view>
					</view>
					<view class='cell-item-bd'>
						<input class='cell-bd-input fsz26' type="text" v-model="logi_code" placeholder="请填写快递公司名称"/>
					</view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title fsz26'>物流单号</view>
					</view>
					<view class='cell-item-bd'>
						<input class='cell-bd-input fsz26' type="text" v-model="logi_no" placeholder="请填写物流单号" />
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group' v-show="status == 2 && reship_status > 1">
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>快递公司</view>
					</view>
					<view class='cell-item-bd'>
						<input class='cell-bd-input' type="text" disabled="false" :value="logi_code"/>
					</view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>物流单号</view>
					</view>
					<view class='cell-item-bd'>
						<input class='cell-bd-input' type="text" disabled="false" :value="logi_no"/>
					</view>
				</view>
			</view>
		</view>
		<view class="button-bottom" v-show="status == 2 && reship_status == 1">
			<button class="btn btn-b" @click="submitBtn" :disabled='submitStatus' :loading='submitStatus'>提交</button>
		</view>
		<view class="button-bottom" v-show="(order_status == 1 && status == 2 && refund_status != 1 && refund_status != 0) || (order_status == 1 && status == 2 && reship_status == 3)">
			<button class="btn btn-b" @click="repeat">再次申请售后</button>
		</view>
	</view>
</template>

<script>
export default {
    data() {
        return {
			delivers: ["请选择物流公司","顺丰", "中通", "圆通","韵达"],
			deliverIndex: 0,
			type_name:'',     //售后类型显示
			refund:0,         //退款金额
			images:[],        //图片
			reason: '暂无',       //问题描述
			ttype:1,          //售后类型
			status:1,         //售后单状态
			status_name: '审核中',   //售后单状态文字描述
			reship_status:0,        //退货单状态
			reship_name:'',
			refund_status:0,        //退款单状态
			refund_name:'',
			reship_info:[],         //退货单明细,如果售后单未审核呢，那么显示的是售后单明细，如果售后单审核通过了，显示退款单明细
			items:[],             //退货明细
			mark:"暂无",            //拒绝原因
			logi_no:'',            //回填物流信息
			logi_code:'',          //物流公司
			reship_id:'',
			mode: 'aspectFill',
			order_id: '', //订单号
			order_status: '', //订单状态
			submitStatus: false,
			goodsInfo: []
        }
    },
	methods: {
		//提交按钮
		submitBtn() {
			this.submitStatus = true;
			if (this.logino == '') {
				this.$common.errorToShow('请输入退货快递信息');
				this.submitStatus = false;
				return false;
			}
			let data = {
				logi_no: this.logi_no,
				logi_code:this.logi_code,
				reship_id: this.reship_id,
			};
			this.$api.sendShip(data, res => {
				if (res.status) {
					this.$common.successToShow('提交成功', ress => {
						this.submitStatus = false;
						uni.navigateBack({
							delta: 1
						});
					});
				} else {
					this.$common.errorToShow(res.msg);
					this.submitStatus = false;
				}
			});
		},
		repeat() {
			this.$common.navigateTo('../after_sale/index?order_id='+this.order_id);
		},
		// 图片点击放大
		clickImg (img) {
			// 预览图片
			uni.previewImage({
				urls: img.split()
			});
		}
	},
	//页面加载
	onLoad(options) {
		let data = {
			aftersales_id: options.aftersales_id
		}
		this.$api.afterSalesInfo(data, res => {
			if(res.status){
				let info = res.data.info;
				if (info.type == 1){
					this.ttype = 1;
					this.type_name = '未收货';
				}else{
					this.ttype = 2;
					this.type_name = '已收货';
				}
				this.goodsInfo = info.items;
				this.refund = info.refund;
				this.images = info.images;
				this.reason = info.reason;
				this.reship_info = res.data.reship;
				this.order_id = info.order_id;
				this.order_status = info.order_status;
				if(info.mark){
					this.mark = info.mark;
				}
				if(info.status == 1){
					this.status = 1;
					this.status_name = '审核中';
				}else if(info.status == 2){
					this.status = 2;
					this.status_name = '申请通过';
					//退款单状态
					if (info.bill_refund) {
						if (info.bill_refund.status == 1) {
							this.refund_status = 1;
							this.refund_name = '退款中';
						} else if (info.bill_refund.status == 2) {
							this.refund_status = 2;
							this.refund_name = '退款成功';
						}
					}
		
					//退货单状态
					if(info.bill_reship){
						this.reship_id = info.bill_reship.reship_id
						if(info.bill_reship.status == 1) {
							this.reship_status = 1;
							this.reship_name = '待发退货';
						} else if (info.bill_reship.status == 2) {
							this.reship_status = 2;
							this.reship_name = '待收退货';
							this.logi_no = info.bill_reship.logi_no;
							this.logi_code = info.bill_reship.logi_code;
						} else {
							this.reship_status = 3;
							this.reship_name = '已收退货';
							this.logi_no = info.bill_reship.logi_no;
							this.logi_code = info.bill_reship.logi_code;
						}
					}
				}else{
					this.status = 3;
					this.status_name = '申请驳回';
				}
			} else {
				this.$common.errorToShow(res.msg);
			}
		});
	}
}
</script>

<style>
.back-img{
	width: 100%;
	height: 200upx;
	position: relative;
	background-color: #FF7159;
}
.back-img image{
	width: 100%;
	height: 100%;
	position: absolute;
}
.back-img-c{
	width: 100%;
	height: 100%;
	color: #fff;
	position: relative;
	z-index: 99;
	padding: 50upx;
}
.back-img-t{
	font-size: 32upx;
}
.back-img-b{
	font-size: 24upx;
}
.list-goods-name{
	width: 100% !important;
}
.invoice-type .uni-list-cell{
	display: inline-block;
	font-size: 26upx;
	color: #333;
	position: relative;
	margin-left: 50upx;
}
.invoice-type .uni-list-cell>view{
	display: inline-block;
}
.invoice-type-icon{
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
}
.invoice-type-c{
	margin-left: 50upx;
	line-height: 2;
}
.cell-item-ft .cell-bd-input{
	text-align: right;
	width: 500upx;
	font-size: 28upx;
}
.cell-item-bd .cell-bd-input{
	width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
.right-img{
	border-bottom: 0;
}
.cell-textarea{
	padding: 0 26upx 20upx;
	font-size: 26upx;
	color: #333;
	word-wrap: break-word;
}
.evaluate-c-b{
	overflow: hidden;
	padding: 0 20upx;
}
.upload-img{
	width: 146upx;
	height: 146upx;
	margin: 14upx;
	text-align: center;
	color: #999999;
	font-size: 22upx;
	border: 2upx solid #E1E1E1;
	border-radius: 4upx;
	display: inline-block;
	float: left;
	padding: 24upx 0;
}
.goods-img-item{
	width: 174upx;
	height: 174upx;
	padding: 14upx;
	float: left;
	position: relative;
}
.goods-img-item:nth-child(4n){
	margin-right: 0;
}
.goods-img-item image{
	width: 100%;
	height: 100%;
}
.del{
	width: 30upx !important;
	height: 30upx !important;
	position: absolute;
	right: 0;
	top: 0;
	z-index: 999;
}
.black-text .cell-bd-text{
	font-size: 28upx;
}
.button-bottom .btn{
	line-height: 86rpx;
}
.add-title-item .cell-item-bd{
	margin-left: 0;
}
</style>
