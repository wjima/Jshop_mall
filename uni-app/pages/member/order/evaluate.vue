<template>
	<view class="content">
		<view class="content-top">
			<view class='img-list'>
				<view class='img-list-item'
				v-for="item in info.items"
				:key="item.id"
				>
					<view class="img-list-item-gray">
						<image class='img-list-item-l small-img' :src='item.image_url' mode='aspectFill'></image>
						<view class='img-list-item-r small-right'
						@click="goodsDetail(item.goods_id)"
						>
							<view class='little-right-t'>
								<view class='goods-name list-goods-name'>{{ item.name }}</view>
							</view>
						</view>
					</view>
					
					<view class="evaluate-num">
						<view class="evaluate-num-t">商品评分</view>
						<view class="evaluate-num-b">
							<uni-rate 
							size="18" 
							:jid="item.id"
							:value="score[item.id]"
							@change="changeScore"
							></uni-rate>
						</view>
					</view>
					
					<view class="evaluate-content">
						<view class="evaluate-c-t">
							<input v-model="textarea[item.id]" placeholder="宝贝满足你的期待吗? 说说你的使用心得" />
						</view>
						<view class="evaluate-c-b">
							<view class="goods-img-item"
							v-if="images[item.id].length"
							v-for="(img, key) in images[item.id]"
							:key="key"
							>
								<image class="del" src="/static/image/del.png" mode="" @click="removeImg(item.id, key)"></image>
								<image class="" :src="img.url" mode="" @click="clickImg(img.url)"></image>
							</view>
							<view class="upload-img" v-show="isupload[item.id]">
								<image class="icon" src="/static/image/camera.png" mode="" @click="uploadImg(item.id)"></image>
								<view class="">上传照片</view>
							</view>
						</view>
					</view>
					
				</view>
			</view>
			
		</view>

		<view class="button-bottom">
			<button class="btn btn-square btn-b" hover-class="btn-hover" @click="toEvaluate" :disabled='submitStatus' :loading='submitStatus'>提交评论</button>
		</view>
		
	</view>
</template>

<script>
import uniRate from "@/components/uni-rate/uni-rate.vue"
import { goods } from '@/config/mixins.js'
export default {
	mixins: [goods],
    components: {uniRate},
	data () {
		return {
			orderId: 0,
			info: {}, // 订单详情
			images: [],
            score: [], // 商品评价
            textarea: [], // 商品评价信息
            isupload: [], // 启/禁用 图片上传按钮
			rate: 5,
			submitStatus: false
		}
	},
	onLoad (options) {
		this.orderId = options.order_id
		this.orderId 
		? this.orderInfo() 
		: this.$common.errorToShow('获取失败', () => {
			uni.navigateBack({
				delta: 1
			})
		})
	},
	computed: {
		// 获取vuex中状态
		maxUploadImg () {
			return this.$store.state.config.image_max
		}
	},
	methods: {
		// 获取订单详情
		orderInfo () {
			let data = {
				order_id: this.orderId
			}
			this.$api.orderDetail(data, res => {
				if (res.status && res.data.pay_status >= 2 && res.data.ship_status >= 3 && res.data.confirm >= 2 && res.data.is_comment === 1) {
					const _info = res.data
					
					let images = []
					let textarea = []
					let upload = []
					let score = []
					
					_info.items.forEach (item => {
						images[item.id] = []
						textarea[item.id] = ''
						upload[item.id] = true
						score[item.id] = 5
					})
					
					this.info = _info
					
					this.images = images
					this.textarea = textarea
					this.score = score
					this.isupload = upload
				} else {
					this.$common.errorToShow('订单不存在或状态不可评价!')
				}
			})
		},
		// 上传图片
		uploadImg (key) {
			this.$api.uploadFiles(res => {
				if (res.status) {
					let img = {
                        url: res.data.url,
                        id: res.data.image_id
                    }
                    this.images[key].push(img)
					
					this.$common.successToShow(res.msg)
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		},
		// 删除图片
		removeImg (id, key) {
			this.images[id].splice(key, 1)
		},
		// 图片点击放大
		clickImg (img) {
			// 预览图片
			uni.previewImage({
				urls: img.split()
			});
		},
		// 改变评分
		changeScore (e) {
			this.score[e.id] = e.value
		},
		// 提交评价
		toEvaluate () {
			this.submitStatus = true;
			let items = {}
			
			this.images.forEach((item, key) => {
				items[key] = {
					images: item,
                    score: this.score[key],
                    textarea: this.textarea[key]
				}
			})

			let data = {
				order_id: this.orderId,
				items: items
			}

			this.$api.orderEvaluate(data, res => {
                if (res.status) {
                    this.$common.successToShow(res.msg, ress => {
						// 更改订单列表页的订单状态
						let pages = getCurrentPages(); // 当前页
						let beforePage = pages[pages.length - 2]; // 上个页面
						
						if (beforePage !== undefined && beforePage.route === 'pages/member/order/orderlist') {
							// #ifdef MP-WEIXIN
							beforePage.$vm.isReload = true
							// #endif
							
							// #ifdef H5
							beforePage.isReload = true
							// #endif
							
							// #ifdef MP-ALIPAY || MP-TOUTIAO
							this.$db.set('order_user_evaluate', true, true);
							// #endif
						}
						
						let before = pages[pages.length - 3]; // 上个页面
						if (before !== undefined && before.route === 'pages/member/order/orderlist') {
							// #ifdef MP-WEIXIN
							before.$vm.isReload = true
							// #endif
							
							// #ifdef H5
							before.isReload = true
							// #endif
							
							// #ifdef MP-ALIPAY || MP-TOUTIAO
							this.$db.set('order_user_evaluate', true, true);
							// #endif
						}
						
						// this.submitStatus = false;
						uni.navigateBack({
							delta: 1
						});
					})
                } else {
					this.$common.errorToShow(res.msg)
					// this.submitStatus = false;
				}
            },res => {
				this.submitStatus = false;
			})
		}
	},
	watch: {
		images: {
			handler () {
				this.images.forEach((item, key) => {
					this.isupload[key] = item.length > this.maxUploadImg ? false : true
				})
			},
			deep: true
		}
	}
}
</script>

<style>
.img-list-item{
	padding: 30upx 20upx;
}
.img-list-item-gray{
	background-color: #F7F7F7;
	overflow: hidden;
	padding: 18upx 20upx;
}
.small-right{
	width: 520upx;
}
.evaluate-content{
	background-color: #fff;
	padding: 20upx 0upx;
}
.evaluate-c-t{
	width: 100%;
	height: 240upx;
}
.evaluate-c-t textarea{
	width: 100%;
	height: 100%;
	font-size: 26upx;
	padding: 10upx;
}
.evaluate-c-b{
	overflow: hidden;
}
.upload-img{
	width: 146upx;
	height: 146upx;
	margin: 14upx;
	text-align: center;
	color: #999999;
	font-size: 22upx;
	border: 2upx solid #E1E1E1;
	/* #ifdef MP-ALIPAY */
	border-top: 8upx solid #E1E1E1;
	/* #endif */
	border-radius: 4upx;
	display: inline-block;
	float: left;
	padding: 24upx 0;
}
.goods-img-item{
	width: 174upx;
	height: 174upx;
	padding: 14upx;
	float: left;
	position: relative;
}
.goods-img-item:nth-child(4n){
	margin-right: 0;
}
.goods-img-item image{
	width: 100%;
	height: 100%;
}
.del{
	width: 30upx !important;
	height: 30upx !important;
	position: absolute;
	right: 0;
	top: 0;
	z-index: 999;
}
.evaluate-num{
	padding: 20upx 26upx;
	background-color: #fff;
	margin-top: 20upx;
}
.evaluate-num-t{
	color: #333;
	font-size: 28upx;
	margin-bottom: 20upx;
}
.button-bottom .btn{
	width: 100%;
}
</style>
