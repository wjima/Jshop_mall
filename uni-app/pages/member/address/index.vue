<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group'>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>收货人</view>
					</view>
					<view class='cell-item-bd' style="width: 70%;">
						<input type="text" class='cell-bd-input' placeholder='请填写收货人姓名' v-model="name" ></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>手机号</view>
					</view>
					<view class='cell-item-bd' style="width: 70%;">
						<input type="text" class='cell-bd-input' placeholder='请填写收货人手机号' v-model="mobile" ></input>
					</view>
				</view>
				
				<view class='cell-item cell-item-mid right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>省市区</view>
					</view>

					<view class='cell-item-bd'>
						<input :value="pickerValue" readonly   @focus="showThreePicker"></input>
						<area-picker ref="areaPicker" :areaId="areaId" :defaultIndex="defaultIndex"  @onConfirm="onConfirm" class="fsz26"></area-picker>
					</view>
					
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/ic-pull-down.png' @click="showThreePicker"></image>
					</view>
				</view>
				
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>详细地址</view>
					</view>
					<view class='cell-item-bd'style="width: 70%;">
						<input type="text" class='cell-bd-input' placeholder='请填写收货详细地址' v-model="address"></input>
					</view>
				</view>
				<view class='cell-item' @click="defaultChange">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>设为默认</view>
					</view>
					<view class='cell-item-ft' >
						<label class="radio" ><radio value="1" :checked="checked" color="#FF7159"/></label>
					</view>
				</view>
			</view>
		</view>
		<view class="button-bottom">
			
			<button class="btn btn-square btn-w" @click="delShip" v-if="id && id != 0" hover-class="btn-hover2" :disabled='submitStatus' :loading='submitStatus'>删除</button>
			
			<button class="btn btn-square btn-b" @click="saveShip" hover-class="btn-hover2" :disabled='submitStatus' :loading='submitStatus'>保存</button>
		</view>
	</view>

</template>

<script>
import areaPicker from "@/components/area-picker/areaPicker.vue";
export default {
	components: {
		areaPicker
	},
	data() {
		return {
			id: 0,
			name: '',
			mobile: '',
			region: ['北京市', '北京市', '东城区'],
			areaId: 110101,
			address: '',
			is_def: 2,
			multiArray: [
				[],
				[],
				[]
			],
			multiIndex: [110000, 110100, 110101],
			checked: false,
			pickerValue: '',
			defaultIndex: [0, 0, 0],
			submitStatus: false
		}
	},
	computed: {},
	methods: {
		// 省市区联动初始化
		showThreePicker() {
			this.$refs.areaPicker.showPicker();
		},
		onConfirm(e) {
			let province_name = e[0].name;
			let city_name = e[1].name;
			let county_name = e[2].name;
			this.pickerValue = e[0].name+ " "+ e[1].name+" "+e[2].name
			let data = {
				province_name: province_name,
				city_name: city_name,
				county_name: county_name
			}
			let regionName = [province_name, city_name, county_name];
			this.$api.getAreaId(data, res => {
				if (res.status) {
					this.areaId = res.data
				} else {
					uni.showModal({
						title: '提示',
						content: '地区选择出现问题，请重新选择地区',
						showCancel: false
					});
				}
			});
		},
		// 信息验证
		checkData (data) {
			this.submitStatus = false;
			if (!data.name) {
				this.$common.errorToShow('请输入收货人姓名')
				return false
			} else if (!data.mobile) {
				this.$common.errorToShow('请输入收货人手机号')
				return false
			} else if (data.mobile.length !== 11) {
				this.$common.errorToShow('收货人手机号格式不正确')
				return false
			} else if (!data.area_id) {
				this.$common.errorToShow('请选择地区信息')
				return false
			} else if (!data.address) {
				this.$common.errorToShow('请输入收货地址详细信息')
				return false
			} else {
				return true
			}
		},
		//默认
		defaultChange(){
			if(this.checked){
				this.checked = false;
				this.is_def = 2;
			}else{
				this.checked = true;
				this.is_def = 1;
			}
		},
		//编辑获取收货地址信息
		getShipInfo() {
			let data = {
				'id': this.id
			}
			this.$api.shipDetail(data, res => {
				if(res.status){
					let region = res.data.area_name.split(" ");
					this.name = res.data.name;
					this.mobile = res.data.mobile;
					this.region = region;
					this.areaId = res.data.area_id;
					
					this.pickerValue = this.region[0]+ " "+ this.region[1]+" "+this.region[2]
					this.$refs.areaPicker.init();//初始化插件
					
					this.address = res.data.address;
					this.is_def = res.data.is_def;
					if(res.data.is_def == 1){
						this.checked = true;
					}else{
						this.checked = false;
					}
				}else{
					this.$common.errorToShow('获取收货地址信息出现问题');
					// this.submitStatus = false;
				}
			},res => {
				this.submitStatus = false;
			});
		},
		//删除地址
		delShip() {
			this.submitStatus = true;
			this.$api.removeShip({'id': this.id}, res => {
				if(res.status){
					// console.log(res);
					let user_ship = this.$store.state.userShip;
					// console.log(user_ship);
					if(user_ship.id==this.id){
						let data={}
						this.$store.commit("userShip",data)
					}
					
					this.$common.successToShow(res.msg, ress => {
						// this.submitStatus = false;
						uni.navigateBack({
							delta: 1
						});
					});
				}else{
					this.$common.errorToShow(res.msg);
					// this.submitStatus = false;
				}
			},res => {
				this.submitStatus = false;
			});
		},
		//存储收货地址
		saveShip() {
			this.submitStatus = true;
			let data = {
				name: this.name,
				address: this.address,
				mobile: this.mobile,
				is_def: this.is_def,
				area_id: this.areaId
			}
			
			if(this.id && this.id != 0){
				//编辑存储
				data.id = this.id
				
				if (this.checkData(data)) {
					this.$api.editShip(data, res => {
						if(res.status){
							this.$common.successToShow(res.msg, ress => {
								// this.submitStatus = false;
								uni.navigateBack({
									delta: 1
								});
							});
						}else{
							this.$common.errorToShow(res.msg);
							// this.submitStatus = false;
						}
					},res => {
						this.submitStatus = false;
					});
				}
			}else{
				//添加
				if (this.checkData(data)) {
					this.$api.editShip(data, res => {
						if(res.status){
							this.$common.successToShow(res.msg, ress => {
								// this.submitStatus = false;
								uni.navigateBack({
									delta: 1
								});
							});
						}else{
							this.$common.errorToShow(res.msg);
							// this.submitStatus = false;
						}
					},res => {
						this.submitStatus = false;
					});
				}
			}
		}
	},
	onLoad(e) {
		if(e.ship_id){
			//编辑
			this.id = e.ship_id;
			this.getShipInfo();
		}else{
			//添加
			this.pickerValue = this.region[0]+ " "+ this.region[1]+" "+this.region[2];
			uni.setNavigationBarTitle({
				title: '添加地址'
			});
		}
	},
	onBackPress() {
		if (this.$refs.areaPicker.pickerShow) {
			this.$refs.areaPicker.closePicker();
			return true;
		}
	},

}
</script>

<style>
.user-head{
	height: 100upx;
}
.user-head-img{
	height: 90upx;
	width: 90upx;
	border-radius: 50%;
}
.cell-hd-title{
	color: #333;
}
.cell-item-hd{
	width: 180rpx;
}
.cell-item-bd{
	color: #666;
	font-size: 26upx;
}
.button-bottom .btn {
	width: 50%;
}
.cell-bd-input{
	width: 100%;
}
.right-img .cell-item-ft{
	right: 26rpx;
}
/* #ifdef MP-ALIPAY */
input{
	font-size: 24upx;
}
/* #endif */
</style>