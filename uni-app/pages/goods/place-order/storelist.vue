<template>
	<view class="content">
		<view class='search'>
			<view class='search-c'>
				<image class='icon search-icon' src='/static/image/zoom.png'></image>
				<input class='search-input' placeholder-class='search-input-p' placeholder='请输入门店名' v-model="key"></input>
			</view>
			<button class="btn btn-g" hover-class="btn-hover2" @click="storeSearch">搜索</button>
		</view>
		<view class='cell-group margin-cell-group'>
			<view class='cell-item add-title-item right-img cell-item-mid' v-for="(item, key) in storeList" :key="key" @click="selectStore(item.id, item.store_name, item.mobile, item.all_address)">
				<view class="cell-item-hd">
					<image class='cell-hd-icon' src='/static/image/homepage.png'></image>
				</view>
				<view class='cell-item-bd cell-item-bd-block'>
					<view class="cell-bd-view black-text">
						<text class="cell-bd-text">{{item.store_name|| ''}}</text>
					</view>
					<view class="cell-bd-view">
						<text class="cell-bd-text">电话：{{item.mobile|| ''}}</text>
					</view>
					<view class="cell-bd-view">
						<text class="cell-bd-text">地址：{{item.all_address|| ''}}</text>
					</view>
				</view>
				<view class='cell-item-ft' @click.stop="openLocation(item)">
					<image class='cell-ft-next icon' src='/static/image/location.png'></image>
					<text class="cell-ft-text color-9">{{item.distance|| ''}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data () {
			return {
				storeList: [],
				key: '',
				longitude: '',
				latitude: '',
			}
		},
		onShow () {
			this.getStoreList();
		},
		methods: {
			//打开导航
			openLocation(item){
				if(item.coordinate.length>1){
					uni.openLocation({
						latitude: parseFloat(item.latitude),
						longitude: parseFloat(item.longitude),
						name:item.store_name,
						address:item.all_address,
						success: function () {
							console.log('success');
						}
					});
				}else{
					this.$common.errorToShow("门店信息不全，无法导航")
				}
			},
			//门店搜索
			storeSearch(){
				this.getStoreList();
			},
			//获取门店列表
			getStoreList(){
				let _this = this;
				uni.getLocation({
					type: 'gcj02',
					success: function (res) {
						_this.longitude = res.longitude;
						_this.latitude = res.latitude;
					},
					complete: function (res) {
						let data = {
							'key': _this.key,
							'longitude': _this.longitude,
							'latitude': _this.latitude
						}
						_this.$api.storeList(data, e => {
							_this.storeList = e.data;
						});
					}
				});
			},
			//门店选择
			selectStore(id, name, mobile, address){
				let pages = getCurrentPages()
				let pre = pages[pages.length - 2]
				let store = {id, name,mobile, address};
				// store['id'] = id;
				// store['name'] = name;
				// store['mobile'] = mobile;
				// store['address'] = address;
				this.$store.commit('changeAddress', store);
				uni.navigateBack({
					delta: 1
				});
				return
				
				// #ifdef MP-ALIPAY || MP-TOUTIAO
				this.$db.set('user_store', store, true);
				// #endif

				// #ifdef MP-WEIXIN
				pre.$vm.store = store
				// #endif
				
				// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE
				pre.store = store
				// #endif
				
				uni.navigateBack({
					delta: 1
				});
			}
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
.search-input {
	padding: 10upx;
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
.add-title-item .cell-item-hd {
	min-width: 50upx;
	color: #666;
	font-size: 28upx;
}
.cell-bd-view {
	margin-bottom: 6upx;
}
.cell-bd-view .cell-bd-text{
	font-size: 22upx;
	color: #999;
	max-width: 80%;
}
.black-text .cell-bd-text{
	font-size: 28upx;
	color: #333;	
}
</style>
