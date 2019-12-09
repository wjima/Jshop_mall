<template>
	<view class="content">
		<view class='search'>
			<view class='search-c' v-bind:class="$store.state.searchStyle">
				<image class='icon search-icon' src='/static/image/zoom.png'></image>
				<input class='search-input' placeholder-class='search-input-p' placeholder='请输入关键字搜索' v-model="key" focus :auto-focus="focus" :fixed="focus"></input>
			</view>
			<button class="btn btn-g" @click="search" hover-class="btn-hover2">搜索</button>
		</view>
		<view class="history-c" v-show="keys.length > 0">
			<view class="history-title">
				<view class='ht-left'>历史记录</view>
				<view class='ht-right' @click="deleteKey">清除</view>
			</view>
			<view class="history-body">
				<view class="hb-item" v-for="(item, key) in keys" :key="key" @click="toNav(item)">
					{{item|| ''}}
				</view>
			</view>
		</view>
		<view class="history-c" v-show="recommend && recommend.length > 0">
			<view class="history-title">
				<view class='ht-left'>搜索发现</view>
			</view>
			<view class="history-body">
				<view class="hb-item" v-for="(item, key) in recommend" :key="key" @click="toNav(item)">
					{{item|| ''}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			keys: [],
			key: '',
			navType: 'toNav',
			focus: true,
		}
	},
	computed: {
		recommend() {
			return this.$store.state.config.recommend_keys
		}
	},
	methods: {
		//搜索
		search: function () {
			let keys = this.key;
			if(keys != '') {
				let search_key = this.$db.get('search_key');
				if (!search_key) {
					search_key = [];
				}
				let flag = true;
				for (var key in search_key) {
					if (search_key[key] == keys) {
						flag = false;
					}
				}
				if (flag) {
					search_key.unshift(keys);
				}
				this.$db.set('search_key', search_key);
				this.$db.set('search_term', keys);
				this.$common.navigateTo('/pages/classify/index?key=' + keys);
			}
		},

		//清除
		deleteKey: function () {
			//删除显示
			this.keys = [];
			//删除存储
			this.$db.del('search_key');
		},

		//跳转操作
		toNav: function (keys) {
			this.$db.set('search_term', keys);
			let search_key = this.$db.get('search_key');
			if (!search_key) {
				search_key = [];
			}
			var flag = true;
			for (var key in search_key) {
				if (search_key[key] == keys) {
					flag = false;
				}
			}
			if (flag) {
				search_key.unshift(keys);
			}
			this.$db.set('search_key', search_key);
			this.$common.navigateTo('/pages/classify/index?key=' + keys);
		},
	},
	//加载触发
	onShow(e) {
		this.keys = this.$db.get('search_key');
        this.key = this.$db.get('search_term');
		this.focus = true;
	},
	//页面卸载触发
	onUnload() {
	    this.$db.set('search_term', '');
	}
}
</script>

<style>
.search{
	display: flex;
}
.search-c{
	width: 80%;
	margin-right: 2%;
}
.search-input-p{
	padding: 0 !important;
}
.search .btn{
	width: 18%;
	border: none;
	background-color: #f1f1f1;
	font-size: 28upx;
	color: #333;
	border-radius: 6upx;
	line-height: 72upx;
}
.history-c{
	padding: 20upx 26upx;
}
.history-title{
	overflow: hidden;	
}
.ht-left{
	float: left;
	font-size: 28upx;
	color: #333;
}
.ht-right{
	float: right;
	color: #999;
	font-size: 26upx;
}
.history-body{
	overflow: hidden;
	margin-top: 20upx;
	min-height: 200upx;
}
.hb-item{
	display: inline-block;
	float: left;
	background-color: #fff;
	color: #888;
	margin-right: 20upx;
	margin-bottom: 14upx;
	font-size: 26upx;
	padding: 10upx 20upx;
}
.square{
	border-radius: 0;
}
.radius{
	border-radius: 12upx;
}
</style>
