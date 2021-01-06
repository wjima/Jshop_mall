<template>
	<view class="bargain">
		<view class="shop-wrap">
			<view class="shop-title" v-if="info.bargain_user">
				<image :src="info.bargain_user.avatar||''" mode="" class="avatar"></image>
				{{ info.bargain_user.nickname || '' }}
			</view>
			<view class="shop-info" v-if="info.goods">
				<image :src="info.goods.image_url || ''" mode="" class="shop-pic"></image>
				<view class="shop-detail">
					<view class="info-title fsz28 color-3">{{ info.goods.name || '' }}</view>
					<view class="sub-desc fsz24">
						<text class="red ">{{ info.goods.brief || '' }}</text>
						<text class="color-9">库存:{{ info.max_goods_nums || '0' }}</text>
						<text class="color-9">|</text>
						<text class="color-9">已售:{{ info.sales_num || '0' }}</text>
					</view>
					<view class="time" v-show="info.lasttime && (info.status_progress == 1 ||info.status_progress == 2)">
						<uni-countdown
							:day="info.lasttime.day"
							:hour="info.lasttime.hour"
							:minute="info.lasttime.minute"
							:second="info.lasttime.second"
							backgroundColor="#4f1701"
							color="#fff"
						></uni-countdown>
						<text class="desc">后砍价过期</text>
					</view>
				</view>
			</view>
			<view class="pro-text">
				<view class="now-price-wrap">
					<text class="tag">当前价</text>
					<text class="red now-price">￥{{ info.current_price || '0.00' }}元</text>
					<br />
				</view>
				已砍
				<text class="red">￥{{ info.cut_off_price || '0.00' }}</text>
				元
			</view>
			<progress :percent="info.cut_off_progress" active border-radius="10" stroke-width="5" activeColor="#ff7159" backgroundColor="#e5e5e5" />
			<view class="share" @click="goShare()" v-if="info.status_progress == 1 && type == 1">分享好友，代您砍价</view>
			<view class="share" @click="goShare()" v-if="info.status_progress == 1 && type == 2">分享好友，帮他砍价吧~</view>
			<view class="share" v-if="info.status_progress == 2 && type == 1" @click="buyNow()">砍价成功，购买吧~</view>
			<view class="share" v-if="info.status_progress == 2 && type == 2" >非常感谢，助我砍价成功</view>
			
			<view class="share" v-if="info.status_progress == 3 || info.status_progress == 4">砍价结束</view>
		</view>

		<view class="tab-nav">
			<view class="tab-item" v-for="(item, idx) in tabList" :key="idx" @click="tabClick" :data-tabindex="idx" :class="{ active: idx == current }">{{ item }}</view>
		</view>
		<view class="tab tab-list-wrap" v-show="current == 0">
			<view v-if="info.friends_record && info.friends_record.list.length > 0">
				<view v-for="(item, idx) in info.friends_record.list" :key="idx" class="tab-list">
					<view class="user">
						<image :src="item.avatar||''" mode="aspectFill" class="user-avatar"></image>
						<view class="user-info">
							<view class="user-name">{{ item.nickname || '' }}</view>
							<view class="user-date">{{ item.ctime || '' }}</view>
						</view>
					</view>
					<view class="money">
						<text class="red">-{{ item.bargain_price || '0.00' }}</text>
						元
					</view>
				</view>
			</view>
			<view class="comment-none" v-else><image class="comment-none-img" src="/static/image/order.png" mode=""></image></view>
		</view>
		<!--商品详情-->
		<view class="tab tab-list-wrap" v-if="current == 1">
			<jshopContent :content="info.goods.intro" v-if="info.goods"></jshopContent>
			<view class="comment-none" v-else><image class="comment-none-img" src="/static/image/order.png" mode=""></image></view>
		</view>
		<!--活动规则-->
		<view class="tab tab-list-wrap" v-if="current == 2">
			<jshopContent :content="info.desc" v-if="info.desc"></jshopContent>
			<view class="comment-none" v-else><image class="comment-none-img" src="/static/image/order.png" mode=""></image></view>
		</view>
		<view class="tab tab-list-wrap" v-show="current == 3">
			<view v-if="info.attendance_record && info.attendance_record.list.length > 0">
				<view v-for="(item, idx) in info.attendance_record.list" :key="idx" class="tab-list">
					<view class="user">
						<image :src="item.avatar||''" mode="aspectFill" class="user-avatar"></image>
						<view class="user-info">
							<view class="user-name">{{ item.nickname || '' }}</view>
							<view class="user-date">{{ item.ctime || '' }}</view>
						</view>
					</view>
				</view>
			</view>
			<view class="comment-none" v-else><image class="comment-none-img" src="/static/image/order.png" mode=""></image></view>
		</view>
		<pop :popShow.sync="popShow" :price="bargain_price" />
		<lvv-popup position="bottom" ref="share" v-if="info.goods && record_id && record_id != 0">
			<!-- #ifdef H5 -->
			<shareByH5
				:goodsId="info.id"
				:shareImg="info.goods.image_url"
				:shareTitle="info.name"
				:shareContent="info.intro"
				:shareHref="shareHref"
				:shareType="10"
				:bargainType="2"
				:record_id="record_id"
				@close="closeShare()"
			></shareByH5>
			<!-- #endif -->

			<!-- #ifdef MP-WEIXIN -->
			<shareByWx
				:goodsId="info.id"
				:shareImg="info.goods.image_url"
				:shareTitle="info.name"
				:shareContent="info.intro"
				:shareHref="shareHref"
				:shareType="10"
				:bargainType="2"
				:record_id="record_id"
				@close="closeShare()"
			></shareByWx>
			<!-- #endif -->

			<!-- #ifdef MP-ALIPAY -->
			<shareByAli
				:goodsId="info.id"
				:shareImg="info.goods.image_url"
				:shareTitle="info.name"
				:shareContent="info.intro"
				:shareHref="shareHref"
				:shareType="10"
				:bargainType="2"
				:record_id="record_id"
				@close="closeShare()"
			></shareByAli>
			<!-- #endif -->

			<!-- #ifdef MP-TOUTIAO -->
			<shareByTt
				:goodsId="info.id"
				:shareImg="info.goods.image_url"
				:shareTitle="info.name"
				:shareContent="info.intro"
				:shareHref="shareHref"
				:shareType="10"
				:bargainType="2"
				:record_id="record_id"
				@close="closeShare()"
			></shareByTt>
			<!-- #endif -->

			<!-- #ifdef APP-PLUS || APP-PLUS-NVUE -->
			<shareByApp
				:goodsId="info.id"
				:shareImg="info.goods.image_url"
				:shareTitle="info.name"
				:shareContent="info.intro"
				:shareHref="shareHref"
				:shareType="10"
				:bargainType="2"
				:record_id="record_id"
				@close="closeShare()"
			></shareByApp>
			<!-- #endif -->
		</lvv-popup>
		<view class="flot-btn" v-if="(info.status_progress == 1 || info.status_progress == 2) && type == 2" @click="friendsAddBargain()">我也要砍</view>
		<view class="flot-btn" v-if="type == 1 && info.status_progress == 1" @click="buyNow()">立即购买</view>
	</view>
</template>

<script>
import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
import jshopContent from '@/components/jshop/jshop-content.vue'; //视频和文本解析组件
import uniCountdown from '@/components/uni-countdown/uni-countdown.vue';
import pop from '@/pages/bargain/pop.vue';
// #ifdef H5
import shareByH5 from '@/components/share/shareByh5.vue';
// #endif
// #ifdef MP-WEIXIN
import shareByWx from '@/components/share/shareByWx.vue';
// #endif
// #ifdef MP-TOUTIAO
import shareByTt from '@/components/share/shareByTt.vue';
// #endif
// #ifdef MP-ALIPAY
import shareByAli from '@/components/share/shareByAli.vue';
// #endif
// #ifdef APP-PLUS || APP-PLUS-NVUE
import shareByApp from '@/components/share/shareByApp.vue';
// #endif
import { apiBaseUrl } from '@/config/config.js';

import { goBack } from '@/config/mixins.js';

export default {
	mixins: [goBack],

	data() {
		return {
			tabList: ['亲友团', '商品详情', '活动规则', '参与榜'],
			current: 0,
			popShow: false,
			id: 0, //砍价id
			type: 1, //砍价类型 1自己砍，2替别人砍
			record_id: 0, //邀请人活动id //自己进的时候，没有记录id
			bargain_price: 0, //砍掉多少钱
			info: {}, //砍价信息
			userInfo: {}, // 用户信息
			buyNum: 1, //购买数量
			shareUrl: '/pages/share/jump'
		};
	},
	components: {
		pop,
		jshopContent,
		uniCountdown,
		lvvPopup,
		// #ifdef H5
		shareByH5,
		// #endif
		// #ifdef MP-WEIXIN
		shareByWx,
		// #endif
		// #ifdef MP-TOUTIAO
		shareByTt,
		// #endif
		// #ifdef MP-ALIPAY
		shareByAli,
		// #endif
		// #ifdef APP-PLUS || APP-PLUS-NVUE
		shareByApp
		// #endif
	},
	onLoad(option) {
		if (!option.id) {
			goBack.backBtn();
		}
		this.id = option.id;
		//有邀请人，说明是别人来砍价的
		if (option.record_id && option.record_id != 0) {
			this.record_id = option.record_id;
		}
		if (option.type && option.type != 0) {
			this.type = option.type;
		}
		// this.getUserInfo();
	},
	onShow() {
		this.getUserInfo();
	},
	computed: {
		shareHref() {
			let pages = getCurrentPages();
			let page = pages[pages.length - 1];
			// #ifdef H5 || MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE
			return apiBaseUrl + 'wap/' + page.route + '?id=' + this.id + '&record_id=' + this.record_id + '&type=2';
			// #endif

			// #ifdef MP-ALIPAY
			return apiBaseUrl + 'wap/' + page.__proto__.route + '?id=' + this.id + '&record_id=' + this.record_id + '&type=2';
			// #endif
		}
	},
	methods: {
		tabClick(e) {
			this.current = e.currentTarget.dataset.tabindex;
		},
		//参加砍一刀
		doAdd() {
			let data = {
				id: this.id
			};
			this.$api.addBargain(data, res => {
				if (res.status) {
					this.doBargain(res.data); //参与成功
				} else {
					if (res.data.code && res.data.code == 'over') {
						this.$common.errorToShow(res.msg);
						goBack.backBtn();
					} else {
						this.getBargainDetial(res.data); //已经参与过
					}
				}
			});
		},
		//砍一刀
		doBargain(record_id) {
			let data = {
				type: this.type,
				id: this.id,
				record_id: record_id
			};
			this.record_id = record_id;
			this.$api.doBargain(data, res => {
				this.getBargainDetial(record_id); //获取详情
				if (res.status) {
					this.info.current_price = res.data.current_price;
					this.bargain_price = res.data.bargain_price; //砍掉多少钱
					this.popShow = true;
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
		//砍价详情
		getBargainDetial(record_id) {
			let data = {
				type: this.type,
				record_id: record_id,
				id: this.id
			};
			this.record_id = record_id;
			this.$api.getBargainDetial(data, res => {
				if (res.status) {
					this.info = res.data;
				} else {
					// 接口请求出错了
					this.$common.errorToShow(res.msg);
				}
			});
		},
		getUserInfo() {
			var _this = this;
			this.$api.userInfo({}, res => {
				if (res.status) {
					_this.userInfo = res.data;
					if (_this.type == 1) {
						//自己进入的，先参加
						if (_this.record_id && _this.record_id != 0) {
							//会员中心进入
							_this.getBargainDetial(_this.record_id);
						} else {
							_this.doAdd();
						}
					} else {
						_this.doBargain(_this.record_id); //他人进入的，直接进入砍价
					}
				}
			});
		},
		// 立即购买
		buyNow() {
			if (this.buyNum > 0) {
				let data = {
					product_id: this.info.goods.product.id,
					nums: 1,
					type: 2,
					order_type: 6 //砍价
				};
				this.$api.addCart(
					data,
					res => {
						if (res.status) {
							let cartIds = res.data;
							this.$common.navigateTo(
								'/pages/goods/place-order/index?cart_ids=' + JSON.stringify(cartIds) + '&order_type=6&bargain_id=' + this.id + '&record_id=' + this.record_id
							);
						} else {
							this.$common.errorToShow(res.msg);
						}
					},
					res => {
						this.submitStatus = false;
					}
				);
			}
		},
		//我也要砍
		friendsAddBargain() {
			this.$common.navigateTo('/pages/bargain/index?id=' + this.id);
		},
		goShare() {
			this.$refs.share.show();
		},
		closeShare() {
			this.$refs.share.close();
		},
		//获取分享URL todo
		getShareUrl() {
			let data = {
				client: 2,
				url: '/pages/share/jump',
				type: 1,
				page: 10,
				params: {
					record_id: this.record_id,
					id: this.id,
					type:2//分享别人，只能是2
				}
			};
			let userToken = this.$db.get('userToken');
			if (userToken && userToken != '') {
				data['token'] = userToken;
			}
			this.$api.share(data, res => {
				this.shareUrl = res.data;
			});
		}
	},
	watch: {
		record_id: {
			handler() {
				// #ifdef MP-WEIXIN
				this.getShareUrl();
				// #endif
			},
			deep: true
		}
	},
	//分享
	onShareAppMessage() {
		return {
			title: '我正在参与' + this.info.name + '砍价，快来祝我一臂之力~',
			// #ifdef MP-ALIPAY
			desc: this.info.intro,
			// #endif
			imageUrl: this.info.goods.image_url,
			path: this.shareUrl
		};
	}
};
</script>

<style>
.flot-btn {
	position: fixed;
	bottom: 0;
	width: 100%;
	height: 80rpx;
	text-align: center;
	line-height: 80rpx;
	background-color: #ff7159;
	font-size: 26rpx;
	color: #ffffff;
}

.red {
	color: #ff7159;
}
.bargain {
	padding-bottom: 100rpx;
}
.shop-wrap {
	padding: 0 20rpx;
	background: #fff;
	padding: 15rpx 20rpx 30rpx 20rpx;
}
.shop-wrap .shop-title {
	display: flex;
	align-items: center;
	margin-bottom: 15rpx;
}
.shop-wrap .shop-title .avatar {
	width: 47rpx;
	height: 50rpx;
	border-radius: 50%;
	margin-right: 20rpx;
}
.shop-wrap .shop-info {
	padding: 20rpx;
	background: #f4f4f4;
	display: flex;
}
.shop-wrap .shop-info .shop-pic {
	width: 120rpx;
	height: 120rpx;
	margin-right: 30rpx;
	flex: 0 0 120rpx;
}
.shop-info .shop-detail .info-title {
	width: 485rpx;
	overflow: hidden;
	white-space: nowrap;
	word-break: break-all;
	text-overflow: ellipsis;
	font-size: 20rpx;
}
.shop-info .shop-detail .sub-desc {
	font-size: 24rpx;
	color: #cacaca;
	margin-top: 6rpx;
}
.shop-info .shop-detail .sub-desc text.red {
	font-size: 24upx;
}
.shop-info .shop-detail .sub-desc text {
	margin-right: 15rpx;
}
.shop-info .shop-detail .time {
	display: flex;
	align-items: center;
	margin-top: 15rpx;
}
.shop-info .shop-detail .time text.block {
	display: inline-block;
	width: 37rpx;
	height: 37rpx;
	text-align: center;
	line-height: 37rpx;
	background-color: #4f1701;
	font-size: 20rpx;
	color: #fff;
	margin: 0 10rpx;
}
.shop-info .shop-detail .time text.block:first-child {
	margin-left: 0;
}
.shop-info .shop-detail .time text.desc {
	font-size: 24upx;
	color: #5f5f5f;
}
.pro-text {
	margin-top: 20rpx;
	margin-bottom: 14rpx;
	text-align: center;
	font-size: 24upx;
}
.pro-text text {
	font-size: 24upx;
}
.pro-text .now-price {
	font-size: 44rpx;
}
.pro-text .tag {
	color: #ff7159;
	border: 1rpx solid #ff7159;
	padding: 2rpx 4rpx;
	margin-right: 6rpx;
	font-size: 22rpx;
}
.pro-text .now-price-wrap {
	display: flex;
	align-items: center;
	justify-content: center;
	margin-bottom: 10rpx;
}
.share {
	width: 668rpx;
	height: 60rpx;
	border-radius: 30rpx;
	text-align: center;
	line-height: 60rpx;
	color: #fff;
	margin: 0 auto;
	margin-top: 44rpx;
	background: linear-gradient(left, #ff7159, #fb3642);
}
.tab-nav {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	margin-top: 20rpx;
	background: #fff;
}
.tab-nav .tab-item {
	flex: 1;
	text-align: center;
	color: #ff7159;
	padding: 28rpx 0;
	border-bottom: 1px solid #f3f3f3;
}
.tab-nav .tab-item.active {
	border-bottom: 1rpx solid #ff7159;
}

.tab-list {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 24rpx 20rpx;
}
.tab-list-wrap {
	background: #ffff;
}
.tab-list-wrap .user-avatar {
	width: 50rpx;
	height: 50rpx;
	border-radius: 50%;
	margin-right: 30rpx;
}
.tab-list-wrap .user {
	display: flex;
}
.tab-list-wrap .user .user-name {
	font-size: 24rpx;
	color: #434343;
}
.tab-list-wrap .user .user-date {
	color: #ababab;
	font-size: 16rpx;
	margin-top: 10rpx;
}
.tab-list-wrap .money {
	font-size: 16rpx;
	color: #ababab;
}
.tab-list-wrap .money text {
	font-size: 22rpx;
	color: #ff7159;
}
.comment-none {
	text-align: center;
	padding: 160upx 0;
}

.comment-none-img {
	width: 274upx;
	height: 274upx;
}
</style>
