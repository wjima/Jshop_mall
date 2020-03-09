<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group'>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>银行卡号</view>
					</view>
					<view class='cell-item-bd'>
						<input type="number" class='cell-bd-input' v-model="cardNumber" focus @blur="checkCard()" placeholder='请输入银行卡号'></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>持卡人</view>
					</view>
					<view class='cell-item-bd'>
						<input type="text" class='cell-bd-input' v-model="name" placeholder='请输入持卡人姓名'></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>银行名称</view>
					</view>
					<view class='cell-item-bd'>
						<input type="text" class='cell-bd-input' :disabled="true" v-model="bankName"></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>银行卡类型</view>
					</view>
					<view class='cell-item-bd'>
						<input type="text" class='cell-bd-input' :disabled="true" v-model='cardTypeName'></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>开户行名</view>
					</view>
					<view class='cell-item-bd'>
						<input type="text" class='cell-bd-input' v-model="accountBank" placeholder='请输入开户银行名'></input>
					</view>
				</view>
				
				<view class='cell-item cell-item-mid right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>开户行地址</view>
					</view>
					<view class='cell-item-bd'>
						<input :value="pickerValue" @focus="showThreePicker"></input>
						<area-picker ref="areaPicker" :areaId="areaId" :defaultIndex="defaultIndex" @onConfirm="onConfirm"></area-picker>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/ic-pull-down.png' @click="showThreePicker"></image>
					</view>
				</view>
				
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>设为默认</view>
					</view>
					<view @click="defaultChange">
						<view class='cell-item-ft'>
							<label class="radio"><radio value="1" :checked="checked" color="#333"/></label>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-b" @click="addCard" hover-class="btn-hover2" :disabled='submitStatus' :loading='submitStatus'>保存</button>
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
			bankName: '',	// 银行名称
			cardType: 1, // 卡类型
			cardTypeName: '', // 卡片类型
			bankCode: '', // 银行缩写码
			accountBank: '', // 开户行
			cardNumber: '', // 银行卡号
			name: '',	// 开户人姓名
			mobile: '',	// 
			region: ['北京市', '北京市', '东城区'],
			areaId: 110101,
			address: '',
			is_def: 2,
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
		// 选择收货地址
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
		// 选择/取消默认
		defaultChange () {
			this.checked = !this.checked
			this.is_def = this.is_def === 1 ? 2 : 1
		},
		//存储收货地址
		saveShip() {
			if(this.id && this.id != 0){
				//编辑存储
				let data = {
					'id': this.id,
					'name': this.name,
					'address': this.address,
					'mobile': this.mobile,
					'is_def': this.is_def
				}
				
				data['area_id'] = this.areaId,
				this.$api.editShip(data, res => {
					if(res.status){
						this.$common.successToShow('编辑成功', function(){
							uni.navigateBack({
								delta: 1
							});
						});
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			}else{
				//添加
				let data = {
					'area_id': this.areaId,
					'name': this.name,
					'address': this.address,
					'mobile': this.mobile,
					'is_def': this.is_def
				}
				this.$api.saveUserShip(data, res => {
					if(res.status){
						this.$common.successToShow('添加成功', function(){
							uni.navigateBack({
								delta: 1
							});
						});
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			}
		},
		// 判断获取银行卡类型
		checkCard () {
			if (this.cardNumber) {
				let data = {
					card_code: this.cardNumber
				}
				this.$api.getBankCardOrganization(data, res => {
					if (res.status) {
						let data = res.data
						this.bankName = data.name
						this.cardType = data.type
						this.bankCode = data.bank_code
						this.cardTypeName = data.type_name
					} else {
						this.$common.errorToShow(res.msg, () => {
							this.bankCode = this.bankName = this.cardType = this.cardTypeName = ''
						})
					}
				})
			} else {
				this.bankCode = this.bankName = this.cardType = this.cardTypeName = ''
			}
		},
		// 添加银行卡
		addCard () {
			if (!this.cardNumber) {
				this.$common.errorToShow('请输入银行卡号')
            } else if (!this.bankName || !this.cardType || !this.bankCode) {
				this.$common.errorToShow('请输入正确的银行卡号')
            } else if (!/^[\u4E00-\u9FA5\uf900-\ufa2d·s]{2,30}$/.test(this.name)) {
				this.$common.errorToShow('请输入正确的持卡人名称')
            } else if (!this.areaId) {
				this.$common.errorToShow('请选择开户行所在地区')
            } else if (!this.accountBank) {
				this.$common.errorToShow('请输入开户银行信息')
            } else {
				this.submitStatus = true;
				let data = {
					bankName: this.bankName,
                    areaId: this.areaId,
                    accountBank: this.accountBank,
                    accountName: this.name,
                    bankCode: this.bankCode,
                    cardNumber: this.cardNumber,
                    cardType: this.cardType,
                    isDefault: this.is_def
				}
				
				this.$api.addBankCard(data, res => {
					if (res.status) {
						this.$common.successToShow(res.msg, ress => {
							// this.submitStatus = false;
							uni.navigateBack({
								delta: 1
							});
						})
					} else {
						this.$common.errorToShow(res.msg);
					}
				},res => {
					this.submitStatus = false;
				})
			}
		},
		// #ifdef MP-ALIPAY
		// alipay bank
		aliPayBank() {
			if(this.cardNumber.length >= 16 && this.cardNumber.length <= 19){
				let data = {
					card_code: this.cardNumber
				}
				this.$api.getBankCardOrganization(data, res => {
					if (res.status) {
						let data = res.data
						this.bankName = data.name
						this.cardType = data.type
						this.bankCode = data.bank_code
						this.cardTypeName = data.type_name
					} else {
						this.$common.errorToShow(res.msg, () => {
							this.bankCode = this.bankName = this.cardType = this.cardTypeName = ''
						});
					}
				})
			} else {
				this.bankCode = this.bankName = this.cardType = this.cardTypeName = ''
			}
		}
		// #endif
	},
	onLoad(e) {
		if(e.ship_id){
			//编辑
			this.id = e.ship_id;
			this.getShipInfo();
		}else{
			//添加
			this.pickerValue = this.region[0]+ " "+ this.region[1]+" "+this.region[2];
		}
	},
	onBackPress() {
		if (this.$refs.areaPicker.pickerShow) {
			this.$refs.areaPicker.closePicker();
			return true;
		}
	},
	// #ifdef MP-ALIPAY
	watch: {
		cardNumber () {
			this.$common.throttle(this.aliPayBank, this, 450);
		}
	},
	// #endif
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
.right-img .cell-item-ft{
	right: 26rpx;
}
/* #ifdef MP-ALIPAY */
input{
	font-size: 24upx;
}
/* #endif */
</style>