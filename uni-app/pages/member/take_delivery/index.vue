<template>
	<view class="content">
		<view class="content-top">
			<view class="ad" >
				<image class="ad-img" src="/static/image/member-bg.png" mode="widthFix" ></image>
			</view>
			<view class='search'>
				<view class='search-c'>
					<image class='icon search-icon' src='/static/image/zoom.png'></image>
					<input class='search-input' placeholder-class='search-input-p' placeholder='请输入完整提货单号、订单号、提货手机号' v-model="key"></input>
				</view>
				<button class="btn btn-g" hover-class="btn-hover2" @click="search">查询</button>
			</view>
			<view v-if="allData.length">
				<checkbox-group @change="checkboxChange">
					<view class="img-list">
						
						<view class="img-list-c" v-for="(item, index) in allData" :key="index">
							<view class="img-list-title">
								<view class="ilt-left">
									<text class="color-6">订单号:</text><text class="color-9">{{ item.order_id }}</text>
								</view>
								<view class="ilt-right  color-9">
									{{ item.status_name }}
								</view>
							</view>
							<view class="img-list-bot">
								<label class="uni-list-cell uni-list-cell-pd">
									<view v-if="!item.disabled" class="img-list-checkbox">
										<checkbox color="#FF7159" :value="item.id" :checked="item.checked" :disabled="item.disabled" v-if="item.disabled" class="checkboxNo"/>
										<checkbox color="#FF7159" :value="item.id" :checked="item.checked" :disabled="item.disabled" v-else/>
									</view>
								</label>
								<view class="img-list-right">
									<view class="img-list-content" v-for="(i, key) in item.goods" :key="key">
										<view class="img-list-item">
											<image class="img-list-item-l" :src="i.image_url" mode='aspectFill'></image>
											<view class="img-list-item-r">
												<view class="goods-name list-goods-name">{{i.name}}</view>
												<view class="goods-item-c">
													<view class="goods-buy">
														<view class="goods-salesvolume">规格：{{i.addon}}</view>
														<view class="goods-salesvolume">数量：{{i.nums}}</view>
														<view class="goods-salesvolume">SN码：{{i.sn}}</view>
														<view class="goods-salesvolume">BN码：{{i.bn}}</view>
													</view>
												</view>
											</view>
										</view>
									</view>
								</view>
							</view>
						</view>
						
					</view>
				</checkbox-group>
			</view>
		</view>
		
		<view class="button-bottom" v-if="allData.length">
			<button class="btn btn-b btn-square" @click="write" v-if="checkedIds.length">确认核销</button>
			<button class="btn btn-b btn-square completed" v-else>请选择待核销订单</button>
		</view>
	</view>
</template>

<script>
	export default {
		data(){
			return {
				key: '', // 筛选条件
				isgo: false,
				isgotext: '确认核销',
				allData: [] // 提货单列表
			}
		},
		onLoad(e){
			if(e.id){
				this.key = e.id;
			}
			this.getLadingInfo();
		},
		computed: {
			// 获取选中的提货单id
			checkedIds () {
				let ids = []
				this.allData.forEach(item => {
					// 判断不是禁用状态 并且是选中状态 并且是未核销状态
					if (!item.disabled && item.checked && item.status === 1) {
						ids.push(item.id)
					}
				})
				return ids
			},
		},
		methods: {
			// 多选框点击事件处理
			checkboxChange (e) {
                var values = e.detail.value;
				this.allData.forEach(item => {
					if (values.includes(item.id)) {
						item.checked = true
					} else {
						item.checked = false
					}
				})
            },
			//获取提货单详情
			getLadingInfo() {
				if(this.key){
					let data = {
						'key': this.key
					}
					this.$api.ladingInfo(data, e => {
						if (e.status) {
							this.allData = this.formatData(e.data);
						} else {
							this.allData = []; // 清空数据
							this.$common.modelShow('提示', e.msg, function(){});
						}
					});
				}
			},

			//搜索
			search() {
				if(this.key != ''){
					this.getLadingInfo();
				}else{
					this.$common.errorToShow('请输入查询关键字');
					return false;
				}
			},
	
			//查询判断是否可以核销
			isGoWrite(data) {
				let isgo = false;
				if (data.order_info.pay_status == 2 && data.order_info.ship_status == 3){
					isgo = true;
					this.lading_id = data.id;
					this.goodsList = data.goods;
					this.allData = data;
				} else {
					this.$common.modelShow('无法核销', '订单必须支付并已发货才可以核销', function(){});
				}
				this.isgo = isgo;
			},
			// 数据转化
			formatData (data){
				data.forEach (item => {
					if (item.status === 2) {
						// 已提货
						this.$set(item, 'checked', false)
						this.$set(item, 'disabled', true)
					} else {
						// 未提货
						this.$set(item, 'checked', true)
						this.$set(item, 'disabled', false)
					}
				})
				return data
			},
			//去核销
			write() {
				let _this = this;
				this.$common.modelShow('提示', '您确认核销吗？', function(res){
					//去核销
					let data = {
						lading_ids: _this.checkedIds.join()
					}
					_this.$api.ladingExec(data, res => {
						if(res.status) {
							_this.$common.successToShow(res.msg, _this.afterChangeDataStatus())
						}
					});
				});
			},
			// 核销完成后更改数据状态
			afterChangeDataStatus () {
				this.allData.forEach(item => {
					if (this.checkedIds.indexOf(item.id) > -1) {
						item.status = 2;
						item.checked = false;
						item.disabled = true;
						this.getLadingInfo();
					}
				})
			}
		}
	}
</script>

<style>
.ad {
	width: 100%;
	overflow: hidden;
}
.ad-img{
	width: 100%;
	float: left;
	margin-bottom: 20upx;
}
.ad-img:last-child{
	margin-bottom: 0;
}
.search{
	display: flex;
}
.search-c{
	width: 85%;
	margin-right: 2%;
}
.search-icon{
	left: 20upx;	
}
.search-input {
	padding: 10upx 30upx 10upx 30upx;
}
.search-input-p{
	padding: 0 !important;
}
.search .btn{
	width: 15%;
	border: none;
	background-color: #f1f1f1;
	font-size: 26upx;
	color: #333;
	border-radius: 6upx;
	line-height: 72upx;
	padding-left: 18upx;
	padding-right: 18upx;
}
.list-goods-name{
	margin-bottom: 8upx;
}
.goods-salesvolume{
	display: block;
	margin-bottom: 6upx;
}
.completed{
	background-color: #d9d9d9;
	color: #4e4e4e;
}
.img-list-bot{
	background-color: #fff;
	display: flex;
	padding: 30upx 26upx;
}
.img-list-title{
	padding: 26upx 26upx 0;
	background-color: #fff;
	font-size: 28upx;
	overflow: hidden;
}
.ilt-left{
	float: left;
}
.ilt-right{
	float: right;
}
.img-list-checkbox{
	position: relative;
	height: 100%;
}
.img-list-checkbox uni-checkbox{
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
}
.img-list-right{
	margin-left: 60upx;
}
.img-list-item{
	padding: 0;
}
.img-list-item-r{
	width: 360upx;
}
</style>
