<template>
	<view class="content">
		<view class="map-body">
			<cover-view></cover-view>
			<map id="storeMap" :latitude="latitude" :longitude="longitude" :markers="covers"  style="width: 100%;height: 100%;"></map>
		</view>
		<scroll-view class="store-list" scroll-y>
			<view class="cell-item add-title-item" v-for="(item, index) in storeList" :key="index" @click="goMarkers(item.id)">
				<view class="cell-item-hd"><image class="store-img" :src="item.logo"></image></view>
				<view class="cell-item-bd">
					<view class="cell-bd-view">
						<text class="cell-bd-text fsz30">{{ item.store_name }}</text>
					</view>
					<view class="cell-bd-view">
						<text class="cell-bd-text color-6 fsz24">电话：{{ item.mobile }}</text>
					</view>
					<view class="cell-bd-view">
						<text class="cell-bd-text color-6 fsz24">地址：{{ item.all_address }}</text>
					</view>
				</view>
				<view class="cell-item-ft"><image class="cell-ft-next icon" src="/static/image/right.png"></image></view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			storeList: [],
			longitude: 0,
			latitude: 0,
			covers: [{
				'longitude': 0,
				'latitude': 0
			}]
		};
	},
	onLoad() {
		this.getMyLocation();
		this.getStoreList();
	},
	methods: {
		// 获取自己的位置信息
		getMyLocation() {
			let _this = this;
			uni.getLocation({
			    type: 'wgs84',
			    success: function (res) {
					_this.longitude = res.longitude;
					_this.latitude = res.latitude;
			    },
				fail: function () {
					_this.$common.errorToShow("获取位置信息失败")
				}
			});

		},
		// 获取店铺列表信息
		getStoreList() {
			let _this = this;
			_this.$api.storeList({}, res => {
				if (res.status) {
					_this.storeList = res.data;
					let storeList = res.data;
					let covers = [];
					for (let i=0; i < storeList.length; i++) {
						let newArr = {}
						newArr.latitude = storeList[i].latitude;
						newArr.longitude = storeList[i].longitude;
						newArr.width = '50rpx';
						newArr.height = '50rpx';
						newArr.iconPath = '/static/image/gps-blue.png'
						covers.push(newArr)
						
					}
					//console.log(covers)
					_this.covers = covers;
				} else {
				}
				// console.log(res)
			});
		}
	}
};
</script>

<style scoped>
.content {
	width: 100%;
	/* #ifdef H5 */
	height: calc(100vh - 44px);
	/* #endif */
}
.map-body {
	width: 100%;
	height: 700rpx;
	position: relative;
}
.store-list {
	background-color: #fff;
	height: calc(100vh - 44px - 700rpx);
}
.store-item {
	display: flex;
}
.store-img {
	width: 140rpx;
	height: 140rpx;
}
.store-right {
	flex: 1;
}
</style>
