<template>
	<view>
		<view class="picker-mask" @click="closePicker" catchtouchmove="true" v-show="pickerShow"  ></view>
		<view class="picker-content" :class="{'pickerShow':pickerShow}" >
			<view class="picker-button">
				<text @click="closePicker">取消</text>
				<text @click="confirm">确定</text>
			</view>
			<!-- 三列选择-联动 -->
			<picker-view class="picker-view" indicator-class="picker-view-selected-three" :value="pickerIndex" @change="pickerViewChangeThree">
				<picker-view-column>
					<view class="picker-item" v-for="(item, index) in pickerList" :key="index">{{item.label}}</view>
				</picker-view-column>
				<picker-view-column >
					<view class="picker-item" v-for="(item, index) in pickerList[pickerIndex[0]].children" :key="index">{{item.label}}</view>
				</picker-view-column>
				<picker-view-column>
					<view class="picker-item" v-for="(item, index) in pickerList[pickerIndex[0]].children[pickerIndex[1]].children"
					 :key="index">{{item.label}}</view>
				</picker-view-column>
			</picker-view>
		</view>
	</view>
</template>

<script>
	// mode所有类型
	let pickerModeArray = ["one", "two_linkage", "two_nolinkage", "three"];

	export default {
		name: "area-picker",
		props: {
			areaId:{
				type: Number,
				required: true,
			},
			// 默认picker选中项索引
			defaultIndex: {
				type: Array,
				required: true,
				validator: (value) => {
					if (value.length > 0 && value.length <= 3) {
						return true;
					}
					return false;
				}
			}
		},
		data() {
			return {
				pickerIndex: [0,0,0], // picker索引值
				pickerShow: false,
				region: ['河南省', '郑州市', '中原区'], //开户行地区
				provinceKey:-1,//省份id
				cityKey:-1,//市id
				areaKey:-1,//区域id
				selectedData:[],
				pickerList:this.$db.get("areaList"),
				province:this.$db.get("areaList"),
			};
		},
		created() {
			this.init();
		},
		watch: {
			// 匹配选中索引
			mode() {
				this.pickerIndex = this.defaultIndex;
			}
		},
		methods: {
			init(){
				this.province = this.$db.get("areaList");
				//查找省市区 id
				this.getFullPath(this.areaId,this.province);
				this.pickerIndex = [this.provinceKey,this.cityKey,this.areaKey];
			},
			//倒查城市信息
			getFullPath(id,data){
				for(var i = 0;i<data.length;i++){
					if(id == data[i].value){
						if(!data[i].children){
							this.areaKey = i;
							return true;
						}else if(data[i].hasOwnProperty("children")){
							if(data[i].children[0] && !data[i].children[0].children){
								this.cityKey = i;
								return true;
							}else{
								this.provinceKey = i;
								return true;
							}
						}
					}else{
						if(data[i].hasOwnProperty("children")){
							if(data[i].children[0]!==undefined){
								if(data[i].children[0].hasOwnProperty("children")){
									this.provinceKey = i;
								}else{
									this.cityKey = i;
								}
							}
							if(typeof data[i].children !='undefined' ){
								var res = this.getFullPath(id,data[i].children);
								if(res){
									return true;
								}
							}
						}
					}
				}
			},
			// 三列联动选项变化
			pickerViewChangeThree(e) {
				let changeValue = e.detail.value;
				
				// 超规处理
				if (this.pickerList[changeValue[0]].children.length - 1 < changeValue[1]) {
					changeValue[1] = this.pickerList[changeValue[0]].children.length - 1;
				}
				if (this.pickerList[changeValue[0]].children[changeValue[1]].children.length - 1 < changeValue[2]) {
					changeValue[2] = this.pickerList[changeValue[0]].children[changeValue[1]].children.length - 1;
				}
				this.pickerIndex = changeValue;
			},
			// 显示组件
			showPicker() {
				// 隐藏软件盘
				uni.hideKeyboard();
				this.init();
				this.pickerShow = true;
			},
			// 确定事件——返回选中项的数组索引（也可以自定义其他返回数据，不过返回索引通用性更强）
			confirm() {
				this.pickerShow = false;
				this.selectedData = [
					{
						id:this.province[this.pickerIndex[0]].value,
						name:this.province[this.pickerIndex[0]].label,
					},
					{
						id:this.province[this.pickerIndex[0]].children[this.pickerIndex[1]].value,
						name:this.province[this.pickerIndex[0]].children[this.pickerIndex[1]].label,
					},
					{
						id:this.province[this.pickerIndex[0]].children[this.pickerIndex[1]].children[this.pickerIndex[2]].value,
						name:this.province[this.pickerIndex[0]].children[this.pickerIndex[1]].children[this.pickerIndex[2]].label,
					},
				];
				this.$emit("onConfirm", this.selectedData);
			},
			// 隐藏组件
			closePicker() {
				this.pickerShow = false;
			}
		}
	}
</script>

<style>
	.picker-mask {
		position: fixed;
		top: 0;
		right: 0;
		left: 0;
		bottom: 0;
		z-index: 50;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.3);
	}

	.picker-content {
		flex-direction: column;
		position: fixed;
		bottom: 0;
		left: 0;
		z-index: 100;
		width: 100%;
		height: 600upx;
		background-color: #FFFFFF;
		transform: translateY(100%);
		transition: all 200ms ease;
	}

	.pickerShow {
		transform: translateY(0) !important;
	}

	.picker-content .picker-button {
		justify-content: space-between;
		height: 80upx;
		line-height: 80upx;
	}

	.picker-button text {
		width: 180upx;
		font-size: 28upx;
		font-weight: 500;
		display: block;
		text-align: center;
		overflow: hidden;
	}

	.picker-button text:first-child {
		color: #A1A1A1;
		float: left;
	}

	.picker-button text:last-child {
		color: #FF7159;
		float: right;
	}

	.picker-content .picker-view {
		width: 100%;
		height: 500upx;
	}

	.picker-content .picker-view-selected-one,
	.picker-content .picker-view-selected-two,
	.picker-content .picker-view-selected-three {
		height: 68upx;
		line-height: 68upx;
		border-top: #1AAD19 1upx solid;
		border-bottom: #1AAD19 1upx solid;
	}

	.picker-content .picker-view-selected-one {
		position: relative;
		left: 25%;
		width: 50%;
	}

	.picker-content .picker-view-selected-two {
		position: relative;
		left: 15%;
		width: 70%;
	}

	.picker-content .picker-view-selected-three {
		position: relative;
		left: 5%;
		width: 90%;
	}

	.picker-view .picker-item {
		width: 100%;
		height: 34px;
		line-height: 34px;
		font-size: 15px;
		font-weight: 600;
		display: block;
		text-align: center;
	}
</style>
