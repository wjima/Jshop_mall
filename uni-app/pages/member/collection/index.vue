<template>
	<view class="content">
		<view class="collection" v-if="list.length">
			<view class="container_of_slide" 
			v-for="(item, index) in list" 
			:key="index"
			>
				<view class="slide_list" 
				@touchstart="touchStart($event,index)" 
				@touchend="touchEnd($event,index)" 
				@touchmove="touchMove($event,index)"
				@tap="recover(index)" 
				:style="{transform:'translate3d('+item.slide_x+'px, 0, 0)'}"
				v-if="item.goods"
				>
					<view class="now-message-info" hover-class="uni-list-cell-hover" :style="{width:Screen_width+'px'}" 
					@click="goodsDetail(item.goods_id)">
						<view class="icon-circle">
							<image class='goods-img' :src="item.goods.image_url" mode="aspectFill"></image>
						</view>
						
						<view class="list-right">
							<view class="list-title">{{ item.goods.name }}</view>
							<view class="red-price">￥{{ item.goods.price }}</view>
							<view class="list-detail">{{ item.ctime }}</view>
						</view>
						<view class="list-right-1">
							<image class='cell-ft-next icon' src='/static/image/right.png'></image>
						</view>
					</view>
					<view class="group-btn">
						<view class="removeM btn-div" @tap="collect(index)">
							取消
						</view>
					</view>
					<view style="clear:both"></view>
				</view>
			</view>
			<uni-load-more
			:status="loadStatus"
			></uni-load-more>
		</view>
		<view class="collection-none" v-else>
			<image class="collection-none-img" src="/static/image/order.png" mode=""></image>
		</view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
import { goods } from '@/config/mixins.js'
	export default {
		mixins: [ goods ],
		components: {
			uniLoadMore
		},
		computed: {
			Screen_width() {
				return uni.getSystemInfoSync().windowWidth;
			}
		},
		data() {
			return {
				visible: false,
				start_slide_x: 0,
				btnWidth: 0,
				startX: 0,
				LastX: 0,
				startTime: 0,
				screenName: '',
				page: 1,
				limit: 10,
				list: [], // 商品浏览足迹
				loadStatus: 'more'
			};
		},
		onLoad () {
			this.goodsCollectionList()
		},
		onShow() {
			const res = uni.getSystemInfoSync();
		},
		onReachBottom () {
			if (this.loadStatus === 'more') {
				this.goodsCollectionList()
			}
		},
		methods: {
			goodsCollectionList () {
				let data = {
					page: this.page,
					limit: this.limit
				}

				this.loadStatus = 'loading'
				
				this.$api.goodsCollectionList(data, res => {
					if (res.status) {
						let _list = res.data.list
						_list.forEach (item => {
							this.$set(item, 'slide_x', 0)
							item.ctime = this.$common.timeToDate(item.ctime)
						})
						this.list = [...this.list, ..._list]
						
						if (res.data.count > this.list.length) {
							this.page ++
							this.loadStatus = 'more'
						} else {
							this.loadStatus = 'noMore'
						}
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			},
			cancelEvent(){
				this.visible = false
			},
			// 滑动开始
			touchStart(e, index) {
				this.startCilentY = e.touches[0].clientY;
				//记录手指放上去的时间
				this.startTime = e.timeStamp;
				//记录滑块的初始位置
				this.start_slide_x = this.list[index].slide_x;
				// 按钮宽度
				// #ifndef MP-TOUTIAO
				uni.createSelectorQuery()
					.in(this)
					.selectAll('.group-btn')
					.boundingClientRect()
					.exec(res => {
						if (res[0] != null) {
							this.btnWidth = res[0][index].width * -1;
						}
					});
				// #endif

				// #ifdef MP-TOUTIAO
				tt.createSelectorQuery()
					.selectAll(".group-btn")
					.boundingClientRect(rects => {
						rects.forEach(rect => {
							this.btnWidth = rect.width * -1;
						});
					})
					.exec();
				// #endif 
				// 记录上一次开始时手指所处位置
				this.startX = e.touches[0].pageX;
				// 记录上一次手指位置
				this.lastX = this.startX;
				//初始化非当前滑动消息列的位置
				this.list.forEach((item, eq) => {
					if (eq !== index) {
						item.slide_x = 0;
					}
				});
			},
			// 滑动中
			touchMove(e, index) {
				var endCilentY = e.touches[0].clientY; 
				var moveClientY = endCilentY - this.startCilentY;
				if (this.direction === 'Y' || Math.abs(moveClientY ) > 20 || e.currentTarget.dataset.disabled === true) { this.direction = ''; return; }
				const endX = e.touches[0].pageX;
				const distance = endX - this.lastX;
				// 预测滑块所处位置
				const duang = this.list[index].slide_x + distance;
				// 如果在可行区域内
				if (duang <= 0 && duang >= this.btnWidth) {
					this.list[index].slide_x = duang;
				}
				// 此处手指所处位置将成为下次手指移动时的上一次位置
				this.lastX = endX;
			},
			// 滑动结束
			touchEnd(e, index) {
				let distance = 10;
				const endTime = e.timeStamp;
				const x_end_distance = this.startX - this.lastX;
				if (Math.abs(endTime - this.startTime) > 200) {
					distance = this.btnWidth / -2;
				}
				// 判断手指最终位置与手指开始位置的位置差距
				if (x_end_distance > distance) {
					this.list[index].slide_x = this.btnWidth;
				} else if (x_end_distance < distance * -1) {
					this.list[index].slide_x = 0;
				} else {
					this.list[index].slide_x = this.start_slide_x;
				}
			},
			// 点击回复原状
			recover(index) {
				this.list[index].slide_x = 0;
			},
			// 取消收藏
			collect (index) {
				let data = {
					goods_id: this.list[index].goods_id
				}
				
				this.$api.goodsCollection(data, res => {
					if (res.status) {
						this.$common.successToShow(res.msg, () => {
							this.$nextTick(() => {
								this.list.splice(index, 1)
							})
						})
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			}
		}
	};
</script>

<style scoped>
.collection .goods-img{
	width: 150upx;
	height: 150upx;	
}
.container_of_slide {
	width: 100%;
	overflow: hidden;
}
.slide_list {
	transition: all 100ms;
	transition-timing-function: ease-out;
	min-width: 200%;
}
.now-message-info {
	box-sizing:border-box;
	display: flex;
	align-items: center;
	font-size: 16px;
	clear:both;
	padding: 20upx 26upx;
	margin-bottom: 2upx;
	background: #FFFFFF;
}
.now-message-info,
.group-btn {
	float: left;
}
.group-btn {
	display: flex;
	flex-direction: row;
	height: 190upx;
	min-width: 100upx;
	align-items: center;

}
.group-btn .btn-div {
	height: 190upx;
	color: #fff;
	text-align: center;
	padding: 0 50upx;
	font-size: 34upx;
	line-height: 190upx;
}
.group-btn .top {
	background-color: #FFAA33;
}
.group-btn .removeM {
	background-color: #ff3b44;
}
.icon-circle{
	width:150upx;
	height: 150upx;
	float: left;
}
.list-right{
	float: left;
	margin-left: 25upx;
	height: 150upx;
}
.list-right-1{
	float: right;
	color: #A9A9A9;
}
.list-title{
	width: 490upx;
	line-height:1.5;
	overflow:hidden;
	color:#333;
	display:-webkit-box;
	-webkit-box-orient:vertical;
	-webkit-line-clamp:2;
	overflow:hidden;
	font-size: 26upx;
	color: #333;
	min-height: 80upx;
}
.list-detail{
	width: 460upx;
	font-size: 24upx;
	color: #a9a9a9;
	display:-webkit-box;
	-webkit-box-orient:vertical;
	-webkit-line-clamp:1;
	overflow:hidden;
}
.collection-none{
	text-align: center;
	padding: 200upx 0;
}
.collection-none-img{
	width: 274upx;
	height: 274upx;
}
</style>
