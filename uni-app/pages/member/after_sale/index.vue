<template>
	<view class="content">
		<view class='cell-group margin-cell-group cell-header' style="margin-left: 0;padding-left: 20rpx;">
			请选择退货商品和数量
		</view>
		<form @submit="submit" report-submit='true'>
			<view class="content-top">
				<view class="img-list cart-list">
					<checkbox-group class="cart-checkbox" v-for="(item, key) in items" :key="key" @change="checkboxChange" :data-idx="key" :data-itemid="item.id">
						<view class="cart-checkbox-item">
							<label class="uni-list-cell uni-list-cell-pd">
								<view class="cart-checkbox-c">
									<checkbox :value='item.id' :checked="item.checked" color="#FF7159" v-if="item.disabled" :disabled="item.disabled"
									 class="checkboxNo" />
									<checkbox :value='item.id' :checked="item.checked" color="#FF7159" v-else />
								</view>
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
													购买数:{{item.nums}}
												</view>
												<view class="goods-salesvolume mr5">
													发货数量:{{item.sendnums}}
												</view>
												<view class="goods-salesvolume mr5" v-show="item.reship_nums!=0">
													已退数量:{{item.reship_nums}}
												</view>
												<view class="goods-salesvolume" v-if="!item.disabled">
													<label>可退数：</label>
													<input type="number" v-model="item.returnNums" @focus="onFocus(item,key)" @blur="updateNum(item,key)"
													 class="inputStyle" ref="input" @click.stop />
												</view>
											</view>
										</view>
									</view>
								</view>
							</label>
						</view>
					</checkbox-group>
				</view>
				<view class='cell-group margin-cell-group'>
					<view class='cell-item'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>是否发货</view>
						</view>
						<view class='cell-item-ft'>
							<view class="uni-form-item uni-column invoice-type">
								<!-- #ifndef MP-ALIPAY -->
								<radio-group class="uni-list" @change="radioChange">
									<label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in type_list" :key="index">
										<view class="invoice-type-icon">
											<radio class="a-radio radioNo" v-if="item.disabled" :id="item.name" :value="item.value" :checked="item.checked"
											 :disabled="item.disabled"></radio>
											<radio class="a-radio " v-else :id="item.name" :value="item.value" :checked="item.checked" :disabled="item.disabled"></radio>
										</view>
										<view class="invoice-type-c">
											<label class="label-2-text" :for="item.name">
												<text>{{item.name}}</text>
											</label>
										</view>
									</label>
								</radio-group>
								<!-- #endif -->
								<!-- #ifdef MP-ALIPAY -->
								<jhlable></jhlable>
								<!-- #endif -->
							</view>
						</view>
					</view>
					<view class='cell-item refund-price'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>退款金额</view>
						</view>
						<view class='cell-item-ft'>
							<input type="digit" class='cell-bd-input red-price' v-model="refund" @focus="refundFocus" ref="refund"></input>
						</view>

					</view>
					<view class="color-9 fsz24 refund-tip">
						可修改，最多￥{{maxRefund}}，含发货邮费￥{{cost_freight}}
					</view>
				</view>
				<view class='cell-group margin-cell-group'>
					<view class='cell-item right-img'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>上传凭证</view>
						</view>
					</view>
					<view class="">
						<view class="evaluate-c-b">
							<view class="goods-img-item" v-for="(item, key) in images" :key="key">
								<image class="del" src="/static/image/del.png" mode="" @click="delImage(item)"></image>
								<image class="" :src="item.url" mode="" @click="clickImg(item.url)"></image>
							</view>
							<view class="upload-img" v-show="isImage" @click="upImage">
								<image class="icon" src="/static/image/camera.png" mode=""></image>
								<view class="">上传照片</view>
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
					<view class="cell-textarea ">
						<input v-model="reason" placeholder="请您在此描述问题(最多200字)" maxlength="200" />
						</view>
				</view>
			</view>
			<view class="button-bottom" style="z-index: 200;">
				<button class="btn btn-b btn-square" formType="submit" :disabled='submitStatus' :loading='submitStatus'>提交</button>
			</view>
		</form>
	</view>
</template>

<script>
import jhlable from '@/components/jihai-lable.vue'
export default {
    data() {
        return {
			type_list: [
				{ value: '1', name: '未发货', checked: true, disabled: false },
				{ value: '2', name: '已发货', checked: false, disabled:false },
			],
			order_id:'',
			items:[],   //退货明细
			item_ids:[],  //选择的退货
			aftersale_type:1,     //售后类型1退款，2退款退货
			refund:0,   //退款金额，等于已支付的金额减去已退款的金额
			maxRefund:0,//最大可退款金额
			refund_show:0,
			images:[],      //图片
			reason:'',      //原因
			image_max: 5,    //用于前台判断上传图片按钮是否显示
			refund_input_noedit: true,
			mode: 'aspectFill',
			submitStatus: false,
			checkedItems:[],//当前选中的商品
			isFlag: true,
			cost_freight:0//运费
        }
    },
	components: { jhlable },
	computed: {
		isImage() {
			let num = this.image_max - this.images.length;
			if(num > 0) {
				return true;
			}else{
				return false;
			}
		},
	},
    methods: {
		// 单选框点击切换
        radioChange: function(evt) {
			this.type_list.forEach(item => {
				if (item.value === evt.target.value) {
					item.checked = true;
					this.aftersale_type = evt.target.value;
				}else{
					item.checked = false;
				}
			});
			if(this.type_list[0].checked){
				this.refund_input_noedit = true;
			}else{
				this.refund_input_noedit = false;
			}
		},
		
		//订单商品信息
		getOrderInfo() {
			let data = {
				order_id: this.order_id
			}
			this.$api.orderDetail(data, res => {
				if (res.status) {
					//如果不是未支付的，已取消的，已完成的状态，就都可以售后
					if (res.data.text_status != 1 && res.data.text_status != 6 && res.data.text_status != 7){
						//判断是已付款未发货，如果是，就禁用退货
						let type_list = this.type_list;
						if (res.data.text_status == 2){
							type_list[1].disabled = true; 
						}
						
						//设置已选中的商品
						let nums = 0;
						let returnNums={}
						let returnStatus
						for(var i=0;i<res.data.items.length;i++){
							 if(res.data.items[i].nums >= res.data.items[i].reship_nums){
							  returnNums = res.data.items[i].nums - res.data.items[i].reship_nums;
							 }
							 if(returnNums>0){
								 returnStatus=true
							 }
							res.data.items[i].id = res.data.items[i].id.toString();
							//this.item_ids = this.item_ids.concat({ id: res.data.items[i].id, nums: returnNums });
							res.data.items[i].returnNums=returnNums			
							res.data.items[i].returnStatus=returnStatus	
							res.data.items[i].checked = false;
							if(res.data.items[i].returnNums>0){
								res.data.items[i].disabled = false;
							}else{
								res.data.items[i].disabled = true;
							}
						}		
						this.items = res.data.items;
	
						this.refund = this.$common.moneySum((res.data.order_amount - res.data.refunded),0);
						//this.refund = this.$common.formatMoney((res.data.order_amount - res.data.refunded), 2, '');
						this.maxRefund = this.$common.formatMoney((res.data.order_amount - res.data.refunded), 2, '');
						this.cost_freight = res.data.cost_freight;//运费
						this.refund_show = res.data.payed - res.data.refunded;
						this.type_list = type_list;
					}else{
						this.$common.errorToBack('订单不可以进行售后');
					}
				} else {
					this.$common.errorToBack('没有找到此订单');
				}
			});
		},
		
		//退货商品选择
		checkboxChange (e) {
			let id = e.target.dataset.itemid
			let idx = this.checkedItems.findIndex(item => item == id)
			if(idx >=0) {
				this.checkedItems.splice(idx, 1)
			} else {
				this.checkedItems.push(id);
			}
			this.getReturnData();
			// return 
			// this.checkedItems.push(v);
			// console.log(e)
			// return
			// let nums = 0;
			// this.item_ids = [];
			// for (var i = 0; i < e.detail.value.length; i++) {
			// 	let k = e.detail.value[i];
			// 	for(var j = 0; j < this.items.length; j++){
			// 		if(this.items[j].id == k) {
			// 			if(this.items[j].nums > this.items[j].reship_nums) {
			// 				// nums = this.items[j].sendnums - this.items[j].reship_nums;
			// 				nums=this.$refs.input[i].value
			// 				this.item_ids = this.item_ids.concat({ id: k, nums: nums });
			// 				console.log(this.item_ids)
			// 			}
			// 		}
			// 	}
			// }

			
			// let _this = this;
			// let id = e;
			// let cartData = _this.items;
			// for (let key in cartData) {
			// 	if (cartData[key].id == id) {
			// 		if (cartData[key].checked == true) {
			// 			cartData[key].checked = false;
			// 		} else {
			// 			cartData[key].checked = true;
			// 		}
			// 	}
			// }
			// _this.checkedItems = cartData;
			// console.log(cartData)
			// this.getReturnData();
		},
		// 点击输入框的事件
		onFocus(item,key){
			item.returnNums = '';
			if(this.checkedItems.indexOf(item.id)==-1){
				this.checkedItems.push(item.id)
			}
			this.items[key].checked = true;
			this.getReturnData();
		},
		//处理退款金额光标事件
		refundFocus(e){
			this.refund = '';
		},
		
		//数量改变事件
		updateNum(updateNum,key){
			let nums = 0;
			nums = this.items[key].nums - this.items[key].reship_nums;
			if(nums<updateNum.returnNums){
				this.isFlag = false;
				this.items[key].returnNums = nums;
				this.$common.errorToShow("您填写的数量不对！")
				//return false;
			}
			
			if( updateNum.returnNums=='' ){
				this.items[key].returnNums = nums;
			}
			this.isFlag = true;
			this.items[key].returnNums = updateNum.returnNums;
			this.getReturnData();
	
		},
		
		//计算要退货的商品数量
		getReturnData(){
			let nums = 0;
			this.item_ids = [];
			for (var i = 0; i < this.checkedItems.length; i++) {
				let k = this.checkedItems[i];
				for(var j = 0; j < this.items.length; j++){
					if(this.items[j].id == k) {
						if(this.items[j].nums >= this.items[j].reship_nums) {
							
							nums = this.items[j].nums - this.items[j].reship_nums;
							if (nums>=this.items[j].returnNums) {
								nums = this.items[j].returnNums
								this.item_ids = this.item_ids.concat({ id: k, nums: nums });
							} else {
								this.$common.errorToShow("您填写的数量不对！")
								return ;
							}
						}
					}
				}
			}
		},

		//提交
		submit(e) {
			this.submitStatus = true;
			let images = [];
			for(var i = 0; i<this.images.length; i++) {
				images = images.concat(this.images[i].image_id);
			}

			//判断退款金额
			// let reg = /^[0-9]+(.[0-9]{1,2})?$/;
			// if (!reg.test(this.refund)) {
			// 	this.$common.errorToShow('请输入正确金额');
			// 	this.submitStatus = false;
			// 	return false;
			// } else {
			// 	if (this.refund > this.refund_show) {
			// 		this.$common.errorToShow('退款金额过大');
			// 		this.submitStatus = false;
			// 		return false;
			// 	} 
			// }
			if(!this.isFlag) {
				this.$common.errorToShow('您填写的数量不对！');
				this.submitStatus = false;
				return false;
			}
			 console.log(this.item_ids)
			if(this.item_ids.length<=0){
				this.$common.errorToShow('请选择要售后的商品');
				this.submitStatus = false;
				return false;
			}
			//组装数据，提交数据
			let data = {
				order_id:this.order_id,
				type: this.aftersale_type,
				items:this.item_ids,
				images:images,
				refund: this.refund,
				reason:this.reason
			};
			// #ifdef MP-WEIXIN
			data['formId'] = e.detail.formId;
			// #endif
			this.$api.addAfterSales(data, res => {
				if(res.status){
					this.$common.successToShow('提交成功', ress => {
						this.$common.navigateTo("/pages/member/order/orderdetail?order_id="+this.order_id)
					});
				}else{
					this.$common.errorToShow(res.msg);
				}
			},res => {
				this.submitStatus = false;
			});
		},
		
		//上传图片
		upImage() {
			let num = this.image_max - this.images.length;
			if(num > 0){
				this.$api.uploadImage(num, res => {
					if(res.status){
						this.images.push(res.data);
						this.$common.successToShow(res.msg);
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			}
		},

		//删除图片
		delImage(e) {
			let newImages = [];
			for(var i = 0; i < this.images.length; i++) {
				if(this.images[i].image_id != e.image_id){
					newImages.push(this.images[i]);
				}
			}
			this.images = newImages;
		},
		// 图片点击放大
		clickImg (img) {
			// 预览图片
			uni.previewImage({
				urls: img.split()
			});
		}
    },
	onLoad(e) {
		this.order_id = e.order_id;
		this.getOrderInfo();
		this.getReturnData()
	}
}
</script>

<style>
.content-top{
	/* #ifdef MP-WEIXIN */
	padding-bottom: 116rpx;
	/* #endif */
}
.list-goods-name{
	font-size: 26rpx;
	width: 100% !important;
}
.cart-checkbox-item{
	position: relative;
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
.right-img{
	border-bottom: 0;
}
.cell-textarea{
	padding: 0 26upx 20upx;
}
.cell-textarea textarea{
	width: 100%;
	height: 200upx;
	font-size: 26upx;
	color: #333;
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
.cell-header{
	padding: 10rpxpx 0;
	margin: 0;
	margin-left: 20rpx;
	font-size: 28rpx;
	font-weight: 550;
}
.mr5{
	margin-right: 10rpx;
}
.inputStyle{
	display: inline-block;
	border: 2rpx solid #ccc;
	height: 13rpx;
	line-height: 13rpx;
	width: 60rpx;
	text-align: center;
	font-size: 24rpx;
	vertical-align: middle;
	margin-bottom: 8rpx;
}
/* #ifdef MP-ALIPAY */

/* #endif */
/* #ifdef MP-TOUTIAO */
.goods-item-c{
	height: 1.5rem;
}
.goods-buy{
	height: 100%;
}
/* #endif */
.refund-price{
	border-bottom: none;
}
.refund-tip{
	padding: 0 26rpx 10rpx;
	text-align: right;
}
</style>
