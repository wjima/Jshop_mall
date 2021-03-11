<template>
	<view class="content">
		<view class="cont-t">
			<view class="rule" @click="tips()">
				<image src="/static/image/wx/rule.png" mode=""></image>
				兑换规则
			</view>
			<view class="hu">
				<image src="/static/image/wx/hu.png" mode=""></image>
				<view class="word">
					<view class="fsz32">
						未兑换步数
					</view>
					<view class="step-num">
						<text class="fsz-72">{{steps}}</text>步
					</view>
					<view class="">
						可兑换{{points}}积分
					</view>
				</view>
				<button class="change-btn" @click="change" :disabled="steps<rule.min_steps">
					兑换
				</button>
			</view>
		</view>
		<view>
			<view class="cont-b" v-for="(item,index) in logs" :key="index">
				<view class="item">
					<view class="date">
						{{item.ctime}}
					</view>
					<view class="list">
						<view class="list-item">
							<view class="fsz32 color-0">
								积分兑换
							</view>
							<view class="fsz32 color-0">
								-{{ item.steps }}步
							</view>
						</view>
						<view class="list-item item-b">
							<view class="fsz24 color-6">
								兑换天数：{{ item.date_str }}
							</view>
							<view class="fsz24 color-6">
								+{{ item.points }}积分
							</view>
						</view>
					</view>
				</view>
			</view>
			<uni-load-more
			:status="loadStatus"
			></uni-load-more>
		</view>

		<lvv-popup ref="tips" position="center">
			<view class="tips-lvv">
				<view class="lvv-top">
					<view class="title">
						兑换规则
					</view>
				</view>
				<view class="rule-info">
					<view class="rule-title">
						步数兑换积分的规则如下：
					</view>
					<view class="rule">
						1、每日{{ rule.min_steps }}步起可兑换{{ rule.min_jifen }}积分
					</view>
					<view class="rule">
						2、每超出{{ rule.more_steps }}步可兑换{{ rule.more_jifen }}积分
					</view>
					<view class="rule">
						3、每次兑换将兑换所有待兑换的步数
					</view>
					<view class="rule">
						4、兑换的积分可在下单时抵扣
					</view>
				</view>
				<view class="confirm-btn" @click="close('tips')">
					我知道啦
				</view>
			</view>
		</lvv-popup>
		<lvv-popup ref="success" position="center">
			<view class="success-lvv">
				<view class="succ-top">
					<image src="/static/image/wx/smile.png" mode=""></image>
				</view>
				<view class="succ-b">
					<view class="words fsz-48 color-0 fsw-b">
						兑换成功
					</view>
					<view class="words">
						您使用{{ exchange.steps }}步数兑换{{ exchange.points }}积分
					</view>
					<view class="words">
						健康有我，持续运动！
					</view>
					<view class="confirm-btn" @click="close('success')">
						我知道啦
					</view>
				</view>
			</view>
		</lvv-popup>
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
	export default {
		data() {
			return {
				logs: [], //兑换日志
				steps: 0, // 待兑换步数
				points: 0, //可兑换积分
				rule: {},
				loadStatus: 'more',
				page: 1,
				limit: 10,
				exchange: {
					steps: 0,
					points: 0
				}
			}
		},
		onLoad() {
			this.getData();
			this.getLog();
		},
		onPullDownRefresh() {
			// 下拉刷新
			this.page = 1;
			this.limit = 1;
			this.getLog();
		},
		onReachBottom () {
			if (this.loadStatus === 'more') {
				this.getLog()
			}
		},
		methods: {
			tips() {
				this.$refs.tips.show();
			},
			change() {
				if (this.steps > 0) {
					this.$common.modelShow('步数兑换积分', '确认兑换吗?', () => {
						this.$api.werunPoint({}, res => {
							if (res.status) {
								this.exchange.steps = res.data.steps;
								this.exchange.points = res.data.points;
								this.points = 0;
								this.steps = 0;
								this.$refs.success.show();
							} else {
								this.$common.errorToShow(res.msg);
								return;
							}
						})
					})
				} else {
					this.$common.errorToShow('当前无可兑换的步数哦');
				}
				return false
			},
			// 关闭modal弹出框
			close(target) {
				this.$refs[target].close();
			},
			getData() {
				this.$api.werunCollect({}, res => {
					if (res.status) {
						this.steps = res.data.steps;
						this.points = res.data.points;
						this.rule = res.data.setting;
					} else {
						this.$common.errorToShow(res.msg);
					}
				})
			},
			getLog() {
				let data = {
					page: this.page,
					limit: this.limit
				}
				this.loadStatus = 'loading'
				this.$api.werunLog(data, res => {
					if (res.code == '0') {
						this.logs = [...this.logs, ...res.data]
						if (res.data.count > this.logs.length) {
							this.page ++
							this.loadStatus = 'more'
						} else {
							this.loadStatus = 'noMore'
						}

					} else {
						this.$common.errorToShow(res.msg);
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.content {
		.cont-t {
			height: 680rpx;
			width: 100%;
			background-color: #64C9FD;
			padding: 30rpx 20rpx;

			.rule {
				color: #fff;
				font-size: 28rpx;

				image {
					width: 28rpx;
					height: 28rpx;
					vertical-align: middle;
					margin-right: 5rpx;
				}
			}

			.hu {
				text-align: center;
				color: #fff;
				position: relative;

				image {
					width: 480rpx;
					height: 392rpx;
					margin: auto;
				}

				.word {
					position: absolute;
					top: 90rpx;
					left: 50%;
					transform: translateX(-50%);

					.step-num {
						margin: 30rpx 0 10rpx;
					}
				}

				.change-btn {
					width: 240rpx;
					height: 80rpx;
					line-height: 80rpx;
					text-align: center;
					border-radius: 100rpx;
					background-color: #036EB7;
					position: absolute;
					bottom: -30rpx;
					left: 50%;
					transform: translateX(-50%);
					color: #fff;
				}
			}
		}

		.cont-b {
			.item {
				.date {
					padding: 30rpx 20rpx;
					color: #666;
					font-size: 28rpx;
				}

				.list {
					padding: 38rpx 20rpx;
					background-color: #fff;

					.list-item {
						display: flex;
						justify-content: space-between;

						&:last-child {
							margin-top: 10rpx;
						}
					}
				}
			}
		}
	}

	.tips-lvv {
		width: 600rpx;
		background-color: #fff;
		height: 660rpx;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		box-shadow: 0px 0px 12rpx rgba(0, 0, 0, 0.16);
		border-radius: 16rpx;

		.title {
			height: 90rpx;
			line-height: 90rpx;
			text-align: center;
			font-size: 32rpx;
			color: #000;
			font-weight: 700;
		}

		.rule-info {
			padding: 28rpx;

			.rule-title {
				margin-bottom: 28rpx;
				font-size: 28rpx;
				color: #000;
			}

			.rule {
				font-size: 28rpx;
				color: #000;
				margin-bottom: 28rpx;
			}
		}

		.confirm-btn {
			width: 560rpx;
			height: 92rpx;
			line-height: 92rpx;
			text-align: center;
			background-color: #64C9FD;
			color: #fff;
			font-size: 28rpx;
			border-radius: 16rpx;
			margin: 25rpx auto 0;
		}
	}

	.success-lvv {
		width: 600rpx;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);

		.succ-top {
			width: 100%;
			height: 364rpx;
			background-color: #64C9FD;
			border-radius: 16rpx 16rpx 0px 0px;
			display: flex;
			align-items: center;
			justify-content: center;

			image {
				width: 160rpx;
				height: 160rpx;
			}
		}

		.succ-b {
			width: 100%;
			height: 364rpx;
			background-color: #fff;
			border-radius: 0px 0px 16rpx 16rpx;
			text-align: center;
			padding: 30rpx 0;

			.words {
				margin-top: 10rpx;
				color: #999;
				font-size: 28rpx;
			}
		}

		.confirm-btn {
			width: 560rpx;
			height: 92rpx;
			line-height: 92rpx;
			text-align: center;
			background-color: #64C9FD;
			color: #fff;
			font-size: 28rpx;
			border-radius: 16rpx;
			margin: 40rpx auto 0;
		}
	}

	.fsz-72 {
		font-size: 72rpx;
	}

	.color-0 {
		color: #000 !important;
	}

	.fsz-48 {
		font-size: 48rpx !important;
	}

	.fsw-b {
		font-weight: bold !important;
	}
	button[disabled]{
		color: rgba(0,0,0,.3)!important;
		background-color: #f7f7f7!important;
	}
</style>
