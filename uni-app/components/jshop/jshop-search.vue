<template>
	<view class="" >
		<!-- 搜索框 -->
		<view class="search" ref="searchBar" id="search">
			<view class='search-c' @click='goSearch()' v-bind:class="jdata.params.style" >
				<view class='search-input search-input-p'>
					<view class="search-input-p-c">
						{{jdata.params.keywords}}
					</view>
				</view>
				<image class='icon search-icon' src='/static/image/zoom.png'></image>
			</view>
		</view>
		<!-- 搜索框 -->
		<!-- <view class="search search-fixed" v-show="searchFixed">
			<view class='search-c' @click='goSearch()' v-bind:class="jdata.params.style">
				<view class='search-input search-input-p'>
					<view class="search-input-p-c">
						{{jdata.params.keywords}}
					</view>
				</view>
				<image class='icon search-icon' src='/static/image/zoom.png'></image>
			</view>
		</view>	 -->
	</view>
</template>
<script>
	export default {
		name: "jshopsearch",
		props: {
			jdata:{
				// type: Object,
				required: true,
			}
		},
		data() {
			return {
				keyword:'',
				searchTop: 0,
				scrollTop: 0,
				searchFixed: this.$store.state.searchFixed||false
			};
		},
		
		created() {
			//#ifdef H5
			this.$nextTick(() => {
				this.searchTop = this.$refs.searchBar.$el.offsetTop;
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
			goSearch() {
				uni.navigateTo({
					url: '/pages/index/search'
				});
			},
			handleScroll() {
				this.scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
				this.scrollTop >= this.searchTop? this.searchFixed = true : this.searchFixed = false;
			},
		},
		/*
		 //失效
		 onPageScroll(){
			var _this = this;
			// #ifdef MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE
			const query = uni.createSelectorQuery().in(this)
			  query.select('.search').boundingClientRect(function(res){
				if(res.top<0){
					_this.searchFixed = true ;
				}else{
					_this.searchFixed = false;
				}
			  }).exec()
			// #endif
		} */
	}
</script>

<style>
.search-input-p {
	color: #888;
}
.square{
	border-radius: 0;
}
.radius{
	border-radius: 12upx;
}
.search-fixed{
	position: fixed;
	top: 0;
	/* background: linear-gradient(rgba(0, 0, 0, .5), rgba(0, 0, 0, 0)); */
	transition: all .5s;
}
/* .isOpacity {
		background: linear-gradient(rgba(0, 0, 0, .5), rgba(0, 0, 0, 0));
		transition: all .5s;
	}

.isOpacity .search-input {
	background-color: rgba(255, 255, 255, .5);
	transition: all .5s;
} */
</style>
