<template>
	<view class="content">
		<view class="ig-top">
			<view class="ig-top-t">
				<view class="" v-if="lasttime.day!=0||lasttime.hour!=0||lasttime.minute!=0||lasttime.second!=0">
					剩余时间：<uni-countdown :day="lasttime.day" :hour="lasttime.hour" :minute="lasttime.minute" :second="lasttime.second"></uni-countdown>
				</view>
			</view>
			<view class="ig-top-m">
				<view class="user-head-img-c" v-for="(item, index) in teamInfo.list" :key="index">
					<view class="user-head-img-tip" v-if="item.id == item.team_id">拼主</view>
					<image class="user-head-img cell-hd-icon have-none" :src='item.user_avatar' mode=""></image>
				</view>
				<view class="user-head-img-c uhihn" v-if="teamInfo.team_nums" v-for="n in teamInfo.team_nums" :key="n"><text>?</text></view>
			</view>
			<view class="ig-top-b">
				<view class="igtb-top">
					还差<text class="red-price">{{ teamInfo.team_nums }}</text>人，赶快邀请好友来拼单吧
				</view>
				<view class="igtb-mid">
					<button class="btn" @click="goShare()">邀请好友拼单</button>
				</view>
				<view class="igtb-bot">
					分享好友越多，成团越快
				</view>
			</view>
		</view>
		<!-- 弹出层 -->
		<lvv-popup position="bottom" ref="share">

			<!-- #ifdef H5 -->
			<shareByH5 :shareType='3' :goodsId="goodsInfo.goods_id" :teamId="teamInfo.team_id" :groupId="teamInfo.rule_id"
			 :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief" :shareHref="shareHref"
			 @close="closeShare()"></shareByH5>
			<!-- #endif -->

			<!-- #ifdef MP-WEIXIN -->
			<shareByWx :shareType='3' :goodsId="goodsInfo.goods_id" :teamId="teamInfo.team_id" :groupId="teamInfo.rule_id"
			 :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief" :shareHref="shareHref"
			 @close="closeShare()"></shareByWx>
			<!-- #endif -->

			<!-- #ifdef MP-ALIPAY -->
			<shareByAli :shareType='3' :goodsId="goodsInfo.goods_id" :teamId="teamInfo.team_id" :groupId="teamInfo.rule_id"
			 :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief" :shareHref="shareHref"
			 @close="closeShare()"></shareByAli>
			<!-- #endif -->
			
			<!-- #ifdef MP-TOUTIAO -->
			<shareByTt :shareType='3' :goodsId="goodsInfo.goods_id" :teamId="teamInfo.team_id" :groupId="teamInfo.rule_id"
			 :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief" :shareHref="shareHref"
			 @close="closeShare()"></shareByTt>
			<!-- #endif -->

			<!-- #ifdef APP-PLUS || APP-PLUS-NVUE -->
			<shareByApp :shareType='3' :goodsId="goodsInfo.goods_id" :teamId="teamInfo.team_id" :groupId="teamInfo.rule_id"
			 :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief" :shareHref="shareHref"
			 @close="closeShare()"></shareByApp>
			<!-- #endif -->

		</lvv-popup>
		<view class="cell-group margin-cell-group">
			<view class='cell-item'>
				<view class='cell-item-hd'>
					<view class='cell-hd-title'>商品名称</view>
				</view>
				<view class='cell-item-ft'>
					<text class="cell-ft-text">{{ goodsInfo.name }}</text>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd'>
					<view class='cell-hd-title'>拼单时间</view>
				</view>
				<view class='cell-item-ft'>
					<text class="cell-ft-text">{{ orderInfo.ctime }}</text>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd'>
					<view class='cell-hd-title'>拼单须知</view>
				</view>
				<view class='cell-item-ft group-notice'>
					<text class="cell-ft-text">* 好友拼单 </text>
					<text class="cell-ft-text">* 人满发货 </text>
					<text class="cell-ft-text">* 人不满退款 </text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
	import uniCountdown from "@/components/uni-countdown/uni-countdown.vue"
	import {
		get
	} from '@/config/db.js';
	import {
		apiBaseUrl
	} from '@/config/config.js';
	import share from '@/components/share/share.vue';
	// #ifdef H5
	import shareByH5 from '@/components/share/shareByh5.vue'
	// #endif
	// #ifdef MP-WEIXIN
	import shareByWx from '@/components/share/shareByWx.vue'
	// #endif
	// #ifdef MP-ALIPAY
	import shareByAli from '@/components/share/shareByAli.vue'
	// #endif
	// #ifdef MP-TOUTIAO
	import shareByTt from '@/components/share/shareByTt.vue'
	// #endif
	// #ifdef APP-PLUS || APP-PLUS-NVUE
	import shareByApp from '@/components/share/shareByApp.vue'
	// #endif

	import htmlParser from '@/common/html-parser'
	export default {
		components: {
			lvvPopup,
			uniCountdown,
			share,
			// #ifdef H5
			shareByH5,
			// #endif
			// #ifdef MP-WEIXIN
			shareByWx,
			// #endif
			// #ifdef MP-ALIPAY
			shareByAli,
			// #endif
			// #ifdef MP-TOUTIAO
			shareByTt,
			// #endif
			// #ifdef APP-PLUS || APP-PLUS-NVUE
			shareByApp,
			// #endif
			// spec
		},
		data() {
			return {
				shareType: 3,
				providerList: [], // 分享通道 包含生成海报
				swiper: {
					indicatorDots: true,
					autoplay: true,
					interval: 3000,
					duration: 800,
				}, // 轮播图属性设置
				goodsInfo: [],
				teamInfo: [],
				favLogo: [
					'/static/image/ic-me-collect.png',
					'/static/image/ic-me-collect2.png'
				],
				horizontal: 'right', //右下角弹出按钮
				vertical: 'bottom',
				direction: 'vertical',
				pattern: {
					color: '#7A7E83',
					backgroundColor: '#fff',
					selectedColor: '#007AFF',
					buttonColor: "#FF7159"
				},

				query: '', // query参数登录跳转回来使用
				indicatorDots: false,
				autoplay: false,
				interval: 2000,
				duration: 500,
				lasttime: {
					day: 0,
					hour: 0,
					minute: 0,
					second: 0
				}, //购买倒计时
				userToken: 0,
				time: 0,
				order_id:'',//订单号
				orderInfo:{},
                shareUrl: '/pages/share/jump'
			}
		},
		onLoad(options) {
			if(options.order_id){
				this.order_id = options.order_id;
			}else{
				this.$common.errorToShow('参数错误');
			}
			let teamInfo,orderInfo,goodsInfo
	
			let pages = getCurrentPages()
			let pre = pages[pages.length - 2]
			if(typeof pre!='undefined'){
				// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE
				teamInfo = pre.teamInfo
				orderInfo = pre.orderInfo
				// #endif
				
				// #ifdef MP-WEIXIN
				teamInfo = pre.$vm.teamInfo
				orderInfo = pre.$vm.orderInfo
				// #endif
				
				// #ifdef MP-ALIPAY || MP-TOUTIAO
				teamInfo = pre.data.teamInfo;
				orderInfo = pre.data.orderInfo
				// #endif
			}
			if(teamInfo && orderInfo){
				this.teamInfo = teamInfo;
				this.orderInfo = orderInfo;
				this.goodsInfo = orderInfo.items[0];
				
			}else{
				this.orderDetail();
				this.getTeam();
			}
			let timestamp = Date.parse(new Date())/1000;
			this.lasttime = this.$common.timeToDateObj(options.close_time-timestamp);
		},
		computed: {
			shareHref() {
				let pages = getCurrentPages()
				let page = pages[pages.length - 1]
				// #ifdef H5 || MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE || MP-TOUTIAO
				return apiBaseUrl + 'wap/' + page.route + '?scene=' + this.query;
				// #endif

				// #ifdef MP-ALIPAY
				return apiBaseUrl + 'wap/' + page.__proto__.route + '?scene=' + this.query;
				// #endif
			}
		},
		onReachBottom() {
			if (this.current === 2 && this.goodsComments.loadStatus === 'more') {
				this.getGoodsComments();
			}
		},
		methods: {
			//拼团信息
			getTeam(){
				this.$api.getOrderPintuanTeamInfo({order_id:this.order_id},res=>{
					if (res.status) {
						
					    this.teamInfo = {
					        list:res.data.teams,
							current_count:res.data.teams.length,
					        people_number:res.data.people_number,
					        team_nums:res.data.team_nums,//剩余
					        close_time:res.data.close_time,//关闭时间
							id:res.data.id,//拼团id
							team_id:res.data.team_id,//拼团团队id
							rule_id:res.data.rule_id,
					    };
						//console.log(this.lasttime);
					}else{
					    this.$common.errorToShow(res.msg)
					}
					
				});
			},
			//获取订单详情
			orderDetail () {
			    let _this = this
			    let data = {
			        order_id: _this.order_id
			    }
			    _this.$api.orderDetail(data, function(res) {
			        if (res.status) {
			            let data = res.data
						// 支付时间转换
						if (data.ctime !== null) {
							data.ctime = _this.$common.timeToDate(data.ctime)
						}
						
			            _this.orderInfo = data
						_this.goodsInfo = data.items[0];
					
			        } else {
			            _this.$common.errorToShow(res.msg)
			        }
			    })
			},
			// 关闭弹出层
			close() {
				this.$emit('close')
			},
			// 点击操作
			clickHandler(e) {
				if (e.cate === 'poster') {
					this.createPoster()
				} else {
					// 去分享
					this.share(e)
				}
			},
			// 显示modal弹出框
			toshow(type, team_id = 0) {
				if (type == 1) {
					this.lvvpopref_type = 1;
				}
				if (team_id !== 0) {
					this.team_id = team_id;
				}
				this.$refs.lvvpopref.show();
			},
			// 关闭modal弹出框
			toclose() {
				this.$refs.lvvpopref.close();
			},
			// 跳转到h5分享页面
			goShare() {
				this.$refs.share.show();
			},
			closeShare() {
				this.$refs.share.close();
			},
            //获取分享URL
            getShareUrl() {
                let data = {
                    client: 2,
                    url: "/pages/share/jump",
                    type: 1,
                    page: 3,
                    params: {
                        goods_id: this.goodsInfo.goods_id,
                        team_id: this.teamInfo.list[0].team_id
                    }
                };
                let userToken = this.$db.get('userToken');
                if (userToken && userToken != '') {
                	data['token'] = userToken;
                }
                this.$api.share(data, res => {
                    this.shareUrl = res.data
                });
            }
		},
        watch:{
            goodsInfo: {
                handler () {
                    this.getShareUrl();
                },
                deep: true
            },
            teamInfo: {
                handler () {
                    this.getShareUrl();
                },
                deep: true
            }
        },
		//分享
		onShareAppMessage() {
            return {
			 	title: this.goodsInfo.name,
			 	// #ifdef MP-ALIPAY
			 	desc: this.goodsInfo.brief,
			 	// #endif
			 	imageUrl: this.goodsInfo.image_url,
			 	path: this.shareUrl
			 }
		 }
	}
</script>

<style>
	.ig-top {
		text-align: center;
		background-color: #fff;
		padding: 20upx 26upx;
	}
	.ig-top-t,
	.ig-top-m {
		margin-bottom: 20upx;
	}
	.ig-top-t>view {
		display: inline-block;
		padding: 0 10upx;
		color: #999;
	}
	.user-head-img-c {
		position: relative;
		width: 80upx;
		height: 80upx;
		border-radius: 50%;
		margin-right: 20upx;
		box-sizing: border-box;
		display: inline-block;
		border: 1px solid #f3f3f3;
	}
	.user-head-img-tip {
		position: absolute;
		top: -6upx;
		left: -10upx;
		display: inline-block;
		background-color: #FF7159;
		color: #fff;
		font-size: 22upx;
		z-index: 98;
		padding: 0 10upx;
		border-radius: 10upx;
		transform: scale(.8);
	}
	.user-head-img-c .user-head-img {
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}
	.user-head-img-c:first-child {
		border: 1px solid #FF7159;
	}
	.uhihn {
		width: 80upx;
		height: 80upx;
		border-radius: 50%;
		display: inline-block;
		border: 2upx dashed #e1e1e1;
		text-align: center;
		color: #d1d1d1;
		font-size: 40upx;
		box-sizing: border-box;
		position: relative;
	}
	.uhihn>text {
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
	}
	.igtb-top {
		font-size: 32upx;
		color: #333;
		margin-bottom: 16upx;
	}
	.igtb-mid {
		margin-bottom: 16upx;
	}
	.igtb-mid .btn {
		width: 100%;
		background-color: #FF7159;
		color: #fff;
	}
	.igtb-bot {
		font-size: 24upx;
		color: #666;
	}
	.cell-ft-text {
		max-width: 520upx;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.group-notice .cell-ft-text {
		color: #999;
		margin-left: 20upx;
		font-size: 26upx;
	}
</style>