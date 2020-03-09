<template>
	<view class="content">
		<view class="ed-head color-6"
		v-if="add.length"
		>
			收货地址：{{ add }}
		</view>
		<view class="ed-body">
			<view v-if="isExpress">
				<view class="ed-body-item"
				v-for="(item, index) in express.data"
				:key="index"
				>
					<view class="edbi-left">
						<view class="edbi-date color-6">
							{{ item.date }}
						</view>
						<view class="edbi-time color-9">
							{{ item.utime }}
						</view>
					</view>
					<view class="edbi-circle last-circle" v-if="item.end">
						<view>收</view>
					</view>
					<view class="edbi-circle"v-else>
						<view></view>
					</view>
					<view class="edbi-right">
						<view class="edbi-title color-3">
							{{ item.title }}
						</view>
						<view class="edbi-content color-9">
							{{ item.content }}
						</view>
					</view>
				</view>
			</view>
			<view class="ed-none" v-else>
				暂无物流信息
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data () {
		return {
			add: '', // 收货地址
			express: {}, // 快递物流信息
		}
	},
	onLoad (options) {
		let params = options.params
		let arr = decodeURIComponent(params).split('&')
		let code, no
		for (var i = 0; i < arr.length; i++) {
		    let key = arr[i].split("=")[0]
		    if (key == 'code') {
		        code = arr[i].split("=")[1]
		    }
		    if (key == 'no') {
		        no = arr[i].split("=")[1]
		    }
			if (key == 'add') {
				this.add = arr[i].split('=')[1]
			}
		}
		
		if (!code || !no) {
			this.$common.errorToShow('缺少物流查询参数', () => {
				uni.navigateBack({
					delta: 1
				})
			})
		} 
		
		this.expressInfo(code, no)
	},
	computed: {
		isExpress () {
			return Object.keys(this.express).length ? true : false
		}
	},
	methods: {
		expressInfo (code, no) {
			let data = {
				code: code,
				no: no
			}
			this.$api.logistics(data, res => {
				if (res.status) {
					let _info = res.data.info
					_info.data.forEach((item, key) => {
						// 日期时间重新格式化处理
						let times = item.time.split(' ')
						this.$set(item, 'date', times[0].substring(5, times[0].length))
						this.$set(item, 'utime', times[1].substring(0, 5))
						
						// 快递信息格式化处理
						let contents = item.context.split('，')
						this.$set(item, 'title', contents[0])
						this.$set(item, 'content', contents[1] ? contents[1] : '')
						
						// 签收状态logo处理
						this.$set(item, 'end', _info.state === 3 && key === 0 ? true : false)
					})
					
					this.express = _info
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		}
	}
}	
</script>

<style>
.ed-head{
	font-size: 30upx;
	padding: 20upx 26upx;
}
.ed-body{
	margin: 0 26upx;
	background-color: #fff;
	box-shadow: 0 0 20upx #ccc;
	padding: 26upx;
}
.ed-body-item{
	overflow: hidden;
	position: relative;	
}
.edbi-left{
	display: inline-block;
	width: 96upx;
	float: left;
	padding: 4upx 0;
}
.edbi-date{
	font-size: 26upx;
}
.edbi-time{
	font-size: 24upx;
}
.edbi-circle{
	display: inline-block;
	width: 18upx;
	height: 18upx;
	border: 2upx solid #ccc;
	border-radius: 50%;
	position: absolute;
	left: 88upx;
	top: 12upx;
	background-color: #fff;
	z-index: 99;
}
.last-circle{
	width: 40upx;
	height: 40upx;
	font-size: 24upx;
	left: 78upx;
	text-align: center;
	line-height: 40upx;
	color: #fff;
	background-color: #FF7159;
	border: none;
	top: 0;
}
.edbi-right{
	display: inline-block;
	width: 550upx;
	float: right;
	border-left: 2upx solid #e8e8e8;
	padding-left: 30upx;
	position: relative;
	padding-bottom: 30upx;
}
.edbi-title{
	font-size: 30upx;
}
.edbi-content{
	font-size: 26upx;
	margin-top: 4upx;
}
.ed-none{
	text-align: center;
	font-size: 26upx;
	color: #666;
	padding: 100upx;
}
</style>
