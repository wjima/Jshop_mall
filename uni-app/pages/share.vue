<template>
	<view class="content">
		<view class="share-top"><img class="share-img" :src="poster" mode="widthFix"/></view>
		<view class="share-bot">
			<button class="btn btn-b" v-if="weiXinBrowser">长按图片保存到手机</button>
			<button class="btn btn-b" @click="savePoster()" v-else>保存到本地</button>
			<button class="btn btn-w" @click="goBack()">返回</button>
		</view>
	</view>
</template>
<script>
export default {
	data() {
		return {
			poster: ''
		};
	},
	onLoad(options) {
		this.poster = decodeURIComponent(options.poster);
		console.log(this.poster);
	},
	computed: {
		weiXinBrowser () {
			return this.$common.isWeiXinBrowser()
		}
	},
	methods: {
		goBack() {
			uni.navigateBack({
				delta: 1
			});
		},
		// 保存海报到本地
		savePoster() {
			let _this = this;
			// #ifdef H5 
			_this.downloadIamge(_this.poster, 'image');
			// #endif

			// #ifdef MP || MP-ALIPAY || APP-PLUS || APP-PLUS-NVUE
			_this.downloadImageOfMp(_this.poster)
			// #endif
		},
		//下载图片地址和图片名
		downloadIamge(imgsrc, name) {
			var image = new Image();
			// 解决跨域 Canvas 污染问题
			image.setAttribute('crossorigin', 'anonymous');
			image.onload = () => {
				var canvas = document.createElement('canvas');
				canvas.width = image.width;
				canvas.height = image.height;
				var context = canvas.getContext('2d');
				context.drawImage(image, 0, 0, image.width, image.height);
				var url = canvas.toDataURL('image/png'); //得到图片的base64编码数据
				var a = document.createElement('a'); // 生成一个a元素
				var event = new MouseEvent('click'); // 创建一个单击事件
				a.download = name || 'photo'; // 设置图片名称
				a.href = url; // 将生成的URL设置为a.href属性
				a.dispatchEvent(event); // 触发a的单击事件
			};
			image.src = imgsrc;
		},
		downloadImageOfMp (image) {
			let _this = this
			
			// #ifdef APP-PLUS || APP-PLUS-NVUE
			uni.downloadFile({
				url: image,
				success (res) {
					uni.saveImageToPhotosAlbum({
						filePath: image,
						// filePath: res.tempFilePath,
						success() {
							_this.$common.successToShow('保存成功')
						},
						fail() {
							_this.$common.errorToShow('图片保存失败')
						}
					});
				},
				fail () {
					_this.$common.errorToShow('下载失败')
				}
			})
			// #endif
			
			// #ifdef MP
			uni.authorize({
				scope: 'scope.writePhotosAlbum',
				success() {
					// 先下载到本地
					uni.downloadFile({
						url: image,
						success (res) {
							uni.saveImageToPhotosAlbum({
								filePath: res.tempFilePath,
								success() {
									_this.$common.successToShow('保存成功')
								},
								fail() {
									_this.$common.errorToShow('图片保存失败')
								}
							});
						},
						fail () {
							_this.$common.errorToShow('下载失败')
						}
					})
				},
				fail() {
					//console.log('授权失败')
				}
			})
			// #endif
		}
	}
};
</script>
<style>
.share-top {
	margin-bottom: 50upx;
	padding-top: 50upx;
	text-align: center;
}
.share-img {
	box-shadow: 0 0 20upx #ccc;
	width: 80%;
}
.share-bot {
	width: 80%;
	margin: 0 auto;
}
.share-bot .btn {
	width: 100%;
	margin: 20upx 0;
}
</style>
