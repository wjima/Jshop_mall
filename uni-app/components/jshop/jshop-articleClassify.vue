<template>
	<view class='index-article cell-group bottom-cell-group' v-if="jdata.params.list && count">
		<view class='cell-item'
		v-for="item in jdata.params.list"
		:key="item.id"
		@click="articleDetail(item.id)"
		>
			<view class="cell-item-bd">
				<view class="article-title ">
					{{ item.title }}
				</view>
				<view class="article-time">
					{{ item.ctime }}
				</view>
			</view>
		
			<view class="cell-title-img">
				<image :src="item.cover" mode="aspectFill"></image>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	name: "jshoparticleclassify",
	props: {
		jdata:{
			// type: Array,
			required: true,
		}
	},
	computed: {
		count() {  
			return (this.jdata.params.list.length > 0)
		}
	},
	methods: {
		// 查看文章详情
		articleDetail (articleId) {
			this.$common.navigateTo('/pages/article/index?id=' + articleId+'&id_type=1')
		}
	}
}
</script>

<style>
.index-article .cell-title-img{
	width: 160upx;
	height: 160upx;
	float: right;
}
.index-article .cell-title-img image{
	width: 100%;
	height: 100%;
}
.index-article .cell-item-bd{
	padding-right: 40rpx;
	vertical-align: top;
	position: relative;
	display: flex;
	flex: 1;
	flex-direction: column;
	align-items: baseline;
}
.index-article .article-title{
	font-size: 28upx;
	color: #333;
	width: 100%;
	min-height: 80upx;
	display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}
.index-article .article-time{
	font-size: 24upx;
	color: #999;
	display: inline-block;
	min-width: 220upx;
	min-height: 32upx;
	/* position: absolute; */
	/* bottom: 0; */
}

</style>
