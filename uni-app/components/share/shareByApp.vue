<template>
	<view style="width: 100%;height: 300upx;background: #FFFFFF;position: absolute;left:0;bottom: 0;">
		<view class="share-pop">
			<view class="share-item" 
			v-for="(item, index) in providerList"
			:key="index"
			@click="clickHandler(item)">
				<image :src="item.img" mode=""></image>
				<view class="">{{ item.name }}</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-w btn-square" @click="close()">关闭</button>
		</view>
	</view>	
</template>

<script>
import { h5Url } from '@/config/config.js'
export default {
	props: {
		// 商品id
		goodsId: {
			type: Number,
			default: 0
		},
		// 分享的图片
		shareImg: {
			type: String,
			default: ''
		},
		// 分享标题
		shareTitle: {
			type: String,
			default: ''
		},
		// 分享内容
		shareContent: {
			type: String,
			default: ''
		},
		// 分享链接
		shareHref: {
			type: String,
			default: ''
		},
		//分享类型
		shareType:{
			type:Number,
			default:1
		},
		//拼团id
		groupId:{
			type:Number,
			default:0
		},
		//拼团的团队id
		teamId:{
			type:Number,
			default:0
		}
	},
	data () {
		return {
			providerList: [] // 分享通道 包含生成海报
		}
	},
	mounted () {
		//H5+ 获取分享通道
		uni.getProvider({
			service: 'share',
			success: (e) => {
				let data = []
				for (let i = 0; i < e.provider.length; i++) {
					switch (e.provider[i]) {
						case 'weixin':
							data.push({
								name: '分享到微信好友',
								cate: 'share',
								id: 'weixin',
								img: '/static/image/ic-wechat.png',
								sort: 0
							})
							data.push({
								name: '分享到微信朋友圈',
								cate: 'share',
								id: 'weixin',
								type:'WXSenceTimeline',
								img: '/static/image/circle-of-friends.png',
								sort:1
							})
							break;
// 						case 'sinaweibo':
// 							data.push({
// 								name: '分享到新浪微博',
// 								cate: 'share',
// 								id: 'sinaweibo',
// 								img: '/static/image/sina-weibo.png',
// 								sort:2
// 							})
// 							break;
						case 'qq':
							data.push({
								name: '分享到QQ',
								cate: 'share',
								id: 'qq',
								img: '/static/image/qq.png',
								sort:3
							})
							break;
						default:
							break;
					}
				}
				data.push({
					name: '生成海报',
					cate: 'poster',
					id: 'poster',
					img: '/static/image/poster.png',
					sort: 5
				})
				this.providerList = data.sort((x,y) => {
					return x.sort - y.sort
				});
			},
			fail: (e) => {
				// console.log('获取分享通道失败', e)
			}
		});
	},
	methods: {
		// 关闭弹出层
		close () {
			this.$emit('close')
		},
		// 点击操作
		clickHandler (e) {
			if (e.cate === 'poster') {
				this.createPoster()
			} else {
				// 去分享
				this.share(e)
			}
		},
		// 生成海报
		createPoster () {
			let data = {
			    page: this.shareType,
			    url: h5Url + 'pages/share/jump',
			    type: 3,
			    client: 1
			}
			
			if (this.shareType == 2) {
			    //商品详情页
			    data.params = {
			        goods_id: this.goodsId
			    };
			} else if (this.shareType == 3) {
			    //拼团详情页
			    data.params = {
			        goods_id: this.goodsId
			    };
			} else if (this.shareType == 6) {
			    //拼团参团页
			    data.params = {
			        goods_id: this.goodsId,
			        group_id: this.groupId,
			        team_id: this.teamId
			    };
			} else if (this.shareType == 9) {
			    //团购秒杀
			    data.params = {
			        goods_id: this.goodsId,
			        group_id: this.groupId
			    }
			}
			let userToken = this.$db.get('userToken')
			if (userToken) {
				data.token = userToken
			}
			this.$api.share(data, res => {
				if (res.status) {
					this.close()
					this.$common.navigateTo('/pages/share?poster=' + encodeURIComponent(res.data))
				} else {
					this.$common.errorToShow(res.msg)
				}
			});
		},
		// 分享操作
		async share (e) {
			// console.log('分享通道:'+ e.id +'； 分享类型:' + this.shareType);
// 			if(!this.shareContent){
// 				uni.showModal({
// 					content:'分享内容不能为空',
// 					showCancel:false
// 				})
// 				return;
// 			}
// 			
// 			if(!this.shareImg){
// 				uni.showModal({
// 					content:'分享图片不能为空',
// 					showCancel:false
// 				})
// 				return;
// 			}
			let shareType = 0;
			let shareOPtions = {
				provider: e.id,
				scene: e.type && e.type === 'WXSenceTimeline' ? 'WXSenceTimeline' : 'WXSceneSession', //WXSceneSession”分享到聊天界面，“WXSenceTimeline”分享到朋友圈，“WXSceneFavorite”分享到微信收藏     
				type: shareType,
				success: (e) => {
					uni.showModal({
						content: '分享成功',
						showCancel:false
					})
				},
				fail: (e) => {
					uni.showModal({
						content: e.errMsg,
						showCancel:false
					})
				},
				complete:function(){
					// console.log('分享操作结束!')
				}
			}
			
			shareOPtions.summary = this.shareContent ? this.shareContent : ''
			shareOPtions.imageUrl = this.shareImg ? this.shareImg : ''
			shareOPtions.title = this.shareTitle ? this.shareTitle : ''
			shareOPtions.href = this.shareHref ? this.shareHref : ''
			
			if(shareOPtions.type === 0 && plus.os.name === 'iOS'){//如果是图文分享，且是ios平台，则压缩图片 
				shareOPtions.imageUrl = this.shareImg//await this.compress() //目前压缩失败暂时不压缩，保证商品图片大小在20k以内
			}
			if(shareOPtions.type === 1 && shareOPtions.provider === 'qq'){//如果是分享文字到qq，则必须加上href和title
				shareOPtions.href = this.shareHref
				shareOPtions.title = this.shareTitle
			}
			uni.share(shareOPtions);	
		},
		// 压缩图片 图文分享要求分享图片大小不能超过20Kb
		compress () {
			// console.log('开始压缩');
			let img = this.shareImg;
			return new Promise((res) => {
				var localPath = plus.io.convertAbsoluteFileSystem(img.replace('file://', ''));
				// console.log('after' + localPath);
				// 压缩size
				plus.io.resolveLocalFileSystemURL(localPath, (entry) => {
					entry.file((file) => {// 可通过entry对象操作图片 
						// console.log('getFile:' + JSON.stringify(file));
						if(file.size > 20480) {// 压缩后size 大于20Kb
							plus.zip.compressImage({
								src: img,
								dst: img.replace('.jpg', '2222.jpg').replace('.JPG', '2222.JPG'),
								width: '10%',
								height: '10%',
								quality: 1,
								overwrite: true
							}, (event) => {
								// console.log('success zip****' + event.size);
								let newImg = img.replace('.jpg', '2222.jpg').replace('.JPG', '2222.JPG');
								res(newImg);
							}, function(error) {
								uni.showModal({
									content:'分享图片太大,需要请重新选择图片!',
									showCancel:false
								})
							});
						}
					});
				}, (e) => {
					// console.log('Resolve file URL failed: ' + e.message);
					uni.showModal({
						content:'分享图片太大,需要请重新选择图片!',
						showCancel:false
					})
				});
			})
		}
	}
}	
</script>

<style>
.share-pop{
	height: 300upx;
	width: 100%;
	display: flex;
}
.share-item{
	flex: 1;
	text-align: center;
	font-size: 26upx;
	color: #333;
	padding: 20upx 0;
}
.share-item image{
	width: 80upx;
	height: 80upx;
	margin: 20upx;
}	
.share-item .btn{
	line-height: 1;
	display: block;
	font-size: 26upx;
	background-color: #fff;
}
</style>
