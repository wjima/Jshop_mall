<template>
	<view class="content">
		<view class="cell-group list">
			<view class="flc" v-if="articleType.length != 0">
				<view class="buttonStyle" v-for="item in articleType" :key="item.id" @click="selectType(item.id)">{{ item.type_name }}</view>
			</view>
			<view class="cell-item" v-for="item in list" :key="item.id" @click="articleDetail(item.id)">
				<view class="cell-title-img"><image :src="item.cover" mode="aspectFill"></image></view>
				<view class="cell-item-bd">
					<view class="article-title ">{{ item.title }}</view>
					<view class="article-time">{{ item.ctime }}</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="loadStatus"></uni-load-more>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
export default {
	components: {
		uniLoadMore
	},
	data() {
		return {
			cid: 0, // 文章分类id
			page: 1,
			limit: 10,
			list: [],
			loadStatus: 'more',
			articleType: [],
			typeName: ''
		};
	},
	onLoad(options) {
		this.cid = options.cid;
		this.articleList();
	},
	onReachBottom() {
		if (this.loadStatus === 'more') {
			this.articleList();
		}
	},
	methods: {
		redirect() {
			uni.navigateBack({
				delta: 1
			});
		},
		articleList() {
			let data = {
				page: this.page,
				limit: this.limit,
				type_id: this.cid
			};
			//console.log(this.cid);
			this.loadStatus = 'loading';
			this.$api.articleList(data, res => {
				if (res.status) {
					//console.log(res);
					this.articleType = res.data.article_type;
					uni.setNavigationBarTitle({
						title: res.data.type_name
					});
					const _list = res.data.list;
					this.list = [...this.list, ..._list];
					if (res.data.count > this.list.length) {
						this.loadStatus = 'more';
						this.page++;
					} else {
						// 数据已加载完毕
						this.loadStatus = 'noMore';
					}
				} else {
					// 接口请求出错了
					this.$common.errorToShow(res.msg);
				}
			});
		},
		selectType(id) {
			this.cid = id;
			this.$common.navigateTo('/pages/article/list?cid=' + id);
		},
		// 查看文章详情
		articleDetail(articleId) {
			this.$common.navigateTo('/pages/article/index?id=' + articleId + '&id_type=1');
		}
	}
};
</script>

<style>
.cell-title-img {
	width: 160upx;
	height: 160upx;
}

.cell-title-img image {
	width: 100%;
	height: 100%;
}

.cell-item-bd {
	padding-right: 0;
	vertical-align: top;
	position: relative;
	display: flex;
	flex: 1;
	flex-direction: column;
	align-items: baseline;
}

.article-title {
	font-size: 28upx;
	color: #333;
	width: 100%;
	min-height: 80upx;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 2;
	overflow: hidden;
}

.article-time {
	font-size: 24upx;
	color: #999;
	display: inline-block;
	min-width: 220upx;
	min-height: 32upx;
	/* position: absolute; */
	/* bottom: 0; */
}

.flc {
	justify-content: flex-start;
	flex-wrap: wrap;
	padding: 10rpx 20rpx;
	border-bottom: 2rpx solid #f0f0f0;
}

.buttonStyle {
	margin: 0 15rpx 10rpx 0;
	padding: 6rpx 20rpx;
	border: 1rpx solid #ccc;
	font-size: 24rpx;
	border-radius: 10rpx;
}

.list {
	margin-bottom: 20rpx;
}

.navigationBar {
	position: fixed;
	left: 0;
	top: 0;
	width: 100%;
	height: 44px;
	padding: 7px 3px;
	text-align: center;
	// line-height: 44px;
	z-index: 998;
	color: #000;
	background-color: #fff;
	// position: relative;
	display: flex;
	justify-content: space-between;
}

.headerTitle {
	display: inline-block;
	font-weight: 700;
	font-size: 16px;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	line-height: 30px;
	width: 100%;
}

.navigationIcon {
	display: inline-block;
	// float: left;
	// margin-top: 10rpx;
	// overflow: hidden;
	display: flex;
	font-size: 27px;
	width: 30px;
}

.uni-btn-icon {
	color: #000;
	// font-size: 54rpx;
	// float: left;
	align-items: center;
}
</style>
