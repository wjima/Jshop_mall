<template>
	<view class="content">
		<view class='cell-group'>
			<view class='cell-item right-img'>
				<view class='cell-item-hd'>
					<view class='cell-hd-title color-6'>类型筛选</view>
				</view>
				<view class='cell-item-ft'>
					<view class="uni-list">
						<view class="uni-list-cell-db color-6">
							<picker @change="changeState" :value="index" :range="objectType">
								<view class="uni-input">{{objectType[index]}}</view>
							</picker>
						</view>
						<image class='right-img icon' src='/static/image/ic-pull-down.png'></image>
					</view>
				</view>
			</view>
		</view>
		<view class="type-c"
		v-if="list.length"
		>
			<view class="cell-group margin-cell-group"
			v-for="(item, index) in list"
			:key="index"
			>
				<view class="cell-item">
					<view class="cell-item-hd">
						<view class='cell-hd-title'>{{ item.type }}</view>
					</view>
					<view class="cell-item-ft">
						<view class="cell-ft-p color-9">
							{{ item.ctime }}
						</view>
					</view>
				</view>
				<view class="cell-item">
					<view class="cell-item-hd">
						<view class='cell-hd-title color-9'>提现卡号：{{ item.card_number }}</view>
					</view>
					<view class="cell-item-ft red-price">
						{{ item.money }}
					</view>
				</view>
			</view>
			<uni-load-more
			:status="loadStatus"
			></uni-load-more>
		</view>
		<view class="order-none" v-else>
			<image class="cash-none-img" src="/static/image/order.png" mode=""></image>
		</view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'	
export default {
	components: {
		uniLoadMore
	},
    data() {
        return {
			objectType: ['全部', '待审核', '提现成功', '提现失败'],
			index: 0,	// 默认选中的类型	索引
			page: 1,
			limit: 10,
			list: [],
			states: [0, 1, 2, 3], // 不同类型的状态
			loadStatus: 'more'
        }
    },
	onLoad () {
		this.getCash()
	},
	onReachBottom () {
		if (this.loadStatus === 'more') {
			this.getCash()
		}
	},
    methods: {
		// 切换类型
		changeState (e) {
			if (this.index !== e.target.value) {
				this.index = e.target.value;
				this.page = 1
				this.list = []
			}
		},
		// 获取余额明细
		getCash () {
			let data = {
				page: this.page,
				limit: this.limit
			}
			
			if (this.states[this.index]) {
				data.type = this.states[this.index]
			}
			
			this.loadStatus = 'loading'
			
			this.$api.cashList(data, res => {
				if (res.status) {
					if (this.page >= res.total) {
						// 没有数据了
						this.loadStatus = 'noMore'
					} else {
						// 未加载完毕
						this.loadStatus = 'more'
						this.page ++
					}
					this.list = [...this.list, ...res.data]
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		}
    },
	watch: {
		index () {
			this.getCash()
		}
	}
}
</script>

<style>
.uni-list{
	overflow: hidden;
}
.uni-list-cell-db{
	float: left;
	padding-top: 8upx;
	margin-right: 6upx;
	display: inline-block;
}
.uni-list .right-img{
	float: left;
}
.cell-item-bd{
	font-size: 26upx;
}
.type-c .cell-group{
	padding: 10upx 0;
}
.type-c .cell-item{
	border: none;
	min-height: 70upx;
	padding: 0 26upx 0 0;
}
.type-c .cell-item .red-price{
	font-size: 50upx;
}
.type-c .cell-item .color-9{
	font-size: 24upx;
}
.order-none{
	text-align: center;
	padding: 200upx 0;
}
.cash-none-img{
	width: 274upx;
	height: 274upx;
}
</style>
