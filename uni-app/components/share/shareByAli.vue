<template>
	<view style="width: 100%;height: 300upx;background: #FFFFFF;position: absolute;left:0;bottom: 0;">
		<view class="share-pop">
			<view class="share-item">
				<button class="btn" open-type="share">
					<image src="/static/image/share-f.png" mode=""></image>
					<button class="btn" open-type="share">分享给好友</button>
				</button>
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
import { apiBaseUrl } from '@/config/config.js'
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
	methods: {
		// 关闭弹出层
		close () {
			this.$emit('close')
		},
		// 生成海报
		createPoster () {
			let data = {
			    page: this.shareType,
			    url: '/pages/share/jump',
			    type: 3,
			    client: 6
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
