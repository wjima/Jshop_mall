<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group'>
				<view class='cell-item'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>名称</view>
					</view>
					<view class='cell-item-bd'>
						<input class='cell-bd-input' placeholder='请输入店铺名称' v-model="store_name"></input>
					</view>
				</view>
				<view class='cell-item user-head'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>图标</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next user-head-img have-none' mode="aspectFill" :src="logo" @click="uploadLogo"></image>
					</view>
				</view>
			</view>
			<view class='cell-group'>
				<view class='cell-item right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>店招</view>
					</view>
				</view>
				<view class="">
					<view class="evaluate-c-b">
						<view class="goods-img-item" v-for="(item, key) in images" :key="key">
							<image class="del" src="/static/image/del.png" mode="" @click="delImage(item)"></image>
							<image class="" :src="item.url" mode="" @click="clickImg(item.url)"></image>
						</view>
						<view class="upload-img" v-show="isImage" @click="upImage">
							<image class="icon" src="/static/image/camera.png" mode=""></image>
							<view class="">上传照片</view>
						</view>
					</view>
				</view>
			</view>
			<view class='cell-group'>
				<view class='cell-item right-img'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>简介</view>
					</view>

				</view>
				<view class="cell-textarea ">
					<textarea v-model="store_desc" placeholder="请您在此描述问题(最多200字)" maxlength="200" />
					</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-b"  hover-class="btn-hover2" @click="submitHandler()">保存</button>
		</view>
	</view>
</template>

<script>
export default {
    data() {
        return {
			title: 'picker',
			logo: '',
			index: 2,
			images:[],
			image_max: 1,
			store_name:'',//店铺名称
			store_logo:'',
			store_banner:'',
			store_desc:'',//店铺介绍
			store_logo_src:'',
			store_banner_src:'',
        }
    },
    computed: {
		isImage() {
			let num = this.image_max - this.images.length;
			if(num > 0) {
				return true;
			}else{
				return false;
			}
		}
    },
    methods: {
		// 用户上传头像
		uploadLogo () {
			this.$api.uploadFiles(res => {
				if (res.status) {
					this.store_logo = res.data.image_id;
					this.logo = res.data.url;
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		},
		// 保存资料
		submitHandler() {
			if(!this.store_name||this.store_name==''){
				this.$common.errorToShow('请填写店铺名称');
				return;
			}
			if(this.images.length <= 0){
				this.$common.errorToShow('请上传店招');
				return;
			}
			if(!this.store_logo){
				this.$common.errorToShow('请上传图标');
				return;
			}
			this.store_banner = this.images[0].image_id;
			
			this.$api.setStore({
					store_name: this.store_name,
					store_logo: this.store_logo,
					store_banner: this.store_banner,
					store_desc: this.store_desc
				}, res => {
					if(res.status){
						this.$common.successToShow(res.msg, result => {
							uni.navigateBack({
								delta: 1
							});
						});
					}else{
						this.$common.errorToShow(res.msg);
					}
				}
			);
		},
				//上传图片
		upImage() {
			let num = this.image_max - this.images.length;
			if(num > 0){
				this.$api.uploadImage(num, res => {
					if(res.status){
						this.images.push(res.data);
						this.$common.successToShow(res.msg);
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			}
		},
		//删除图片
		delImage(e) {
			let newImages = [];
			for(var i = 0; i < this.images.length; i++) {
				if(this.images[i].image_id != e.image_id){
					newImages.push(this.images[i]);
				}
			}
			this.images = newImages;
		},
		// 图片点击放大
		clickImg (img) {
			// 预览图片
			uni.previewImage({
				urls: img.split()
			});
		}
    },
	onLoad: function() {
		var _this = this;
		_this.$api.getDistributioninfo({check_condition:false}, function(res) {
			if (res.status) {
				_this.store_name = res.data.store_name;
				_this.store_desc = res.data.store_desc;
				_this.store_logo = res.data.store_logo;
				if( res.data.store_logo){
					_this.logo = res.data.store_logo_src;
				}
				_this.store_banner = res.data.store_banner;
				if(_this.store_banner){
					_this.images.push({
						image_id:res.data.store_banner,
						url:res.data.store_banner_src
						});
				}
			} else {
				//报错了
				_this.$common.errorToShow(res.msg);
			}
		});
	}
}
</script>

<style>
.user-head{
	height: 100upx;
}
.user-head-img{
	height: 90upx;
	width: 90upx;
	border-radius: 50%;
}
.cell-hd-title{
	color: #333;
}
.cell-item-bd{
	color: #666;
	font-size: 26upx;
}
.list-goods-name{
	width: 100% !important;
}
.cart-checkbox-item{
	position: relative;
}
.invoice-type .uni-list-cell{
	display: inline-block;
	font-size: 26upx;
	color: #333;
	position: relative;
	margin-left: 50upx;
}
.invoice-type .uni-list-cell>view{
	display: inline-block;
}
.invoice-type-icon{
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
}
.invoice-type-c{
	margin-left: 50upx;
	line-height: 2;
}
.cell-item-ft .cell-bd-input{
	text-align: right;
	width: 500upx;
	font-size: 28upx;
}
.right-img{
	border-bottom: 0;
}
.cell-textarea{
	padding: 0 26upx 20upx;
}
.cell-textarea textarea{
	width: 100%;
	height: 200upx;
	font-size: 26upx;
	color: #333;
}
.evaluate-c-b{
	overflow: hidden;
	padding: 0 20upx;
}
.upload-img{
	width: 146upx;
	height: 146upx;
	margin: 14upx;
	text-align: center;
	color: #999999;
	font-size: 22upx;
	border: 2upx solid #E1E1E1;
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
.cell-textarea textarea{
	background-color: #f8f8f8;
	padding: 12upx 20upx;
	box-sizing: border-box;
}
</style>
