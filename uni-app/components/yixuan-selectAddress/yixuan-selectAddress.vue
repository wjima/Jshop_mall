<template>
	<view class="wrapper" v-show="isShowMask">
		<transition name="content">
			<view class="content_view" v-show="isShow">
				<view class="title_view">
					<view class="title">{{$t("chooseRegion")}}</view>
					<view class="close_view" @click="hidden">
						<icon class="close_icon" :type="'clear'" size="26" />
					</view>
				</view>
				<uni-segmented-control :current="current_country" :values="items" @clickItem="onClickItem" style-type="text"
				 active-color="#dd524d"></uni-segmented-control>
				<view class="select_top">
					<view class="select_top_item" ref="select_top_item" v-for="(item,index) in dataList" :key="index" @click="select_top_item_click(index)">
						<text class="address_value">{{item}}</text>
					</view>
					<view class="indicator" :style="{ left: indicatorStyleLeft + 'px' }" ref="indicator"></view>
				</view>
				<view class="swiper-box" v-if="current_country==0">
					<swiper class="swiper" :current="currentIndex" @change="swiperChange">
						<swiper-item v-for="(swiper_item,swiper_index) in dataList" :key="swiper_index">
							<view class="swiper-item">
								<scroll-view class="scroll-view-item" scroll-y="true">
									<view class="address_item" v-for="(item,index) in pickerArray[swiper_index]" :key="index" @click="address_item_click(swiper_index,index)">
										<image v-if="selectIndexArr[swiper_index] === index" class="address_item_icon" src="/static/image/gou.png"
										 mode=""></image>
										{{item.label}}
									</view>
								</scroll-view>
							</view>
						</swiper-item>
					</swiper>
				</view>
				<view class="swiper-box" v-if="current_country==1">
					<swiper class="swiper" :current="currentIndex" @change="swiperChange">
						<swiper-item v-for="(swiper_item,swiper_index) in dataList" :key="swiper_index">
							<view class="swiper-item">
								<scroll-view class="scroll-view-item" scroll-y="true">
									<view class="address_item" v-for="(item,index) in pickerArray[swiper_index]" :key="index" @click="address_item_click(swiper_index,index)">
										<image v-if="selectIndexArr[swiper_index] === index" class="address_item_icon" src="/static/image/gou.png"
										 mode=""></image>
										{{item.label}}
									</view>
								</scroll-view>
							</view>
						</swiper-item>
					</swiper>
				</view>
			</view>
		</transition>
		<view class="mask" @click="hidden" v-show="isShowMask"></view>
	</view>
</template>

<script>
	import uniSegmentedControl from '@/components/uni-segmented-control/uni-segmented-control.vue';
	export default {
		components: {
			uniSegmentedControl
		},
		props: {
			// 默认选中项
			defaultAddress: {
				type: String,
				required: true
			}
		},
		data() {
			return {
				isShow: false,
				isShowMask: false,
				dataList: [this.$t("pleaseChoose")],
				currentIndex: 0,
				selectIndexArr: [],
				indicatorStyleLeft: 16,
				pickerList: this.$db.get("areaList"),
				pickerArray: [],
				chooseData: [],
				postal_code: [],
				values: [],
				current_country: 0, //当前国家，0是中国，1是其他
				items: [this.$t("china"), this.$t("foreign")],
				arr: []
			};
		},
		computed: {},
		methods: {
			show() {
				this.isShow = true
				this.isShowMask = true
			},
			hidden() {
				for (let i = 0; i < this.dataList.length; i++) {
					if (this.dataList[i] != this.$t("pleaseChoose")) {
						let item = {}
						item.label = this.dataList[i];
						item.postal_code = this.postal_code[i];
						item.value = this.values[i];
						this.chooseData.push(item)
					}
				}
				//选择成功返回数据
				// console.log(this.dataList);
				// console.log(this.chooseData);
				// this.$emit("selectAddress", this.dataList.join(''))
				// this.$emit("selectAddress", this.dataList)
				this.$emit("selectAddress", this.chooseData)
				// 清空数据
				this.chooseData = [];
				this.isShow = false
				setTimeout(() => {
					this.isShowMask = false
				}, 500);
			},
			select_top_item_click(index) {
				// console.log('select_top_item_click')
				this.currentIndex = index
				this.$nextTick(() => {
					this.changeIndicator(index)
				})

			},
			swiperChange(event) {
				let index = event.detail.current
				this.currentIndex = index

				this.changeIndicator(index)
			},
			changeIndicator(index) {
				let indicatorWidth = 30
				const query = uni.createSelectorQuery().in(this);
				let arr = query.selectAll('.select_top_item .address_value')
				arr.fields({
					size: true,
					scrollOffset: false
				}, data => {

					let itemWidth = data[index]["width"] > 80 ? 70 : data[index]["width"]
					let itemCenterX = 10 + index * 80 + itemWidth / 2
					let left = itemCenterX - indicatorWidth / 2

					// console.log('changeIndicator', itemWidth, index)

					this.indicatorStyleLeft = left

				}).exec();
			},
			address_item_click(swiper_index, index) {
				// console.log(swiper_index,index)
				// console.log(this.pickerArray[swiper_index])
				this.selectIndexArr.splice(swiper_index, 5, index)

				//判断当前是否为最下一级
				if (swiper_index === 0) { //第一级
					let currentObj = {}
					// 如果选择是中国
					if (this.current_country == 0) {
						currentObj = this.pickerList[this.current_country]['children'][index]
					} else {
						currentObj = this.arr[index]
					}

					// this.chooseData.push(currentObj)
					let city = currentObj.label
					this.dataList.splice(swiper_index, 5, city)
					// this.dataList.splice(swiper_index + 1,0,this.$t("pleaseChoose"))
					// this.pickerArray.splice(swiper_index + 1, 1,currentObj["children"])
					//如果只有一级
					if (!currentObj["children"]) {
						let city = currentObj.label
						this.dataList.splice(swiper_index, 1, city)
						// 重新组装数据
						this.postal_code.splice(swiper_index, 1, currentObj.postal_code);
						this.values.splice(swiper_index, 1, currentObj.value);
						for (let i = 0; i < this.dataList.length; i++) {
							let item = {}
							item.label = this.dataList[i];
							item.postal_code = this.postal_code[i];
							item.value = this.values[i];
							this.chooseData.push(item)
						}
						//选择成功返回数据
						// console.log(this.dataList);
						// console.log(this.chooseData);
						// this.$emit("selectAddress", this.dataList.join(''))
						// this.$emit("selectAddress", this.dataList)
						this.$emit("selectAddress", this.chooseData)
						// 清空数据
						this.chooseData = [];

						this.$nextTick(() => {
							this.changeIndicator(swiper_index)
						})

						setTimeout(() => {
							this.isShow = false
							//清空
							// this.dataList= [this.$t("pleaseChoose")];
							// // this.pickerArray=[];
							// this.chooseData=[];
						}, 100);
						setTimeout(() => {
							this.isShowMask = false
							//清空
							// this.dataList= [this.$t("pleaseChoose")];
							// // this.pickerArray=[];
							// this.chooseData=[];
						}, 500);
					} else {
						this.dataList.splice(swiper_index + 1, 0, this.$t("pleaseChoose"))
						this.pickerArray.splice(swiper_index + 1, 1, currentObj["children"])
					}

					// 重新组装数据
					this.postal_code.splice(swiper_index, 5, currentObj.postal_code);
					this.values.splice(swiper_index, 5, currentObj.value);
					setTimeout(() => {
						this.currentIndex = 1
						this.changeIndicator(1)
					}, 50);

				} else {
					let currentAreaArray = this.pickerArray[swiper_index]
					let currentObj = currentAreaArray[index]
					// this.chooseData.push(currentObj)
					let area = currentObj["children"]
					if (area !== undefined) {
						let city = currentObj.label
						this.dataList.splice(swiper_index, 5, city)
						this.dataList.splice(swiper_index + 1, 0, this.$t("pleaseChoose"))
						this.pickerArray.splice(swiper_index + 1, 1, currentObj["children"])
						// 重新组装数据
						this.postal_code.splice(swiper_index, 5, currentObj.postal_code);
						this.values.splice(swiper_index, 5, currentObj.value);
						setTimeout(() => {
							this.currentIndex = swiper_index + 1
							this.changeIndicator(swiper_index + 1)
						}, 50);
					} else { //是最下一级
						let city = currentObj.label
						this.dataList.splice(swiper_index, 1, city)
						// 重新组装数据
						this.postal_code.splice(swiper_index, 1, currentObj.postal_code);
						this.values.splice(swiper_index, 1, currentObj.value);
						for (let i = 0; i < this.dataList.length; i++) {
							let item = {}
							item.label = this.dataList[i];
							item.postal_code = this.postal_code[i];
							item.value = this.values[i];
							this.chooseData.push(item)
						}
						//选择成功返回数据
						// console.log(this.dataList);
						if (this.current_country == 0) {
							let first = {
								country_id: this.$db.get("areaList")[this.current_country].label,
								postal_code: this.$db.get("areaList")[this.current_country].postal_code,
								value: this.$db.get("areaList")[this.current_country].value,
								// label:this.$db.get("areaList")[this.current_country].label
							}
							// console.log(first);
							this.chooseData.unshift(first)
						}
						// console.log(this.chooseData);
						this.$emit("selectAddress", this.chooseData)
						// 清空数据
						this.chooseData = [];

						this.$nextTick(() => {
							this.changeIndicator(swiper_index)
						})

						setTimeout(() => {
							this.isShow = false
							//清空
							// this.dataList= [this.$t("pleaseChoose")];
							// // this.pickerArray=[];
							// this.chooseData=[];
						}, 100);
						setTimeout(() => {
							this.isShowMask = false
							//清空
							// this.dataList= [this.$t("pleaseChoose")];
							// // this.pickerArray=[];
							// this.chooseData=[];
						}, 500);

					}

				}
			},
			// tab点击切换
			onClickItem(index) {
				this.dataList = ['请选择'];
				this.currentIndex = 0;
				this.selectIndexArr = [];
				if (this.current_country !== index) {
					this.current_country = index;
				}

				if (this.current_country == 1) {
					this.pickerArray = [];
					this.pickerArray.push(this.arr)
					this.indicatorStyleLeft = 16
					// this.changeIndicator(0)
					// console.log(this.arr);
					// console.log(this.dataList);
				} else {
					this.indicatorStyleLeft = 16
					// this.changeIndicator(0)
					this.pickerArray = [];
					this.pickerArray.push(this.$db.get("areaList")[this.current_country].children)
				}
			},
		},
		created() {

			this.pickerArray.push(this.$db.get("areaList")[this.current_country].children)
			// console.log(this.pickerArray);

			this.arr = this.$db.get("areaList").splice(1, this.$db.get("areaList").length);
			if(this.$db.get("areaList")[0]){
				this.items[0] = this.$db.get("areaList")[0].label
			}
			

		},
		mounted() {
			// 获取默认的地址回填
			if (this.defaultAddress) {
				this.dataList = this.defaultAddress.split(' ');
			}
			// console.log(this.dataList);
			// console.log(this.pickerList);
		},
	}
</script>

<style lang="scss">
	// 不换行
	@mixin no-wrap() {
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}

	.wrapper {
		z-index: 1999;
		// position: absolute;
		top: -44px;
		left: 0;
		bottom: 0;
		right: 0;
		position: fixed;

		.content_view {
			z-index: 999;
			background: white;
			position: absolute;
			height: 80%;
			left: 0;
			bottom: 0;
			right: 0;
			border-top-left-radius: 20px;
			border-top-right-radius: 20px;

			.title_view {
				height: 12%;
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 0 $uni-spacing-row-sm;

				.title {
					font-size: uni-font-size-sm;
				}

				.close_view {
					height: 60px;
					width: 60px;
					display: flex;
					justify-content: center;
					align-items: center;
				}
			}

			.select_top {
				height: 8%;
				display: flex;
				justify-content: start;
				align-items: center;
				padding: 10px;
				position: relative;
				box-sizing: border-box;
				//flex-wrap: wrap;

				.select_top_item {
					// width: 160rpx;
					width: 80px;
					font-size: 14px;
					@include no-wrap();

					.address_value {
						@include no-wrap();
					}
				}


				.indicator {
					position: absolute;
					width: 30px;
					height: 2px;
					background: $uni-color-error;
					left: 16px;
					bottom: 0;
					transition: left 0.5s ease;
				}
			}

			.swiper {
				height: 100%;
				position: relative;
				left: 0;
				top: 20rpx;
				bottom: 0;
				right: 0;

				.swiper-item {
					height: 100%;

					.scroll-view-item {
						height: 100%;
						padding: 0 10px;

						.address_item {
							padding: 5px 0;
							font-size: 14px;
							display: flex;
							align-items: center;

							.address_item_icon {
								width: 20px;
								height: 20px;
								margin-right: 10px;
							}
						}
					}
				}
			}
		}

		.mask {
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
			background: $uni-text-color-grey;
			opacity: 0.7;
		}
	}

	.content-enter {
		transform: translateY(100%);
	}

	.content-enter-to {
		transform: translateY(0%);
	}

	.content-enter-active {
		transition: transform 0.5s;
	}

	.content-leave {
		transform: translateY(0%);
	}

	.content-leave-to {
		transform: translateY(100%);
	}

	.content-leave-active {
		transition: transform 0.5s;
	}

	.swiper-box {
		height: 70%;
	}

	// .segmented-control{
	// 	width: 30%!important;
	// }
</style>
