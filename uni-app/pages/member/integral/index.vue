<template>
	<view class="content">
		<view class="integral-top">
			<view class="integral-top-t">
				可用积分
			</view>
			<view class="integral-top-n">
				{{ pointList.length ? pointList[0].balance : 0}}
			</view>
			<view class="integral-top-d">
				{{ nowDate }}
			</view>
		</view>
		<view class="integral-bottom">
			<view class='cell-group margin-cell-group'>
				<view class='cell-item add-title-item cell-title'>
					<view class='cell-item-bd'>
						<view class="cell-bd-view black-text">
							<text class="cell-bd-text">积分记录</text>
						</view>
					</view>
				</view>
				<view  class='cell-item add-title-item jf-list-item' v-for="item in pointList" :key="item.id" >
					<view class='cell-item-bd'>
						<view class="cell-bd-view black-text">
							<text class="cell-bd-text">{{ item.remarks }}</text>
						</view>
						<view class="cell-bd-view">
							<text class="cell-ft-p">{{ item.num > 0 ? '+' + item.num : item.num }}</text>
						</view>
					</view>
					<view class='cell-item-bd'>		
						<text class="cell-bd-text">{{ item.ctime }}</text>
					</view>
				</view>
				<uni-load-more
				:status="loadStatus"
				:show-icon="true"
				></uni-load-more>
			</view>
		</view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'	
export default {
	data () {
		return {
			page: 1,
			limit: 10,
			pointList: [], // 积分记录
			loadStatus: 'more'
		}
	},
	components: { uniLoadMore },
	onLoad () {
		this.userPointLog()
	},
	computed: {
		nowDate () {
			return this.$common.timeToDate(Math.round(new Date().getTime()/1000))
		}
	},
	methods: {
		userPointLog () {
			let _this = this
			let data = {
				page: _this.page,
				limit: _this.limit
			}
			
			_this.loadStatus = 'loading'
			
			_this.$api.pointLog(data, function (res) {
				if (res.status) {
					_this.pointList = [..._this.pointList, ...res.data]
					// 判断数据是否加载完毕
					if (res.count > _this.pointList.length) {
						_this.page ++
						_this.loadStatus = 'more'
					} else {
						_this.loadStatus = 'noMore'
					}
				} else {
					// 接口請求出錯
					_this.$common.errorToShow(res.msg)
					_this.loadStatus = 'more'
				}
			})
		}
	},
	// 页面滚动到底部触发事件
	onReachBottom () {
		let _this = this
		if (_this.loadStatus === 'more') {
			_this.userPointLog()
		}
	}
}	
</script>

<style lang="scss" scoped>
.jf-list-item {
	display: flex;
	flex-direction: column;
	justify-content: initial;
	align-items: initial;
	.cell-item-bd {
		display: flex;
		justify-content: space-between;
		padding-right: 10rpx;
	}
}
.content{
	background-color: #fff;
	padding-top: 20upx;
}
.integral-top{
	background-color: #F7F7F7;
	text-align: center;
	width: 698upx;
	margin: 0 auto 10upx;
	border-radius: 12upx;
	padding: 40upx 0;
	border: 2upx solid #E9E9E9;
	box-shadow: 0 0 10upx #ddd;
}
.integral-top-t{
	font-size: 28upx;
	color: #666;
	margin-bottom: 16upx;
}
.integral-top-n{
	font-size: 58upx;
	color: #333;
	margin-bottom: 16upx;
}
.integral-top-d{
	font-size: 22upx;
	color: #999;
}
.cell-title .cell-bd-text{
	font-size: 34upx !important;
}
.cell-bd-view{
	font-size: 22upx;
	color: #999;
}
.cell-item .black-text .cell-bd-text{
	font-size: 28upx;
	color: #333;
}
</style>
