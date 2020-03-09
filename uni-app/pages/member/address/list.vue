<template>
	<view class="content">
		<view class="content-top" v-if="list.length">
			<view class="uni-list-cell uni-list-cell-pd" v-for="(item, key) in list" :key="key">
				<view class='cell-group min-cell-group'>
					<view class='cell-item' @click="isSelect(item)">
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>{{item.name}} <text class="phone-num">{{item.mobile}}</text></view>
						</view>
						<view class='cell-item-ft' v-show="type != 'order'">
							<image class='cell-ft-next icon' src='/static/image/compile.png' @click="toEdit(item.id)"></image>
							<text class="cell-ft-text"></text>
						</view>
					</view>
					<view class='cell-item' @click="isSelect(item)">
						<view class='cell-item-hd'>
							<view class="cell-bd-view">
								<view class="cell-tip" v-show="item.is_def === 1">默认</view>
								<text class="cell-bd-text">{{item.area_name + item.address}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="address-none" v-else>
			<image class="address-none-img" src="/static/image/order.png" mode=""></image>
		</view>
		<view class="button-bottom">
			<!-- #ifdef MP-WEIXIN -->
			<button class="btn btn-square btn-b" @click="wechatAddress" hover-class="btn-hover2">从微信获取</button>
			<!-- #endif -->
			<button class="btn btn-square btn-w" @click="toAdd()" hover-class="btn-hover2">新增收货地址</button>
		</view>
	</view>
</template>

<script>
export default {
    data() {
        return {
			list: [] ,// 用户收货地址列表
			type: ''
        }
    },
	onLoad (e) {
		if(e.type){
			this.type = e.type;
		}
	},
	onShow () {
		this.userShipList();
	},
    methods: {
		// 获取收货地址列表
		userShipList () {
			this.$api.userShip({}, res => {
                if (res.status) {
                    this.list = res.data
                }
            })
		},
		// 收货地址删除
		delShip (id) {
			this.$common.modelShow('提示', '确认删除此收货地址?', () => {
				let data = {
					id: id
				}
				this.$api.removeShip(data, res => {
					if (res.status) {
						this.$common.successToShow(res.msg, () => {
							this.userShipList();
						});
					} else {
						this.$common.errorToShow(res.msg);
					}
				})
			})
		},
		//编辑
		toEdit (id) {
			this.$common.navigateTo('./index?ship_id=' + id);
		},
		//添加
		toAdd() {
			this.$common.navigateTo('./index');
		},
		//选择
		isSelect(data) {
			if(this.type == 'order'){
				let pages = getCurrentPages();//当前页
				let beforePage = pages[pages.length - 2];//上个页面
				
				// #ifdef MP-ALIPAY || MP-TOUTIAO
				this.$db.set('address_user_ship', data, true);
				// #endif
		
				// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE
				this.$store.commit("userShip",data)
				// #endif

				// #ifdef MP-WEIXIN
				beforePage.$vm.userShip = data;
				beforePage.$vm.params.area_id = data.area_id;
				// #endif
				
				uni.navigateBack({
					delta: 1
				});
				// this.$common.navigateTo("/pages/goods/place-order/index")
			}
		},
		// #ifdef MP-WEIXIN
		wechatAddress: function () {
			wx.chooseAddress({
				success: res => {
					if (res.errMsg == "chooseAddress:ok") {
						//获取成功
						//存储这个收获地区信息到数据库
						let data = {
							province_name: res.provinceName,
							city_name: res.cityName,
							county_name: res.countyName,
							postal_code: res.postalCode
						};
						let areaId = 0;
						this.$api.getAreaId(data, res1 => {
							if (res1.status) {
								//存储用户收货信息
								let userShipId = 0;
								let userShipData = {
									area_id: res1.data,
									user_name: res.userName,
									detail_info: res.detailInfo,
									tel_number: res.telNumber,
									is_def: 2
								}
								this.$api.saveUserShipWx(userShipData, res2 => {
									if (res2.status) {
										this.$common.errorToShow('存储微信地址成功', r => {
											setTimeout(rp => {
												this.userShipList();
											}, 1000);
										});
									}else{
										uni.showModal({
											title: '提示',
											content: '存储微信地址失败',
											showCancel: false
										});
									}
								});
							}else{
								uni.showModal({
									title: '提示',
									content: '获取微信地址失败',
									showCancel: false
								});
							}
						});
					} else {
						uni.showModal({
							title: '提示',
							content: '获取微信地址失败',
							showCancel: false
						});
					}
				}
			});
		},
		// #endif
    }
}
</script>

<style>
.cell-tip{
	background-color: #FF7159;
	color: #fff;
	font-size: 24upx;
	display: inline-block;
	float: left;
	padding: 4upx 10upx;
	margin-right: 10upx;
	transform: scale(.9);
}
.min-cell-group .cell-ft-text{
	font-size: 24upx;
	margin-right: 10upx;
}
.min-cell-group .cell-item-bd{
	color: #666;
	padding-right: 0;
}
.min-cell-group .default{
	color: #666;
}
.min-cell-group uni-radio .uni-radio-input{
	width: 36upx;
	height: 36upx;
}
.min-cell-group .default .checked-radio{
	display: inline-block;
	float: left;
	position: relative;
	bottom: 2upx;
}
.green{
	background-color: #999;
}
.cell-hd-title{
	font-size: 28upx;
}
.phone-num{
	margin-left: 20upx;
	color: #999;
	font-size: 24upx;
}
.address-none{
	text-align: center;
	padding: 200upx 0;
}
.address-none-img{
	width: 274upx;
	height: 274upx;
}
</style>