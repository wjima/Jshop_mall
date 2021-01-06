<template>
	<view class="">
		<view class="jshop-tabbar bottom-cell-group" ref="tabBar">
			<scroll-view scroll-x='true' class="tabbar-list">
				<view class="tabbar-item" v-for="(item, index) in jdata.params.list" :key="index" @click="showSliderInfo(item.linkType, item.linkValue)">
					{{item.text}}
					<view class="active-tabbar"></view>
				</view>
			</scroll-view>
		</view>
		<!-- <view class="jshop-tabbar bottom-cell-group tabbar-fixed" v-show="tabbarFixed">
			<scroll-view scroll-x='true' class="tabbar-list">
				<view class="tabbar-item" v-for="(item, index) in jdata.params.list" :key="index" @click="showSliderInfo(item.linkType, item.linkValue)">
					{{item.text}}
					<view class="active-tabbar"></view>
				</view>
			</scroll-view>
		</view> -->
	</view>
	
</template>

<script>
export default {
	name: "jshopTabbar",
	props: {
		jdata:{
			// type: Object,
			required: true,
		}
	},
	data() {
		return {
			searchTop: 0,
			scrollTop: 0,
			tabbarFixed: false
		};
	},
	created() {
		//#ifdef H5
		this.$nextTick(() => {
			this.searchTop = this.$refs.tabBar.$el.offsetTop - 52;
		})
		// #endif
		this.searchStyle()
	},

	mounted() {
		// #ifdef H5
		window.addEventListener('scroll', this.handleScroll)
		// #endif
		
			
	},
	methods: {
		searchStyle (){
			this.$store.commit('searchStyle',this.jdata.params.style)
			// console.log(this.data.params.style)
		},
		handleScroll() {
			this.scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
			this.scrollTop >= this.searchTop? this.tabbarFixed = true : this.tabbarFixed = false;
		},
		goClassify(){
			uni.switchTab({
			    url: '/pages/classify/classify'
			});
			// this.$common.navigateTo('/pages/classify/classify')
		},
		showSliderInfo(type, val) {
			console.log(val)
			if (!val) {
				return;
			}
			console.log("11")
			if (type == 1) {
				if (val.indexOf('http') != -1) {
					// #ifdef H5 
					window.location.href = val
					// #endif
					// #ifndef H5
					uni.navigateTo({
						url: '/pages/webview/index.vue?src=' + val
					})
					// #endif
				} else {
					// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE || MP
					if (val == '/pages/index/index' || val == '/pages/classify/classify' || val == '/pages/cart/index/index' || val == '/pages/member/index/index') {
						uni.switchTab({
							url: val
						});
						return;
					} else {
						this.$common.navigateTo(val);
						return;
					}
					// #endif
				}
			} else if (type == 2) {
				// 商品详情
				this.goodsDetail(val)
			} else if (type == 3) {
				// 文章详情
				this.$common.navigateTo('/pages/article/index?id=' + val + '&id_type=1')
			} else if (type == 4) {
				// console.log("11")
				// 文章列表
				this.$common.navigateTo('/pages/article/list?cid=' + val)
			} else if (type == 5) {
				//智能表单 
				this.$common.navigateTo('/pages/form/detail/form?id=' + val)
			}
		},
		//跳转到商品详情页面
		goodsDetail: function(id) {
			let url = '/pages/goods/index/index?id=' + id;
			this.$common.navigateTo(url);
		},
	},
	onPageScroll(){
		var _this = this;
		// #ifdef MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE
		const query = uni.createSelectorQuery().in(this)
		  query.select('.search').boundingClientRect(function(res){
			if(res.top<0){
				_this.tabbarFixed = true ;
			}else{
				_this.tabbarFixed = false;
			}
		  }).exec()
		// #endif
	}
}
</script>

<style>
.tabbar-list{
	padding: 10rpx 0;
	background-color: #fff;
	white-space: nowrap;
	width: 100%;
}
.tabbar-item{
	display: inline-block;
	padding: 10rpx 20rpx;
}
.tabbar-item:first-of-type{
	color: #FF7159;
}
.active-tabbar{
	display: none;
}
.tabbar-item:first-of-type .active-tabbar{
	display: block;
	width: 100%;
	height: 4rpx;
	margin: 10rpx auto 0;
	background-color: #FF7159;
}
.tabbar-fixed{
	position: fixed;
	top: 104rpx;
	transition: all .5s;
	z-index: 999;
	background-color: #fff;
	width: 100%;
}
</style>
