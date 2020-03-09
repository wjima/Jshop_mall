<template>
	<view class="content">
		<view class="collection">
			<view class="container_of_slide" v-for="(item, index) in lists" :key="index">
				<view class="slide_list">
					<view class="now-message-info" hover-class="uni-list-cell-hover">
						<view class="icon-circle">
							<image class='goods-img' :src="item.avatar" mode="aspectFill"></image>
						</view>
						<view class="list-right">
							<view class="list-title">昵称: {{ item.nickname }}</view>
							<view class="list-detail color-6">手机号: {{ item.mobile }}</view>
							<view class="list-detail">推荐时间: {{ item.ctime }}</view>
						</view>
					</view>
					<view style="clear:both"></view>
				</view>
			</view>
			<uni-load-more :status="loadStatus"></uni-load-more>
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
			lists: [],
			page: 1, //当前页
			limit: 10, //每页显示几条
			loadStatus: 'more'
		};
	},
	onLoad () {
		this.getDataList();
	},
	onReachBottom () {
		if (this.loadStatus === 'more') {
			this.getDataList()
		}
	},
	methods: {
		getDataList() {
			this.loadStatus = 'loading'
			let data = {
				page: this.page,
				limit: this.limit
			}
			this.$api.recommendList(data, res => {
				if (res.status) {
				    for (let i = 0; i < res.data.length; i++) {
						if (res.data[i].avatar == null) {
							res.data[i].avatar = this.$store.state.config.shop_default_image;
						}
						if (res.data[i].nickname == null) {
							res.data[i].nickname = '暂无昵称'
						}
				    }
					let lists = this.lists.concat(res.data);
					this.lists = lists;
					if (res.total > this.page) {
						this.page++
						this.loadStatus = 'more'
					} else {
						this.loadStatus = 'noMore'
					}
				}else{
					this.$common.errorToShow(res.msg)
				}
			});
		}
	},
};
</script>

<style scoped>
.collection .goods-img{
	width: 150upx;
	height: 150upx;	
}
.container_of_slide {
	width: 100%;
	overflow: hidden;
}
.slide_list {
	transition: all 100ms;
	transition-timing-function: ease-out;
	min-width: 100%;
}
.now-message-info {
	box-sizing:border-box;
	display: flex;
	align-items: center;
	font-size: 16px;
	clear:both;
	padding: 20upx 26upx;
	margin-bottom: 2upx;
	background: #FFFFFF;
	width: 100%;
}
.now-message-info,
.group-btn {
	float: left;
}
.group-btn {
	display: flex;
	flex-direction: row;
	height: 190upx;
	min-width: 100upx;
	align-items: center;

}
.group-btn .btn-div {
	height: 190upx;
	color: #fff;
	text-align: center;
	padding: 0 50upx;
	font-size: 34upx;
	line-height: 190upx;
}
.group-btn .top {
	background-color: #FF7159;
}
.group-btn .removeM {
	background-color: #999;
}
.icon-circle{
	width:150upx;
	height: 150upx;
	float: left;
}
.list-right{
	float: left;
	margin-left: 25upx;
	height: 150upx;
}
.list-right-1{
	float: right;
	color: #A9A9A9;
}
.list-title{
	width: 490upx;
	line-height:1.5;
	overflow:hidden;
	color:#333;
	font-size: 26upx;
	min-height: 60upx;
}
.list-detail{
	width: 460upx;
	font-size: 24upx;
	color: #a9a9a9;
	display:-webkit-box;
	-webkit-box-orient:vertical;
	-webkit-line-clamp:1;
	overflow:hidden;
	height: 50upx;
}
</style>
