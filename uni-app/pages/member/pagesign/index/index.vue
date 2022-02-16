<template>
	<view>
		<view class="dashboard-wrap">
			<view class="circle-wrap">
				<image :src="`${baseUrl}static/images/pagesign/circle.png`" mode=""></image>
				<view class="circle-p circle-t">每日签到</view>
				<view class="circle-p circle-num">+10</view>
				<view class="circle-p circle-btn" @click="sign">{{pageInfo.isSign ? '已签到' : '签到' }}</view>
			</view>
			<view class="help" @click="popToggle">
				<image :src="`${baseUrl}static/images/pagesign/help.png`" mode=""></image>
			</view>
		</view>
		<!-- style="{height:height+'px',padding:jdata.params.margin+'px'}" -->
		
		<view class="logs-sign">
			<view class="logs-item">
				<view class="logs-icon">
					<image :src="`${baseUrl}static/images/pagesign/total1.png`" mode=""></image>
				</view>
				<view class="logs-r">
					<view class="nums n1">{{pageInfo.point || 0}}</view>
					<view class="txt">积分统计</view>
				</view>
			</view>
			<view class="logs-item">
				<view class="logs-icon">
					<image :src="`${baseUrl}static/images/pagesign/total2.png`" mode=""></image>
					<!-- <image src="../../pagesign/static/images/total2.png" mode=""></image> -->
				</view>
				<view class="logs-r">
					<view class="nums n2">{{pageInfo.continuous || 0}}</view>
					<view class="txt">连续签到 </view>
				</view>
			</view>
			<view class="logs-item">
				<view class="logs-icon">
					<image :src="`${baseUrl}static/images/pagesign/total3.png`" mode=""></image>
					<!-- <image src="../../pagesign/static/images/total3.png" mode=""></image> -->
				</view>
				<view class="logs-r">
					<view class="nums n3">{{pageInfo.omission || 0}}</view>
					<view class="txt">最近漏签</view>
				</view>
			</view>
		</view>
		
		<ren-calendar ref='ren' class="calendar" @changePicker="changePicker" :markDays='markDays'   :collapsible="false"></ren-calendar>
		
		<lvv-popup ref="pop">
			<view class="pop-wrap">
				<view class="pop-title">签到规则</view>
				<view class="item" v-for="(item, idx) in pageInfo.rule" :key="idx">{{item}}</view>
			</view>
		</lvv-popup>
	</view>
</template>

<script>
	import {apiBaseUrl} from '@/config/config.js';
	import renCalendar from '@/pages/member/pagesign/components/ren-calendar/ren-calendar.vue'
	export default {
		components: {
			renCalendar
		},
		data() {
			return {
				markDays:[],
				today: '',
				nowToday: '',
				pageInfo: {},
				text: "",
				baseUrl:apiBaseUrl
			}
		},
		onShow() {
			this.$nextTick(() => {
				let today = this.$refs.ren.getToday().ym;
				this.nowToday = today
				this.init()
			})
		},
		methods: { 
			changePicker(v) {
				let today = v.y + '-' + v.m
				if(today == this.nowToday) {
					this.today = ''
				} else {
					this.today = today
				}
				this.init()
			},
			popToggle() {
				this.$refs.pop.show()
			},
			// 签到
			sign() {
				if(this.pageInfo.isSign) {
					this.$common.errorToShow('今天已经签过到了~~~')
					return false
				}
				this.$api.sign({}, res => {
					this.$common.errorToShow(res.msg, () => {
						if(res.status) {
							this.$common.errorToShow('签到成功~~~')
							this.init()
						}
					})
				})
			},
			init() {
				this.$api.getsigninfo({date: this.today}, res => {
					this.pageInfo = res.data
					this.markDays = res.data.signday || []
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.pop-wrap {
		position: fixed;
		width: 80%;
		background: #FFFFFF;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
		border-radius: 15px;
		height: 241px;
		padding: 16px;
		overflow-y: scroll;
		.item {
			padding: 10rpx 0;
		}
		.pop-title {
			text-align: center;
			font-weight: bold;
			font-size: 15px;
			line-height: 33px;
		}
	}
	.dashboard-wrap{
		display: flex;
		align-items: center;
		flex-direction: column;
		height: 400rpx;
		background: url(../static/images/sing-bg.png) no-repeat;
		background-size: 100% 100%;
		margin-top: -2rpx;
		position: relative;
		.circle{
			&-wrap {
				width: 298rpx;
				height: 246rpx;
				position: relative;
				margin-top: 20rpx;
				image {
					width: 100%;
					height: 100%;
				}
			}
			&-p {
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				color: #FFFFFF;
			}
			&-t {
				top: 72rpx;
				font-size: 24rpx;
			}
			&-num {
				top: 116rpx;
				font-size: 60rpx;
				font-weight: bold;
			}
			&-btn {
				top: 210rpx;
				width: 156rpx;
				height: 52rpx;
				line-height: 52rpx;
				background: #FFFFFF;
				box-shadow: 0px 0px 6rpx rgba(0, 0, 0, 0.16);
				border-radius: 26rpx;
				font-size: 28rpx;
				font-weight: bold;
				color: #417FF9;
				text-align: center;
			}
		}
	}
	
	.help {
		position: absolute;
		right: 0;
		bottom: 100rpx;
		width: 92rpx;
		height: 56rpx;
		background:rgba($color: #0D0303, $alpha: .2) ;
		border-radius: 100rpx 0px 0px 100rpx;
		image {
			width: 52rpx;
			height: 52rpx;
			margin-left: 14rpx;
		}
	}
	.logs{
		&-sign {
			position: relative;
			z-index: 9;
			width: 690rpx;
			height: 160rpx;
			background: #FFFFFF;
			border-radius: 24rpx;
			margin: 0 auto;
			margin-top: -80rpx;
			display: flex;
			align-items: center;
		}
		
		&-item {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 33.33%;
		}
		&-icon {
			width: 68rpx;
			height: 68rpx;
			image {
				width: 100%;
				height: 100%;
			}
		}
		&-r {
			margin-left: 12rpx;
			.nums {
				font-size: 32rpx;
				&.n1 {
					color: #FDAD04;
				}
				&.n2 {
					color: #FF4444;
				}
				&.n3 {
					color: #299999;
				}
			}
			.text {
				font-size: 24rpx;
				color: #666464;
			}
		}
	}
	
	.calendar {
		margin: 30rpx;
		border-radius: 20rpx;
		overflow: hidden;
	}
</style>
