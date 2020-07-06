<template>
	<view class="content">
		<!-- 搜索框 -->
		<view class="search">
			<view class="search-c" @click="goSearch" v-bind:class="$store.state.searchStyle">
				<view class="search-input search-input-p">
					<view class="search-input-p-c">{{ searchKey }}</view>
				</view>
				<image class="icon search-icon" src="/static/image/zoom.png"></image>
			</view>
		</view>

		<!-- 条件筛选 -->
		<view class="screen">
			<view class="screen-item" @click="comprehensive">
				<text class="screen-item-text">综合</text>
				<view class="screen-item-icon">
					<image v-if="searchData.order.key == 'sort' && searchData.order.sort == 'asc'" class="screen-item-icon-img" src="/static/image/bottom-black.png"></image>
					<image v-else class="screen-item-icon-img" src="/static/image/bottom-gray.png"></image>
				</view>
			</view>
			<view class="screen-item" @click="priceSort">
				<text class="screen-item-text">价格</text>
				<view class="screen-item-icon">
					<image v-if="searchData.order.key == 'price' && searchData.order.sort == 'asc'" class="screen-item-icon-img" src="/static/image/top-black.png"></image>
					<image v-else-if="!(searchData.order.key == 'price' && searchData.order.sort == 'asc')" class="screen-item-icon-img" src="/static/image/top-gray.png"></image>
					<image v-if="searchData.order.key == 'price' && searchData.order.sort == 'desc'" class="screen-item-icon-img" src="/static/image/bottom-black.png"></image>
					<image v-if="!(searchData.order.key == 'price' && searchData.order.sort == 'desc')" class="screen-item-icon-img" src="/static/image/bottom-gray.png"></image>
				</view>
			</view>
			<view class="screen-item" @click="salesVolume">
				<text class="screen-item-text">销量</text>
				<view class="screen-item-icon">
					<image v-if="searchData.order.key == 'buy_count' && searchData.order.sort == 'asc'" class="screen-item-icon-img" src="/static/image/top-black.png"></image>
					<image
						v-else-if="!(searchData.order.key == 'buy_count' && searchData.order.sort == 'asc')"
						class="screen-item-icon-img"
						src="/static/image/top-gray.png"
					></image>
					<image v-if="searchData.order.key == 'buy_count' && searchData.order.sort == 'desc'" class="screen-item-icon-img" src="/static/image/bottom-black.png"></image>
					<image
						v-if="!(searchData.order.key == 'buy_count' && searchData.order.sort == 'desc')"
						class="screen-item-icon-img"
						src="/static/image/bottom-gray.png"
					></image>
				</view>
			</view>
			<view class="screen-item">
				<view class="screen-item-icon" style-type="button" :current="current" @click="listGrid">
					<image class="list-grid" src="/static/image/switch-ic-side-2.png" v-if="current == 0"></image>
					<image class="list-grid" src="/static/image/switch-ic-list.png" v-else-if="current == 1"></image>
				</view>
			</view>
			<view class="screen-item screents" v-if="screents" @click="toshow()">
				<text class="screen-item-text">筛选</text>
				<image class="filter-img" src="/static/image/top.png"></image>
			</view>
			<view class="screen-item screents" v-else-if="screentc" @click="toclose()">
				<text class="screen-item-text">筛选</text>
				<image class="filter-img" src="/static/image/bottom.png"></image>
			</view>
		</view>

		<!-- 高级赛选 -->
		<lvv-popup position="top" ref="lvvpopref" style="background: none;">
			<view class="fliter-c">
				<scroll-view scroll-y="true" style="height: 100%;">
					<view class="fliter-item">
						<view class="cell-item right-img">
							<view class="cell-item-hd"><view class="cell-hd-title">价格区间</view></view>
						</view>
						<view class="fliter-i-c">
							<view class="fic-item"><input class="fic-item-input" type="number" v-model="sPrice" /></view>
							<view class="fic-item-line"></view>
							<view class="fic-item"><input class="fic-item-input" type="number" v-model="ePrice" /></view>
						</view>
					</view>
					<view class="fliter-item" v-if="cat_list.length > 0">
						<view class="cell-item right-img">
							<view class="cell-item-hd"><view class="cell-hd-title">分类</view></view>
						</view>
						<view class="fliter-i-c">
							<view v-for="item in cat_list" :key="item.goods_cat_id" v-if="item.goods_cat_id && item.name" @click="selectKey('cat_list', item.goods_cat_id)">
								<view class="fic-item" v-if="!item.isSelect">
									<view class="fic-item-text two-line">{{ item.name }}</view>
								</view>
								<view class="fic-item fic-item-active" v-else-if="item.isSelect">
									<view class="fic-item-text two-line">{{ item.name }}</view>
								</view>
							</view>
						</view>
					</view>
					<view class="fliter-item" v-if="brand_list.length > 0">
						<view class="cell-item right-img">
							<view class="cell-item-hd"><view class="cell-hd-title">品牌</view></view>
						</view>
						<view class="fliter-i-c">
							<view v-for="item in brand_list" :key="item.brand_id" v-if="item.brand_id && item.name" @click="selectKey('brand_list', item.brand_id)">
								<view class="fic-item" v-if="!item.isSelect">
									<view class="fic-item-text two-line">{{ item.name }}</view>
								</view>
								<view class="fic-item fic-item-active" v-else-if="item.isSelect">
									<view class="fic-item-text two-line">{{ item.name }}</view>
								</view>
							</view>
						</view>
					</view>
					<view class="fliter-item" v-if="label_list.length > 0">
						<view class="cell-item right-img">
							<view class="cell-item-hd"><view class="cell-hd-title">标签</view></view>
						</view>
						<view class="fliter-i-c">
							<view v-for="item in label_list" :key="item.id" v-if="item.id && item.name" @click="selectKey('label_list', item.id)">
								<view class="fic-item" v-if="!item.isSelect">
									<view class="fic-item-text two-line">{{ item.name }}</view>
								</view>
								<view class="fic-item fic-item-active" v-else-if="item.isSelect">
									<view class="fic-item-text two-line">{{ item.name }}</view>
								</view>
							</view>
						</view>
					</view>
				</scroll-view>
				<view class="button-bottom">
					<button class="btn btn-square" @click="toclose()">关闭</button>
					<button class="btn btn-b btn-square" @click="filterOk()">确定</button>
				</view>
			</view>
		</lvv-popup>

		<!-- 商品列表 -->
		<scroll-view scroll-y="true" :scroll-into-view="toView" class="scroll-Y" @scrolltolower="lower" enable-back-to-top="true" lower-threshold="45">
			<!-- 表格图片 -->
			<view class="img-grids" v-show="current === 0">
				<view v-if="goodsList.length > 0">
					<view class="img-grids-item" v-for="(item, index) in goodsList" :key="index" @click="goodsDetail(item.id)">
						<image class="img-grids-item-t have-none" :src="item.image_url" mode="aspectFill"></image>
						<view class="img-grids-item-b">
							<view class="goods-name grids-goods-name">{{ item.name }}</view>
							<view class="goods-item-c">
								<view class="goods-price red-price">￥{{ item.price }}</view>
								<image class="goods-cart" src="/static/image/ic-car.png"></image>
							</view>
						</view>
					</view>
				</view>
				<!-- 无数据时默认显示 -->
				<view class="order-none" v-else><image class="order-none-img" src="/static/image/order.png" mode=""></image></view>
			</view>

			<!-- 列表图片 -->
			<view class="img-list" v-show="current === 1">
				<view v-if="goodsList.length > 0">
					<view class="img-list-item" v-for="(item, index) in goodsList" :key="index" @click="goodsDetail(item.id)">
						<image class="img-list-item-l" :src="item.image_url" mode="aspectFill"></image>
						<view class="img-list-item-r">
							<view class="goods-name list-goods-name">{{ item.name }}</view>
							<view class="goods-item-c">
								<view class="goods-price red-price">￥{{ item.price }}</view>
								<view class="goods-buy">
									<view class="goods-salesvolume" v-if="item.comments_count > 0">{{ item.comments_count }}条评论</view>
									<view class="goods-salesvolume" v-else-if="item.comments_count <= 0">暂无评论</view>
									<image class="goods-cart" src="/static/image/ic-car.png"></image>
								</view>
							</view>
						</view>
					</view>
				</view>
				<view class="order-none" v-else><image class="order-none-img" src="/static/image/order.png" mode=""></image></view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
export default {
	data() {
		return {
			current: 0,
			id: '',
			showView: false,
			goodsList: [],
			minPrice: '',
			maxPrice: '',
			ajaxStatus: false,
			loading: true,
			loadingComplete: false,
			nodata: false,
			toView: '',
			searchData: {
				where: {},
				limit: 10,
				page: 1,
				order: {
					key: 'sort',
					sort: 'asc'
				}
			},
			searchKey: '请输入关键字搜索', //关键词
			alllist: true,
			allgrid: false,
			screents: true,
			screentc: false,
			sPrice: '',
			ePrice: '',
			brand_list: [],
			cat_list: [],
			label_list: []
		};
	},
	//加载执行
	onLoad: function(options) {
		var where = {};
		if (options.id) {
			where = {
				cat_id: options.id
			};
		}
		if (options.key) {
			where = {
				search_name: options.key
			};
			this.searchKey = options.key;
		}
		if (options.type) {
			if (options.type == 'hot') {
				where = {
					hot: true
				};
			}
			if (options.type == 'recommend') {
				where = {
					recommend: true
				};
			}
		}
		if (options.cat_id) {
			where.cat_id = options.cat_id;
		}
		if (options.brand_id) {
			where.brand_id = options.brand_id;
		}
		if (options.hot) {
			where.hot = options.hot;
		}
		if (options.recommend) {
			where.recommend = options.recommend;
		}
		if (options.label_id) {
			where.label_id = options.label_id;
		}
		this.setSearchData({
			where: where
		});

		this.getGoods();
	},

	components: { lvvPopup },
	methods: {
		listGrid() {
			if (this.current == 0) {
				this.current = 1;
			} else {
				this.current = 0;
			}
		},
		//设置查询条件
		setSearchData: function(searchData, clear = false) {
			var sd = this.searchData;
			this.searchData = this.$common.deepCopy(sd, searchData);
			if (clear) {
				this.goodsList = [];
			}
		},
		onChangeShowState: function() {
			var _this = this;
			_this.showView = !_this.showView;
		},
		//点击综合排序
		comprehensive: function() {
			this.setSearchData(
				{
					order: {
						key: 'sort',
						sort: 'asc'
					},
					page: 1
				},
				true
			);
			this.getGoods();
		},
		//销量
		salesVolume: function() {
			if (this.searchData.order.key == 'buy_count') {
				if (this.searchData.order.sort == 'desc') {
					this.searchData.order.sort = 'asc';
				} else {
					this.searchData.order.sort = 'desc';
				}
			} else {
				this.searchData.order = {
					key: 'buy_count',
					sort: 'desc'
				};
			}
			this.searchData.page = 1; //从第一页重新显示
			this.setSearchData(this.searchData, true);
			this.getGoods();
		},
		//价格排序
		priceSort: function() {
			if (this.searchData.order.key == 'price') {
				if (this.searchData.order.sort == 'desc') {
					this.searchData.order.sort = 'asc';
				} else {
					this.searchData.order.sort = 'desc';
				}
			} else {
				this.searchData.order = {
					key: 'price',
					sort: 'asc'
				};
			}
			this.searchData.page = 1; //从第一页重新显示
			this.setSearchData(this.searchData, true);
			this.getGoods();
		},
		//设置查询价格区间
		// 		orderPrice: function(e) {
		// 			var reg = /^[0-9]+(.[0-9]{2})?$/;
		// 			if (!reg.test(e.detail.value)) {
		// 				this.$common.errorToShow('请输入正确金额');
		// 				this.maxPrice = '';
		// 			} else {
		// 				this.maxPrice = e.detail.value;
		// 			}
		// 		},
		//查询价格区间
		// 		searchPrice: function(event) {
		// 			if (
		// 				this.minPrice > 0 &&
		// 				this.maxPrice > 0 &&
		// 				this.minPrice > this.maxPrice
		// 			) {
		// 				app.common.errorToShow('价格区间有误');
		// 				return false;
		// 			}
		//
		// 			this.setSearchData(
		// 				{
		// 					page: 1,
		// 					where: {
		// 						price_f: this.minPrice,
		// 						price_t: this.maxPrice
		// 					}
		// 				},
		// 				true
		// 			);
		// 			this.getGoods();
		// 		},
		//页面相关事件处理函数--监听用户下拉动作
		onPullDownRefresh: function() {},
		//跳转到商品详情页面
		goodsDetail: function(id) {
			let url = '/pages/goods/index/index?id=' + id;
			this.$common.navigateTo(url);
		},
		//取得商品数据
		getGoods: function() {
			var _this = this;
			if (_this.ajaxStatus) {
				return false;
			}
			_this.ajaxStatus = true;
			_this.loading = true;
			_this.loadingComplete = false;
			_this.nodata = true;
			//如果已经没有数据了，就不取数据了，直接提示已经没有数据
			if (_this.loadingComplete) {
				_this.$common.errorToShow('暂时没有数据了');
				return false;
			}

			_this.$api.goodsList(_this.conditions(), function(res) {
				if (res.status) {
					//判是否没有数据了，只要返回的记录条数小于总记录条数，那就说明到底了，因为后面没有数据了
					var isEnd = false;
					if (res.data.list.length < _this.searchData.limit) {
						isEnd = true;
					}
					//判断是否为空
					var isEmpty = false;
					if (_this.searchData.page == 1 && res.data.list.length == 0) {
						isEmpty = true;
					}

					if (res.data.class_name != '') {
						uni.setNavigationBarTitle({
							title: res.data.class_name
						});
					} else {
						if (res.data.where && res.data.where.search_name && res.data.where.search_name != '') {
							uni.setNavigationBarTitle({
								title: '商品搜索'
							});
						}
					}

					_this.goodsList = _this.goodsList.concat(res.data.list);
					_this.ajaxStatus = false;
					_this.loading = !isEnd && !isEmpty;
					_this.toView = '';
					_this.loadingComplete = isEnd && !isEmpty;
					_this.nodata = isEmpty;
					if (res.data.filter) {
						let filter = res.data.filter;
						if (filter.brand_ids) {
							for (let i = 0; i < filter.brand_ids.length; i++) {
								filter.brand_ids[i].isSelect = false;
							}
							_this.brand_list = filter.brand_ids;
						}
						if (filter.goods_cat) {
							for (let i = 0; i < filter.goods_cat.length; i++) {
								filter.goods_cat[i].isSelect = false;
							}
							_this.cat_list = filter.goods_cat;
						}
						if (filter.label_ids) {
							for (let i = 0; i < filter.label_ids.length; i++) {
								filter.label_ids[i].isSelect = false;
							}
							_this.label_list = filter.label_ids;
						}
					}
				}
			});
		},
		//上拉加载
		lower: function() {
			var _this = this;
			_this.toView = 'loading';

			if (!_this.loadingComplete) {
				_this.setSearchData({
					page: _this.searchData.page + 1
				});
				_this.getGoods();
			}
		},
		listgrid: function() {
			let _this = this;
			if (_this.alllist) {
				_this.allgrid = true;
				_this.listgrid = true;
				_this.alllist = false;
			} else {
				_this.allgrid = false;
				_this.listgrid = false;
				_this.alllist = true;
			}
		},
		// 统一返回筛选条件 查询条件 分页
		conditions() {
			let data = this.searchData;
			var newData = {};
			newData = this.$common.deepCopy(newData, data);
			//把data里的where换成json
			if (data.where) {
				newData.where = JSON.stringify(data.where);
			}
			//把排序换成字符串
			if (data.order) {
				var sort = data.order.key + ' ' + data.order.sort;
				if (data.order.key != 'sort') {
					sort = sort + ',sort asc'; //如果不是综合排序，增加上第二个排序优先级排序
				}
				newData.order = sort;
			} else {
				newData.order = 'sort asc';
			}
			return newData;
		},
		//老搜索
		search() {
			this.setSearchData(
				{
					page: 1,
					where: {
						search_name: this.keyword
					}
				},
				true
			);
			this.getGoods();
		},
		//去搜索
		goSearch() {
			let pages = getCurrentPages();
			let prevPage = pages[pages.length - 2];
			// #ifdef H5 || MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE || MP-TOUTIAO
			if (prevPage && prevPage.route) {
				let search_flag = prevPage.route;
				if (search_flag == 'pages/index/search') {
					uni.navigateBack({
						delta: 1
					});
				} else {
					this.$common.navigateTo('/pages/index/search');
				}
			} else {
				this.$common.navigateTo('/pages/index/search');
			}
			// #endif

			// #ifdef MP-ALIPAY
			if (prevPage && prevPage.__proto__.route) {
				let search_flag = prevPage.__proto__.route;
				if (search_flag == 'pages/index/search') {
					uni.navigateBack({
						delta: 1
					});
				} else {
					this.$common.navigateTo('/pages/index/search');
				}
			} else {
				this.$common.navigateTo('/pages/index/search');
			}
			// #endif
		},
		//筛选条件弹出窗口
		toshow() {
			this.$refs.lvvpopref.show();
			this.screents = false;
			this.screentc = true;
		},
		//关闭筛选
		toclose() {
			this.$refs.lvvpopref.close();
			this.screentc = false;
			this.screents = true;
		},
		//取消筛选
		filterNo() {
			this.ePrice = '';
			this.sPrice = '';
			for (let i = 0; i < this.cat_list.length; i++) {
				this.cat_list[i].isSelect = false;
			}
			for (let i = 0; i < this.brand_list.length; i++) {
				this.brand_list[i].isSelect = false;
			}
			for (let i = 0; i < this.label_list.length; i++) {
				this.label_list[i].isSelect = false;
			}
			this.filterOk();
			this.toclose();
		},
		//确认筛选
		filterOk() {
			let data = this.searchData;

			//获取分类
			// data.where.cat_id = '';
			for (let i = 0; i < this.cat_list.length; i++) {
				if (this.cat_list[i].isSelect) {
					data.where.cat_id = this.cat_list[i].goods_cat_id;
				}
			}

			//获取多个品牌
			let brand_ids = '';
			for (let i = 0; i < this.brand_list.length; i++) {
				if (this.brand_list[i].isSelect) {
					brand_ids += this.brand_list[i].brand_id + ',';
				}
			}
			if (brand_ids) {
				brand_ids = brand_ids.substr(0, brand_ids.length - 1);
			}
			data.where.brand_id = brand_ids;

			//获取标签
			data.where.label_id = '';
			for (let i = 0; i < this.label_list.length; i++) {
				if (this.label_list[i].isSelect) {
					data.where.label_id = this.label_list[i].id;
				}
			}

			//价格区间
			data.where.price_f = '';
			data.where.price_t = '';
			if (
				this.sPrice * 1 < 0 ||
				(this.ePrice != '' && this.ePrice <= 0) ||
				this.ePrice * 1 < 0 ||
				(this.sPrice * 1 > this.ePrice * 1 && this.sPrice != '' && this.ePrice != '')
			) {
				this.$common.errorToShow('价格区间有误');
				return false;
			} else {
				data.where.price_f = this.sPrice;
				data.where.price_t = this.ePrice;
			}

			this.setSearchData(data, true);
			this.getGoods();
			this.toclose();
		},
		//选择
		selectKey(type, id) {
			//分类一次只能选择一个
			if (type == 'cat_list') {
				for (let i = 0; i < this.cat_list.length; i++) {
					if (this.cat_list[i].goods_cat_id == id) {
						this.cat_list[i].isSelect = this.cat_list[i].isSelect ? false : true;
					} else {
						this.cat_list[i].isSelect = false;
					}
				}
			}

			if (type == 'brand_list') {
				for (let i = 0; i < this.brand_list.length; i++) {
					if (this.brand_list[i].brand_id == id) {
						this.brand_list[i].isSelect = this.brand_list[i].isSelect ? false : true;
					}
				}
			}

			if (type == 'label_list') {
				for (let i = 0; i < this.label_list.length; i++) {
					if (this.label_list[i].id == id) {
						this.label_list[i].isSelect = this.label_list[i].isSelect ? false : true;
					} else {
						this.label_list[i].isSelect = false;
					}
				}
			}
		}
	},
	// #ifdef MP-ALIPAY
	onChangeShowState_show: function() {
		var that = this;
		that.setData({
			showView: (that.showView = true)
		});
	},
	onChangeShowState_hid: function() {
		var that = this;
		that.setData({
			showView: (that.showView = false)
		});
	}
	// #endif
};
</script>

<style>
page {
	background-color: #fff;
}
.search {
	position: fixed;
	z-index: 997;
	/*  #ifdef  H5  */
	top: 44px;
	/*  #endif  */
}
.screen {
	width: 100%;
	padding: 10upx 26upx 20upx;
	margin-bottom: 2upx;
	background-color: #fff;
	position: fixed;
	/*  #ifdef  H5  */
	top: calc(44px + 104upx);
	/*  #endif  */
	/*  #ifndef H5 */
	top: 104upx;
	/*  #endif  */
	display: flex;
	z-index: 997;
}
.screen-item {
	width: 20%;
	height: 50upx;
	display: flex;
	position: relative;
	align-items: center;
	justify-content: center;
}
.screents {
	border-left: 2upx solid #eee;
}
.screen-item-text {
	font-size: 24upx;
	color: #333;
	margin-right: 8upx;
}
.screen-item-icon {
	display: inline-block;
}
.screen-item-icon-img {
	width: 16upx;
	height: 8upx;
	display: block;
}
.screen-item-icon .screen-item-icon-img:first-child {
	margin-bottom: 4upx;
}
.list-grid {
	width: 44upx;
	height: 44upx;
	float: left;
}
.filter-img {
	width: 18upx;
	height: 8upx;
}
.img-grids {
	padding-bottom: 26upx;
}
.img-grids-item {
	margin-bottom: 0;
}
.img-grids > view,
.img-list > view {
	overflow: hidden;
}
.scroll-Y {
	/*  #ifdef  H5  */
	height: calc(100vh - 52rpx - 40rpx);
	padding-top: calc(52rpx + 140rpx);
	box-sizing: border-box;
	/*  #endif  */
	/*  #ifndef H5 */
	height: calc(100vh - 186upx);
	padding-top: 186rpx;
	/*  #endif  */
}

.uni-scroll-view {
	width: 100%;
	height: initail !important;
}
.search-input-p {
	color: #888;
}
.order-none {
	text-align: center;
	padding: 200upx 0;
}
.order-none-img {
	width: 274upx;
	height: 274upx;
}
.fliter-c {
	width: 100%;
	/*  #ifdef  H5  */
	height: calc(100% - 44px - 184upx);
	top: calc(44px + 182upx);
	/*  #endif  */
	/*  #ifndef H5 */
	height: calc(100% - 184upx);
	top: 182upx;
	/*  #endif  */
	background: #ffffff;
	position: absolute;
	left: 0;

	padding-bottom: 90upx;
}
.fliter-item {
}
.fliter-item .cell-item {
	border-bottom: none;
}
.fliter-i-c {
	padding: 0 26upx;
	overflow: hidden;
}
.fic-item {
	display: inline-block;
	float: left;
	width: 160upx;
	margin-right: 14upx;
	height: 70upx;
	background-color: #f1f1f1;
	text-align: center;
	font-size: 24upx;
	margin-bottom: 14upx;
	color: #333;
	padding: 0 10upx;
}
.fic-item-active {
	background-color: #ff7159;
	color: #fff;
}
.fic-item-text {
	position: relative;
	top: 50%;
	transform: translateY(-50%);
}
.fic-item:nth-child(4n) {
	margin-right: 0;
}
.fic-item-line {
	float: left;
	margin: 34upx 18upx 0 0;
	width: 50upx;
	height: 2upx;
	border-bottom: 2upx solid #ccc;
}
.fic-item-input {
	position: relative;
	top: 50%;
	transform: translateY(-50%);
}

/* #ifdef MP-ALIPAY */
.hide {
	display: none;
}
.show {
	display: block;
}
/* #endif */
.square {
	border-radius: 0;
}
.radius {
	border-radius: 12upx;
}
</style>
