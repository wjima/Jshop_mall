<template>
	<view class="content">
		<view class="cont-t">
			<view class="change">
				<view class="change-item" v-for="(item,index) in list" :key="index" :class="{ active: index == ins }" @click="active(index)">
					{{item}}
				</view>
			</view>
		</view>
		<view class="cont-c">
			<view class="c-l">
				<image src="/static/image/avatar-bgi.png" mode="" class="avatar-bgi"></image>
				<image src="/static/image/tip.png" mode="" class="avatar"></image>
			</view>
			<view class="tj">
				<view class="tj-item">
					<view class="fsz24 color-9 tj-t">
						<text class="fsz-48 color-0">{{total.steps || ''}}</text>步
					</view>
					<view class="fsz24 color-9">
						总计
					</view>
				</view>
				<view class="line"></view>
				<view class="tj-item">
					<view class="fsz24 color-9 tj-t">
						<text class="fsz-48 color-0">{{total.course || ''}}</text>公里
					</view>
					<view class="fsz24 color-9">
						总计
					</view>
				</view>
			</view>
		</view>
		<view class="calendar">
			  <my-sign-calendar
			    ref="calendar"
			    :signData="signData"
			    :signList="signList"
			    @showCount="showCount"
				@change="change"
			  >
			  </my-sign-calendar>
		</view>
		<view class="count">
			<view class="count-c">
				<view class="title" v-if="ins == 0">
					{{date.year || ''}}-{{date.month || ''}}-{{date.day || ''}}步数统计
				</view>
				<view class="title" v-else>
					{{date.year || ''}}-{{date.month || ''}}步数统计
				</view>
				<view class="count-item">
					完成步数：{{current.steps || ''}}步
				</view>
				<view class="count-item">
					运动历程：{{current.course || ''}}公里
				</view>
				<!-- <view class="count-item">
					燃烧：500卡
				</view> -->
			</view>
		</view>
	</view>
</template>

<script>
	export default{
		data(){
			return{
				ins:0,
				list:["月报表","年报表"],
				signData: {
					start: '2018-08-01', // 开始的日期（yyyy-mm-dd），默认‘2019-08-01’
					ismake: true, // 是否开启补签
					type: 'month' // 日历模式（年 => year，月 => month，周 => week）
				},
				signList: {
					type:"month",
					list:[{
						year:parseInt(new Date().getFullYear()),
						month:parseInt(new Date().getMonth() + 1),
						day:parseInt(new Date().getDate())
					}]
				}, 
				today: parseInt(new Date().getDate()), //本日
				toMonth: parseInt(new Date().getMonth() + 1), //本月
				toYear: parseInt(new Date().getFullYear()), //本年
				total:{},
				current:{},
				date:{
					year:parseInt(new Date().getFullYear()),
					month:parseInt(new Date().getMonth() + 1),
					day:parseInt(new Date().getDate())
				}
			}
		},
		
		onLoad(options) {
			console.log(options);
			if(!options.start_time){
				uni.navigateBack({
					delta: 1,
				})
			}
			this.signData.start=options.start_time;
			let data = {
				type:2,
				year:this.toYear,
				month:this.toMonth
			}
			this.showCount(data,"total");
			let datas = {
				type:3,
				year:this.toYear,
				month:this.toMonth,
				day:this.today
			}
			this.showCount(datas,"day");
		},
		methods:{
				active(index){
					this.ins = index;
					if(this.ins == 0){
						this.signData.type = "month"
						let data = {
							type:2,
							year:this.toYear,
							month:this.toMonth
						}
						this.showCount(data,"total");
						let datas = {
							type:3,
							year:this.toYear,
							month:this.toMonth,
							day:this.today
						}
						this.date=datas
						this.showCount(datas,"day");
						this.signList.type = "month";
						setTimeout(()=>{
							this.$refs.calendar.signViewShow()
						},500)
					}else{
						this.signData.type = "year"
						let data = {
							type:1,
							year:this.toYear
						}
						this.showCount(data,"total");
						let datas = {
							type:2,
							year:this.toYear,
							month:this.toMonth
						}
						this.date=datas
						this.showCount(datas,"day");
						this.signList.type = "year";
						setTimeout(()=>{
							this.$refs.calendar.signViewShow()
						},500)
					}
				},
				change(data){
					// console.log("data",data);
					let datas={}
					if(data.type == "month"){
						datas = {
							type:2,
							year:data.data.year,
							month:data.data.month
						}
					}else{
						datas = {
							type:1,
							year:data.data.year
						}
					}
					this.showCount(datas,"total");
				},
			  // 签到回调
			  showCount(data,type) {
			    // console.log(data)
				if(data.type == "click"){
					let tmp = {
						year:data.data.year,
						month:data.data.month,
						day:data.data.day,
					}
					this.date = tmp
					type = "current"
					if(data.data.type == "day"){
						tmp.type = 3;
						data = tmp;
					}else{
						tmp.type = 2;
						data = tmp;
					}
				}
				this.$api.werunStatistic(data,res=>{
					if(res.status){
						console.log("步数更新成功：",res.data)
							
						if(type == "total"){
							this.total = res.data;
						}else{
							this.current = res.data;
						}
					}else{
						_this.$common.errorToShow(res.msg);
					}
				})		   
			  },
			  getTotal(data){
				  
			  },
			  getDetails(){
			  				  
			  },
			  
		}
	}
</script>

<style lang="scss" scoped>
	.active{
		color: #51A2CC!important;
		background-color: #fff!important;
	}
	.content{
		.cont-t{
			background-color: #64C9FD;
			height: 200rpx;
			width: 100%;
			border-radius: 0 0 40rpx 40rpx;
			padding: 30rpx 20rpx;
			display: flex;
			flex-direction: row-reverse;
			
			.change{
				.change-item{
					width: 130rpx;
					height: 60rpx;
					background-color: #51A2CC;
					text-align: center;
					line-height: 60rpx;
					color: #fff;
					display: inline-block;
					&:first-child{
						border-radius: 100rpx 0 0 100rpx;
					}
					&:last-child{
						border-radius: 0 100rpx 100rpx 0;
					}
					
				}
			}
		}
		.cont-c{
			width: 710rpx;
			height: 180rpx;
			background-color: #fff;
			border-radius: 16rpx;
			position: relative;
			top: -80rpx;
			left: 50%;
			transform: translateX(-50%);
			.c-l{
				height: 180rpx;
				width: 198rpx;
				position: relative;
				.avatar-bgi{
					height: 100%;
					width: 100%;
				}
				.avatar{
					width: 124rpx;
					height: 124rpx;
					border-radius: 50%;
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%,-50%);
				}
			}
			.tj{
				position: absolute;
				left: 198rpx;
				top: 50%;
				transform: translateY(-50%);
				display: flex;
				width: 72%;
				.tj-item{
					height: 100%;
					width: 50%;
					text-align: center;
					.tj-t{
						margin-bottom: 12rpx;
					}
					
				}
				.line{
					width: 2rpx;
					height: 32rpx;
					background-color: #ccc;
					position: absolute;
					top: 50%;
					right: 50%;
				}
				.line-r{
					right: 0;
				}
			}
		}
		.calendar{
			padding: 20rpx;
			margin-top: -50rpx;
		}
		.count{
			padding: 0 20rpx;
			.count-c{
				background-color: #fff;
				padding: 28rpx 20rpx;
				border-radius: 16rpx;
				.title{
					font-size: 32rpx;
					color: #000;
					font-weight: 700;
					margin-bottom: 32rpx;
				}
				.count-item{
					font-size: 28rpx;
					color: #000;
					text-align: center;
					height: 40rpx;
					line-height: 40rpx;
					
				}
			}
			
		}
	}
	.fsz-48{
		font-size: 48rpx;
	}
	.color-0{
		color: #000;
	}
</style>
