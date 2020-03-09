<template>
	<view class="content">
		<view class="content-top">
			
			<!-- 我的银行卡信息 -->
			<view class='cell-group margin-cell-group' 
			v-if="userbankCard"
			@click="toBankCardList"
			>
				<view class='cell-item right-img cell-item-mid'>
					<view class='cell-item-hd'>
						<image class="yl-logo" :src="cardInfo.bank_logo" mode=""></image>
					</view>
					<view class='cell-item-bd'>
						<text class="cell-bd-view">{{ cardInfo.card_number || ''}}</text>
					</view>
					<view class="cell-item-ft">
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group' 
			v-else
			@click="toBankCardList"
			>
				<view class='cell-item right-img cell-item-mid'>
					<view class='cell-item-hd'>
						<image class="yl-logo" src="/static/image/yl.png" mode=""></image>
					</view>
					<view class='cell-item-bd'>
						<text class="cell-bd-view">请添加银行卡</text>
					</view>
					<view class="cell-item-ft">
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
			</view>
			
			<!-- 提现金额手续费 提现金额input -->
			<view class='cell-group margin-cell-group'>
				<view class='cell-item'>
					<view class='cell-item-bd' v-if="tocashExplain">
						<view class='cell-hd-title' style="color: #666;">{{ tocashExplain || ''}}</view>
					</view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-bd withdrawcash-input'>
						<text>￥</text><input type="number" focus v-model="money"/>
					</view>
				</view>
				<view class='cell-item'>
					<view class='cell-item-bd'>
						<view class='cell-hd-title' style="color: #666;" v-show="!isError">可用余额 {{ user.balance || ''}} 元</view>
						<view class='cell-hd-title' style="color: #f00;" v-show="isError">提现金额超过可用余额</view>
					</view>
				</view>
			</view>
			
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-b" hover-class="btn-hover2" v-if="isSubmit" @click="toCash" :disabled='submitStatus' :loading='submitStatus'>确认提现</button>
			<button class="btn btn-square btn-b" hover-class="btn-hover2" v-else-if="!isSubmit" disabled>确认提现</button>
		</view>
	</view>
</template>

<script>
export default {
	data () {
		return {
			cardInfo: {}, // 我的银行卡信息
			user: {}, // 用户信息
			isError: false, // 当提现金额大于可用余额 显示错误提示
            isSubmit: false, // 提现点击
			money: '', // 用户输入的提现金额
			submitStatus: false
		}
	},
	onLoad () {
		this.userBankCard()
		this.userInfo()
	},
	onShow() {
		// #ifdef MP-ALIPAY || MP-TOUTIAO
		let user_card_info = this.$db.get('user_card_info', true);
		if (user_card_info) {
			this.cardInfo = user_card_info;
			this.$db.del('user_card_info', true);
		}
		// #endif
	},
	computed: {
		userbankCard () {
			if (Object.keys(this.cardInfo).length) {
				return true
			} else {
				return false
			}
		},
		// 店铺提现手续费
		tocashMoneyRate () {
			return this.$store.state.config.tocash_money_rate
		},
		// 店铺提现最低金额
		tocashMoneyLow () {
			return this.$store.state.config.tocash_money_low
		},
		// 提现文字说明
		tocashExplain () {
			if (this.tocashMoneyRate && this.tocashMoneyLow) {
				return '最低提现金额 ' + this.tocashMoneyLow + ' 元（收取 ' + this.tocashMoneyRate + ' %服务费）'
			} else if (this.tocashMoneyLow) {
				return '最低提现金额 ' + this.tocashMoneyLow + ' 元'
			} else if (this.tocashMoneyRate) {
				return '收取 ' + this.tocashMoneyRate + ' %服务费'
			} else {
				return ''
			}
		}
	},
	methods: {
		// 获取我的默认银行卡信息
		userBankCard () {
			this.$api.getDefaultBankCard({}, res => {
				if (res.status) {
					this.cardInfo = res.data
				}
			})
		},
		// 获取用户信息
		userInfo () {
			// 获取我的余额信息
			// 获取用户的可用余额
			this.$api.userInfo({}, res => {
				this.user = res.data
			})
		},
		// 去提现
		toCash () {
			if (!Object.keys(this.cardInfo).length) {
				this.$common.errorToShow('请选择要提现的银行卡')
				return false
            } else if (!this.money) {
				this.$common.errorToShow('请输入要提现的金额')
				return false
            } else if (Number(this.money) === 0) {
				this.$common.errorToShow('提现金额不能为0')
            } else {
				this.submitStatus = true;
                this.$api.userToCash({
                    money: this.money,
                    cardId: this.cardInfo.id
                }, res => {
                    if (res.status) {
						this.$common.successToShow(res.msg, () => {
							// this.submitStatus = false;
							uni.navigateBack({
								delta: 1
							});
						})
                    } else {
						this.$common.errorToShow(res.msg);
						// this.submitStatus = false;
					}
                },res => {
					this.submitStatus = false;
				})
            }
		},
		// 跳转我的银行卡列表
		toBankCardList () {
			this.$common.navigateTo('./bankcard?mold=select')
		}
	},
	watch: {
		money () {
			// 比较用户的输入金额 如果大于可用金额
			if (this.money === '' || Number(this.money) <= 0) {
				this.isSubmit = false
			} else if (Number(this.money) > Number(this.user.balance)) {
				this.isError = true
				this.isSubmit = false
			} else if (Number(this.money) < Number(this.tocashMoneyLow)) {
				this.isError = false
				this.isSubmit = false
			} else {
				this.isError = false
				this.isSubmit = true
			}
		}
	}
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
.cell-item{
	border: none;
}
.cell-item-bd{
	color: #666;
	font-size: 26upx;
}
.button-bottom .btn {
	width: 100%;
}
.yl-logo{
	width: 188upx;
	height: 54upx;
	float: left;
}
.withdrawcash-input{
	display: flex;
	align-items: center;
	font-size: 50upx;
	border-bottom: 2upx solid #e8e8e8;
	padding-bottom: 20upx;
	width: 95%;
	max-width: 95%;
}
.withdrawcash-input text{
	font-size: 40upx;
}
.withdrawcash-input input{
	display: inline-block;
	min-width: 500upx;
	padding-left: 20upx;
}
/* #ifdef MP-ALIPAY */
.cell-hd-title input {
	font-size: 24px;
	height: 18px;
}
/* #endif */
</style>
