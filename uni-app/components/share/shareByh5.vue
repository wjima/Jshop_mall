<template>
	<view style="width: 100%;height: 300upx;background: #FFFFFF;position: absolute;left:0;bottom: 0;">
		<view class="share-pop">
			<view class="share-item" @click="copyUrl()" v-show="!ifwx">
				<image src="/static/image/share-f.png" mode=""></image>
				<view class="">复制链接</view>
			</view>
			<view class="share-item" @click="createPoster()">
				<image src="/static/image/poster.png" mode=""></image>
				<view class="">生成海报</view>
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
		},
		ifwx:{
			type: Boolean
		}
	},
	mounted () {
		/**
		 * 
		 * H5端分享两种 (微信浏览器内引导用户去分享, 其他浏览器)
		 * 
		 */
	},
	methods: {
		// 关闭弹出层
		close () {
			this.$emit('close')
		},
		// 生成海报
		createPoster () {
            let data = {};
            if (this.shareType == 1) {
                //商品
                data = {
                    page: 2, //商品
                    url: h5Url + 'pages/share/jump',
                    params: {
                        goods_id: this.goodsId
                    },
                    type: 3,//海报
                    client: 1
                }
                let userToken = this.$db.get('userToken')
                if (userToken) {
                	data.token = userToken
                }
            } else if(this.shareType == 3) {
                //拼团
                data = {
                    page: 3, //商品
                    url: h5Url + 'pages/share/jump',
                    params: {
                        goods_id: this.goodsId,
                        group_id: this.groupId,
                        team_id: this.teamId
                    },
                    type: 3,//海报
                    client: 1
                }
                let userToken = this.$db.get('userToken')
                if (userToken) {
                	data.token = userToken
                }
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
		copyUrl () {
			let data = {};
			if (this.shareType == 1) {
			    data = {
			        page: 2, //商品
			        url: h5Url + 'pages/share/jump',
			        params: {
			            goods_id: this.goodsId
			        },
			        type: 1,//URL
			        client: 1
			    }
			    let userToken = this.$db.get('userToken')
			    if (userToken) {
			    	data.token = userToken
			    }
			} else if(this.shareType == 3) {
			    data = {
			        page: 3, //拼团
			        url: h5Url + 'pages/share/jump',
			        params: {
			            goods_id: this.goodsId,
			            group_id: this.groupId,
			            team_id: this.teamId
			        },
			        type: 1,//URL
			        client: 1
			    }
			    let userToken = this.$db.get('userToken')
			    if (userToken) {
			    	data.token = userToken
			    }
			}
            let _this = this;
			_this.$api.share(data, res => {
				if(res.status) {
					uni.setClipboardData({
						data: res.data,
						success:function(data){
							_this.$common.successToShow('复制成功');
						}, 
						fail:function(err){
							_this.$common.errorToShow('复制分享URL失败');
						}
					})
				} else {
					_this.$common.errorToShow('复制分享URL失败');
				}
			});
		},
		// 分享操作
		share () {
			// h5分享 判断是否是微信浏览器 引导用户完成分享操作
			
			// 其他浏览器的分享 
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
