<template>
	<view v-show="showPage">
		<form @submit="formSubmit" bindreset="formReset">
			<view class="content">
				<view v-if="form.head_type==1">
					<view class="banner">
						<image :src='form.head_type_value_url[0]' mode='widthFix'></image>
					</view>
				</view>
				<!-- 轮播图 -->
				<view v-else-if="form.head_type == 2">
					<view>
						<view class='sw'>
							<swiper>
								<swiper-item v-for="(item,item_index) in form.head_type_value_url" :key="item_index">
									<image :src="item" class="slide-image" mode='widthFix' />
								</swiper-item>
							</swiper>
						</view>
					</view>
				</view>
				<view v-else-if="form.head_type==3">
					<view class='video'>
						<video :src='form.head_type_video_url[0]' :poster="form.head_type_value_url[0]"></video>
					</view>
				</view>
				<!-- 纯文字 -->
				<view v-if="form.desc !=''">
					<view class='plaintext'>
						<text>{{form.desc}}</text>
					</view>
				</view>
				<view class="input-box">
					<view v-for="(item,index) in form.items" :key="index">
						<view class='goods-box-item' v-if="item.type=='goods'">
							<image class='goods-img' :src='item.goods.image_url' mode='aspectFit'></image>
							<view class='goods-right'>
								<view class='goods-name'>{{item.name}}</view>
								<view class='goods-mid'>
									<text>已售{{item.goods.buy_count}}</text>
								</view>
								<view class='goods-buttom'>
									<view class="goods-price">￥{{item.goods.price}}</view>
									<view class='choose-specs' @click="specifications($event,item)" data-type='1' :data-goods="item.goods.id"
									 :data-id="item.id" data-statu="openspecs">
										选规格
									</view>
									<text class='order-num' v-if="item.cart_count> 0">{{item.cart_count}}</text>
								</view>
							</view>
						</view>
						<view class='form-input-box-item' v-if="item.type=='text'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<input class='ib-item-input' type="text" :name="''+item.id" :data-id="item.id" v-model="item.default_value"
								 placeholder-class='ib-item-input-c' :placeholder="'请输入'+item.name"></input>
							</view>
						</view>
						<!-- 日期 -->
						<view class='form-input-box-item' v-if="item.type=='date'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class="ib-item-mid">
									<picker mode="date" :name="''+item.id" :value="item.default_value" @change="bindDateChange($event,item)"
									 :data-id='item.id'>
										<view>{{item.default_value}}</view>
									</picker>
									<image class='icon-img-right' src='/static/image/ic-unfold.png'></image>
								</view>
							</view>
						</view>
						<!-- 时间 -->
						<view class='form-input-box-item' v-if="item.type=='time'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class="ib-item-mid">
									<picker class="weui-btn" :name="''+item.id" mode="time" :value="item.default_value"
									 @change="bindTimeChange($event,item)" :data-id='item.id'>
										<view>{{item.default_value}}</view>
									</picker>
									<image class='icon-img-right' src='/static/image/ic-unfold.png'></image>
								</view>
							</view>
						</view>
						<!-- 范围选择 -->
						<!-- 多选 -->
						<view class='form-input-box-item' v-if="item.type=='checbox'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class='checkout-list'>
									<checkbox-group @change="checkboxChange($event,item)" :data-value="item.id" :name="''+item.id">
										<label class="checkout-item" v-for="(checkbox_item,item_index) in item.checbox_value" :key="item_index">
											<view class="checkout-item-c" :class="checkbox_item.checked?'black':''">
												<checkbox class="" :value="checkbox_item.value" :checked="checkbox_item.checked" /> {{checkbox_item.value}}
											</view>
										</label>
									</checkbox-group>
								</view>
							</view>
						</view>
						<!-- radio时处理 -->
						<view class='form-input-box-item' v-if="item.type=='radio'">
							<view class='ib-item-left'>
								<text>{{item.name}}:</text>
							</view>
							<view class='ib-item-right'>
								<radio-group class="uni-list" @change="radioChange($event,item)" :data-value="item.id" :name="''+item.id">
									<label class=" uni-list-cell uni-list-cell-pd " v-for="(radio_item, item_index) in  item.radio_value" :key="item_index">
										<view class="invoice-type-icon">
											<radio class="a-radio" :id="radio_item" :value="radio_item" checked=true v-if="radio_item==item.default_value"></radio>
											<radio class="a-radio" :id="radio_item" :value="radio_item" v-if="radio_item!=item.default_value"></radio>
										</view>
										<view class="invoice-type-c">
											<label class="label-2-text" :for="radio_item">
												<text>{{radio_item}}</text>
											</label>
										</view>
									</label>
								</radio-group>
							</view>
						</view>
						<!-- 省市区选择 -->
						<view class='form-input-box-item' v-if="item.type=='area'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class="ib-item-mid">
									<input class="fsz26" :value="pickerValue" @focus="showThreePicker" :name="''+item.id" />
									<area-picker class="fsz26" ref="areaPicker" :areaId="areaId" :defaultIndex="defaultIndex" @onConfirm="onConfirm"></area-picker>
								</view>
							</view>
						</view>
						<!-- 金额 -->
						<view class='form-input-box-item' v-if="item.type=='money'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class="ib-item-mid">
									<input class='ib-item-input' type="digit" :name="''+item.id" v-model="item.default_value" placeholder-class='ib-item-input-c'
									 :placeholder="'请输入'+item.name"></input>
								</view>
							</view>
						</view>
						<!-- 密码 -->
						<view class='form-input-box-item' v-if="item.type=='password'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class="ib-item-mid">
									<input class='ib-item-input' type='password' :name="''+item.id" v-model="item.default_value" placeholder-class='ib-item-input-c'
									 :placeholder="'请输入'+item.name"></input>
								</view>
							</view>
						</view>
						<!-- 图片 -->
						<view class='form-input-box-item' v-if="item.type=='image'">
							<view class='form-input-box-title'>上传{{item.name}}</view>
							<view class='form-multiple-rows'>
								<view class='f-m-r-item'>
									<view class='upload-img-list'>
										<view class='upload-img-bd'>
											<view class='upload-img' v-for="(pic_item, i) in item.pics" :key="i">
												<image @click='pic_del(item,index,i)' :data-index="i" class='del-img' src='/static/image/del.png'></image>
												<image class='upload-camera' :src="pic_item.src" mode='aspectFit'></image>
												<input type='text' hidden='hidden' :name="item.id+'_'+i" v-model="pic_item.image_id"></input>
											</view>
										</view>
										<view class='upload-img-hd'>
											<image class='upload-camera' src="/static/image/camera.png" @click="pic_choose($event,item,index)"
											 :data-id="item.id"></image>
										</view>
									</view>
								</view>
							</view>
						</view>
						<!-- 文本域 -->
						<view class='form-input-box-item' v-if="item.type=='textarea'">
							<view class='form-input-box-title'>{{item.name}}</view>
							<view class='form-multiple-rows'>
								<view class='f-m-r-item form-input-box-item'>
									<textarea :name="''+item.id" class='ib-item-textarea' :placeholder="'请输入'+item.name" placeholder-class="ib-item-input-c"></textarea>
								</view>
							</view>
						</view>
						<!-- 定位 -->
						<view class='form-input-box-item' v-if="item.type=='coordinate'">
							<view class='ib-item-left'>
								<text>{{item.name}}：</text>
							</view>
							<view class='ib-item-right'>
								<view class="ib-item-mid ib-item-start">
									<image class='icon-img' src='/static/image/ic-location.png'></image>
									<input class='ib-item-input margin-r' placeholder-class='ib-item-input-c' :name="''+item.id" :value="item.default_value"
									 disabled='disabled' placeholder="点击获取位置信息" @click="chooseLocation($event,item,index)" :data-id='item.id' />
								</view>
							</view>
						</view>
					</view>
				</view>
				<view class='goods-bottom' v-if="form.type==1">
					<text class='goods-total'>合计
						<text class='goods-total-r'>￥{{goodsTotalMoney}}</text>
					</text>
				</view>
			</view>
			<!-- 底部按钮 -->
			<view class='bottom-btn'>
				<button :style='{backgroundColor:form.button_color}' data-statu="open" form-type="submit" :disabled='submitStatus'
				 :loading='submitStatus'>{{form.button_name}}</button>
			</view>
		</form>
		<lvv-popup position="bottom" ref="lvvpopref" class="lvvpopref">
			<!-- 多规格商品弹出 -->
			<block v-if="showSpecs">
				<view class="modal-body" data-statu="closespecs" catchtouchmove="move">
					<view class='specs-goods-t'>
						<view class='specs-goods-information'>
							<text class='specs-goods-name'>{{goodsInfoName}}</text>
							<text class='specs-goods-price'>￥{{goodsInfoPrint}}</text>
						</view>
						<view class='close-btn' @click="closeModal" :data-goods="select_goods_id" :data-id="select_id" data-type="100"
						 data-statu="closespecs">
							<image src='/static/image/close.png'></image>
						</view>
					</view>
					<scroll-view class='specs-goods-c' scroll-y="true">

						<view class="color" v-for="(value,key) in goodsSpesDesc" :key="key">
							<text class='salespromotion-service-name'>{{key}}</text>
							<view class='salespromotion-service-b'>
								<block v-for="(i,item_index) in value" :key="item_index">
									<view v-if="i.is_default" class='pitch-on'>{{i.name}}</view>
									<view v-else-if="i.product_id != 0" :class='i.is_default ? "pitch-on" : ""' :data-key="i.product_id" :data-id="i.name"
									 @click="selectSku">{{i.name}}</view>
									<view v-else class='nothing'>{{i.name}}</view>
								</block>
							</view>
						</view>

						<!-- 库存 -->
						<view class='number'>
							<text class='salespromotion-service-name'>数量</text>
							<view class="stepper">
								<text :class="goodsNums==0?'disabled':'normal'" @click="bindMinus">-</text>
								<input type="number" @change="bindManual" v-model="goodsNums" />
								<text :class="goodsNums==goodsInfoNumber?'disabled':'normal'" @click="bindPlus">+</text>
							</view>
						</view>
					</scroll-view>
					<view class='detail-footer'>
						<!-- 点击加购物车/购买 -->
						<view class='detail-footer-right determine-next' v-if="status">
							<!-- <view @click='goodsAddCart' class='determine'>确定</view> -->
							<view @click='goodsAddCart' class='next'>下一步</view>
						</view>
						<view class='detail-footer-right' v-else>
							<view class='stockno'>该商品已售罄</view>
						</view>
					</view>
				</view>
			</block>
		</lvv-popup>
	</view>
</template>
<script>
	import areaPicker from '@/components/area-picker/areaPicker.vue'
	import lvvPopup from '@/components/lvv-popup/lvv-popup.vue'
	export default {
		name: '',
		components: {
			areaPicker,
			lvvPopup
		},
		props: {},
		data() {
			return {
				formId: '',
				form: {
					head_type: 1,
					head_type_value_url: '',
				},
				showPage: true,
				hiddenForm: true,
				indicatorDots: true, //商品轮播图底部圆点
				autoplay: true, //商品轮播图自动播放
				interval: 3000, //商品轮播图切换间隔
				duration: 500, //商品轮播图切换动画时间
				slideImg: [], //幻灯片广告数据
				minusStatus: 'disabled', // 使用data数据对象设置样式名
				animationData: {},
				opacityData: {},
				hide: 'animathide',
				formMoney: 0.0, //表单金额
				region: ['河南省', '郑州市', '中原区'],
				areaId: 410102,
				pickerValue: '',
				defaultIndex: [0, 0, 0],
				pics: [], //图片
				goodsNums: 0,
				cart: [],
				currentKey: 0, //当前下单的商品的Key
				currentGoodsId: 0, //当前选中的商品ID
				goodsTotalMoney: '0.00', //商品总额
				originForm: [], //原始表单
				paymentType: '', //支付类型
				payment_type: '', //表单付款码||表单订单
				/** 商品信息*/
				goodsSpesDesc: '',
				productId: '',
				status: '',
				goodsInfoName: '',
				goodsInfoPrint: '',
				goodsInfoNumber: '',
				select_goods_id: '',
				select_id: '',
				showSpecs: false,
				submitStatus: false, //按钮状态
                shareUrl: '/pages/share/jump'
			}
		},
		onLoad(options) {
			var id = options.id
			if (!id) {
				this.$common.errorToShow('路径错误')
				return false
			}
			this.formId = id
			this.$db.set('formId', id)
		},
		onShow() {
			this.showPage = true
			this.getFormDetail()
		},
		methods: {
			// 省市区联动初始化
			showThreePicker() {
				this.pickerValue =
					this.region[0] + ' ' + this.region[1] + ' ' + this.region[2]
				this.$refs.areaPicker[0].showPicker()
			},
			onConfirm(e) {
				let province_name = e[0].name
				let city_name = e[1].name
				let county_name = e[2].name
				this.pickerValue = e[0].name + ' ' + e[1].name + ' ' + e[2].name
				let data = {
					province_name: province_name,
					city_name: city_name,
					county_name: county_name
				}
				let regionName = [province_name, city_name, county_name]
				this.$api.getAreaId(data, res => {
					if (res.status) {
						this.areaId = res.data
					} else {
						uni.showModal({
							title: '提示',
							content: '地区选择出现问题，请重新选择地区',
							showCancel: false
						})
					}
				})
			},
			getFormDetail() {
				var data = {
					id: this.formId,
					token: this.$db.get('userToken')
				}
				var that = this
				this.$api.getFormDetial(data, res => {
					if (res.status) {
						// this.$set(this.$data, 'originForm', res.data)
						this.form = JSON.parse(JSON.stringify(res.data)) 
						this.originForm = JSON.parse(JSON.stringify(res.data)) 
						
						// this.$set(this.form, 'head_type_value_url', res.data.head_type_value_url)
						
						if (res.data.type == '1' || res.data.type == '2') {
							if (res.data.type == '1') {
								//订单
								that.payment_type = this.$config.paymentType.form_order
							} else if (res.data.type == '2') {
								//付款码
								that.payment_type = this.$config.paymentType.form_pay
							}
						}
						//设置title名称
						uni.setNavigationBarTitle({
							title: res.data.name
						})

					} else {
						this.showPage = false;
						if (typeof res.data.need_login == 'undefined') {
							uni.showModal({
								title: '提示',
								content: '表单已过期，请扫描新的二维码',
								showCancel: false,
								success: function(res) {
									if (res.confirm) {
										uni.navigateTo({
											url: '../../index/index'
										})
									}
								}
							})
						} else {
							//去登录	
							this.$store.commit({
								type: 'redirect',
								page: '/pages/form/detail/form?id=' + this.formId
							})
							this.$common.jumpToLogin();
						}
					}
				})
			},
			// 选择日期
			bindDateChange(e, item) {
				item.default_value = e.target.value
			},
			// 选择时间
			bindTimeChange(e, item) {
				item.default_value = e.target.value
			},
			// 单选
			radioChange(e, item) {
				item.default_value = e.detail.value
			},
			// 多选
			checkboxChange(e, item) {
				var values = e.detail.value
				for (var i = 0; i < item.checbox_value.length; ++i) {
					const checkbox_item = item.checbox_value[i]
					if (values.includes(checkbox_item.value)) {
						this.$set(checkbox_item, 'checked', true)
					} else {
						this.$set(checkbox_item, 'checked', false)
					}
				}
			},

			//商品减一
			bindMinus() {
				if (this.goodsNums > 1) {
					this.goodsNums--
				} else {
					this.goodsNums = 0
				}
			},
			//商品加一
			bindPlus() {
				if (this.goodsNums >= this.goodsInfoNumber) {
					this.goodsNums = this.goodsInfoNumber
				} else {
					this.goodsNums++
				}
			},
			/* 输入框事件 */
			bindManual(e) {
				this.num = e.detail.value
			},
			//选择位置
			chooseLocation(e, item,index) {
				var pages = getCurrentPages()
				var items = pages[0].$vm.form.items;
				var that = this;
				uni.chooseLocation({
					success(e) {
						item.default_value = e.latitude + ',' + e.longitude	
						items[index] = item;
						setTimeout(() => {
							that.form.items = items;
						}, 500)
					},
					fail(e) {
						uni.getSetting({
							success(res) {
								if (!res.authSetting['scope.userLocation']) {
									uni.openSetting()
								}
							}
						})
					}
				})
			},
			pic_choose(e, item, index) {
				var that = this
				var pages = getCurrentPages()
				if(pages.length>1){
					var items = pages[1].$vm.form.items;
				}else{
					var items = pages[0].$vm.form.items;
				}
				this.$api.uploadImage(5, res => {
					if (res.status) {
						if (!item.pics) {
							item.pics = []
						}
						item.pics.push({
							src: res.data.url.replace(/\\/g, '/'),
							image_id: res.data.image_id
						})
						
						 // #ifdef H5
						that.$set(that.form.items, index, item)
						// #endif
						// #ifndef H5
							items[index] = item;
							that.form.items = items;
						// #endif 
						that.$common.successToShow(res.msg)
					} else {
						that.$common.errorToShow(res.msg)
					}
				})
			},
			//删除图片
			pic_del(item, index, i) {
				item.pics.splice(i, 1)
				this.$set(this.form.items, index, item)
			},
			//表单提交
			formSubmit(e) {
				var that = this
				var data = e.detail.value
				//订单时需要合并购物车信息
				if (this.form.type == 1) {
					if (this.cart.length < 1) {
						this.$common.errorToShow('请先选择商品')
						return true
					}
					var tempArray = []
					this.cart.forEach(function(item, index, input) {
						tempArray[item.key + '_' + index] = item
					})
					data = Object.assign(data, tempArray)
				}
				let userToken = this.$db.get('userToken')
				let obj = {
					data,
					id: this.form.id,
					token: userToken
				}
				this.submitStatus = true;
				this.$api.addSubmitForm(obj, res => {
					if (res.status) {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
						// that.$common.successToShow(res.msg);
						//表单类型判断是否需要支付，支付金额多少
						if (that.form.type == '1' || that.form.type == '2') {
							//跳转首页
							setTimeout(function() {
								//出来支付按钮
								that.$common.redirectTo('/pages/goods/payment/index?form_id=' + res.data.id + '&type=' + that.payment_type +
									'&recharge=' + res.data.money)
							}, 1000)
						} else {
							that.formReset()
							// that.$common.successToShow(res.msg)
							//跳转首页
							setTimeout(function() {
								wx.navigateTo({
									url: '../../index/index'
								})
							}, 1500)
						}
					} else {
						this.$common.errorToShow(res.msg);
					}
				},res => {
					this.submitStatus = false;
				})
			},
			//表单清空
			formReset(e) {
				this.$db.set('formId', '')
				this.cart = [] //初始化，刷新当前页面
				this.form = this.originForm
			},
			closeModal() {
				this.$refs.lvvpopref.close()
			},
			//选择规格弹出
			specifications(e, item) {
				this.$refs.lvvpopref.show()
				this.showSpecs = true
				this.select_id = e.target.dataset.id
				this.select_goods_id = e.target.dataset.goods
				this.currentKey = e.target.dataset.id //当前选中的key
				this.currentGoodsId = e.target.dataset.goods //当前选中的商品ID
				this.getGoodsInfo(item)
			},
			//获取商品详情
			getGoodsInfo(item) {
				let goods = item.goods
				this.goodsSpesDesc = this.getSpes(goods.product)
				this.productId = goods.product.id
				this.goodsInfoName = goods.product.name
				this.goodsInfoPrint = goods.product.price
				this.goodsInfoNumber = goods.product.stock
				this.goodsNums = this.getNumsByKey(this.currentKey, goods.product.id)
				this.status = goods.product.stock < 1 ? false : true
			},
			/*获取key的数量 */
			getNumsByKey(key, productId) {
				var that = this
				if (that.cart.length < 1) {
					return 0
				} else {
					for (var i = 0; i < that.cart.length; i++) {
						if (that.cart[i].key == key && that.cart[i].productId == productId) {
							return that.cart[i].nums
						}
					}
					return 0
				}
			},
			//加入购物车
			goodsAddCart: function() {
				var productId = this.productId
				var currentKey = this.currentKey
				if (this.cart.length < 1) {
					this.cart.push({
						key: currentKey,
						productId: productId,
						goodsId: this.select_goods_id,
						nums: this.goodsNums,
						price: this.goodsInfoPrint
					})
				} else {
					var isIn = false
					for (var i = 0; i < this.cart.length; i++) {
						if (
							this.cart[i].key == currentKey &&
							this.cart[i].productId == productId
						) {
							this.cart[i] = {
								key: currentKey,
								productId: productId,
								goodsId: this.select_goods_id,
								nums: this.goodsNums,
								price: this.goodsInfoPrint
							}
							isIn = true
						}
					}
					if (!isIn) {
						this.cart.push({
							key: currentKey,
							productId: productId,
							goodsId: this.select_goods_id,
							nums: this.goodsNums,
							price: this.goodsInfoPrint
						})
					}
				}
				this.showSpecs = false
				this.$refs.lvvpopref.close()
				this.getCartNums()
			},
			getCartNums() {
				var items = this.form.items
				var itemKey = ''
				for (var i = 0, len = items.length; i < len; ++i) {
					if (items[i].id == this.currentKey) {
						itemKey = i
					}
				}
				var that = this
				if (this.form.items[itemKey].goods.id == this.currentGoodsId) {
					if (this.form.items[itemKey].cart_count > 0) {
						var cart_count = 0
						var currentKey = this.currentKey
						this.cart.forEach(function(item, index, input) {
							if (item.key == currentKey) {
								cart_count += item.nums
							}
							that.form.items[itemKey].cart_count = cart_count
						})
					} else {
						this.form.items[itemKey].cart_count = this.goodsNums
					}
				} else {
					this.form.items[itemKey].cart_count = this.goodsNums
				}
				this.getGoodsTotalMoney()
			},
			//获取商品总额
			getGoodsTotalMoney() {
				var that = this
				var goodsTotalMoney = 0
				this.cart.forEach(function(item, index, input) {
					goodsTotalMoney += item.price * item.nums
				})
				this.goodsTotalMoney = this.$common.formatMoney(goodsTotalMoney, 2, '')
			},
			getSpes: function(product) {
				if (!product.default_spes_desc) {
					return []
				}
				return product.default_spes_desc
			},
			//获取规格信息
			selectSku(e) {
				var id = e.target.dataset.key
				this.$api.getProductInfo({
					id
				}, res => {
					if (res.status) {
						this.goodsSpesDesc = this.getSpes(res.data)
						this.productId = res.data.id
						this.goodsInfoName = res.data.name
						this.goodsInfoPrint = res.data.price
						this.goodsInfoNumber = res.data.stock
						this.goodsNums = this.getNumsByKey(this.currentKey, res.data.id)
						this.status = res.data.stock < 1 ? false : true
					}
				})
			},
            //获取分享URL
            getShareUrl() {
                let data = {
                    client: 2,
                    url: "/pages/share/jump",
                    type: 1,
                    page: 8,
                    params: {
                        id: this.formId
                    }
                };
                let userToken = this.$db.get('userToken');
                if (userToken && userToken != '') {
                	data['token'] = userToken;
                }
                this.$api.share(data, res => {
                    this.shareUrl = res.data
                });
            }
		},
        watch:{
            formId: {
                handler () {
                    this.getShareUrl();
                },
                deep: true
            }
        },
		//分享
		onShareAppMessage() {
			return {
				title: this.form.name,
				path: this.shareUrl
			}
		}
	}
</script>
<style>
	.content {
		padding-bottom: 200rpx;
		background-color: #eeeeee;
	}

	.sw,
	.video {
		height: 350rpx;
	}

	.banner,
	.sw,
	.video {
		width: 100%;
		/* height: 350rpx; */
		background-color: #fff;
	}

	.banner image,
	.sw swiper,
	.sw swiper image,
	.video video {
		width: 100%;
		height: 100%;
	}

	.plaintext {
		padding: 20rpx 30rpx;
		font-size: 30rpx;
		color: #333;
		background-color: #fff;
	}

	.goods {
		/* margin: 20rpx 0; */
		background-color: #fff;
	}

	.form-input-box-title {
		/* padding: 20rpx 30rpx 0; */
		font-size: 28rpx;
	}

	.goods-box-item {
		overflow: hidden;
		padding: 20rpx 30rpx 20rpx 0;
		margin-left: 30rpx;
		border-bottom: 2rpx solid #eeeeee;
	}

	.goods-box-item:nth-last-child(2) {
		border: none;
	}

	.goods-img {
		width: 150rpx;
		height: 150rpx;
		display: inline-block;
		float: left;
	}

	.goods-right {
		width: 520rpx;
		display: inline-block;
		float: left;
		margin-left: 20rpx;
	}

	.goods-name {
		font-size: 30rpx;
		color: #333;
		overflow: hidden;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}

	.goods-mid {
		font-size: 24rpx;
		color: #999;
	}

	.goods-buttom {
		overflow: hidden;
		position: relative;
		height: 60rpx;
	}

	.goods-price {
		font-size: 28rpx;
		color: #eb0000;
		display: inline-block;
	}

	.stepper {
		width: 156rpx;
		height: 48rpx;
		border-radius: 6rpx;
		margin: 0 auto;
		display: inline-block;
		overflow: hidden;
		box-sizing: border-box;
		float: right;
	}

	.stepper text {
		width: 44rpx;
		line-height: 42rpx;
		text-align: center;
		float: left;
		box-sizing: border-box;
		border: 2rpx solid #ccc;
	}

	.stepper input {
		width: 64rpx;
		height: 38rpx;
		float: left;
		text-align: center;
		font-size: 28rpx;
		display: inline-block;
		box-sizing: border-box;
	}

	.stepper .normal {
		color: black;
	}

	.stepper .disabled {
		color: #ccc;
	}

	.choose-specs {
		width: 136rpx;
		height: 48rpx;
		line-height: 46rpx;
		border-radius: 50rpx;
		margin: 0 auto;
		text-align: center;
		display: inline-block;
		overflow: hidden;
		box-sizing: border-box;
		float: right;
		font-size: 24rpx;
		border: 2rpx solid #ccc;
		position: relative;
		top: 12rpx;
	}

	.goods-bottom {
		border-top: 2rpx solid #eeeeee;
		overflow: hidden;
		padding: 20rpx 30rpx;
		background-color: #fff;
	}

	.goods-total {
		float: right;
		color: #999;
		font-size: 28rpx;
	}

	.goods-total-r {
		color: #eb0000;
		font-size: 30rpx;
	}

	.input-box {
		margin: 20rpx 0;
		background-color: #fff;
	}

	.form-input-box-item {
		/* overflow: hidden; */
		padding: 20rpx 30rpx 20rpx 0;
		margin-left: 30rpx;
		border-bottom: 2rpx solid #eeeeee;
	}

	.ib-item-left {
		display: inline-block;
		/* min-width: 150rpx; */
		/* max-width: 600rpx; */
		font-size: 28rpx;
		color: #333;
		width: 100%;
		/* float: left; */
		padding: 10rpx 0;
	}

	.ib-item-right {
		/* min-width: 600rpx; */
		/* max-width: 690rpx; */
		width: 100%;
		display: inline-block;
		color: #666;
		font-size: 28rpx;
		/* float: left; */
		padding: 6rpx 0;
	}

	.ib-item-input {
		color: #666;
		font-size: 28rpx;
	}

	.margin-r {
		margin-left: 40rpx;
	}

	.ib-item-input-c {
		color: #999;
		font-size: 28rpx;
	}

	.ib-item-label {
		display: inline-block;
		position: relative;
		min-width: 150rpx;
		margin-right: 20rpx;
	}

	.ib-item-label radio {
		position: absolute;
		opacity: 0;
		width: 40rpx;
		height: 40rpx;
	}

	.ib-item-label-text {
		display: inline-block;
		margin-left: 60rpx;
		position: relative;
		top: 2rpx;
	}

	.label-icon {
		position: absolute;
		top: 0;
	}

	.label-icon icon {
		margin: 0;
	}

	.ib-item-mid {
		padding-top: 4rpx;
		margin: 0;
		/* position: relative; */
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.ib-item-mid picker {
		height: 40rpx;
	}

	.ib-item-mid .weui-select {
		border: none;
		height: 100%;
		line-height: 48rpx;
		min-height: 40rpx;
	}

	.ib-item-mid-text {
		margin-left: 40rpx;
		color: #999;
	}

	.icon-img {
		/* position: absolute;
		top: 50%;
		transform: translateY(-50%); */
		width: 32rpx;
		height: 32rpx;
	}

	.icon-img-right {
		/* position: absolute;
		top: 50%;
		transform: translateY(-50%); */
		width: 32rpx;
		height: 32rpx;
		right: 0;
	}

	.form-multiple-rows .form-input-box-item {
		border: none;
	}

	.f-m-r-item {
		color: #666;
		font-size: 28rpx;
		margin-top: 16upx;
	}

	.f-m-r-item .ib-item-label {
		display: block;
		margin-bottom: 20rpx;
	}

	.f-m-r-item .ib-item-label:last-child {
		margin-bottom: 0;
	}

	.various-spec-list {
		overflow: hidden;
	}

	.various-spec-item {
		padding: 10rpx 20rpx;
		display: inline-block;
		border: 2rpx solid #e2e2e2;
		margin-right: 20rpx;
		margin-bottom: 20rpx;
		border-radius: 6rpx;
		color: #666;
		background-color: #f7f7f7;
		min-width: 130rpx;
		text-align: center;
	}

	.vAactive {
		border: 2rpx solid #333;
		color: #333;
	}

	.various-spec-list:last-child .various-spec-item {
		margin-bottom: 0rpx;
	}

	.upload-img-list {
		overflow: hidden;
	}

	.upload-img-hd {
		position: relative;
		width: 150rpx;
		height: 150rpx;
		border: 2rpx solid #e2e2e2;
		background-color: #f7f7f7;
		border-radius: 6rpx;
		box-sizing: border-box;
		float: left;
		margin-left: 30rpx;
	}

	.upload-img-hd input {
		position: absolute;
		width: 100%;
		height: 100%;
		opacity: 0;
	}

	.upload-img-hd image {
		width: 48rpx;
		height: 48rpx;
		position: relative;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	.upload-img-bd {
		/* width: 150rpx; */
		/* height: 150rpx; */
		float: left;

		overflow: hidden;
	}

	.upload-img .upload-camera {
		width: 100%;
		height: 100%;
	}

	.upload-img {
		width: 150rpx;
		height: 150rpx;
		position: relative;
		float: left;
		margin-right: 30rpx;
	}

	.upload-img:last-child {
		margin-right: 0;
	}

	.del-img {
		width: 36rpx !important;
		height: 36rpx !important;
		position: absolute;
		right: 0;
		top: 0;
		z-index: 99;
	}

	.ib-item-textarea {
		width: 100%;
		height: 200rpx;
		box-sizing: border-box;
		border: 2rpx solid #e2e2e2;
		background-color: #f7f7f7;
		border-radius: 6rpx;
		padding: 20rpx 30rpx;
	}

	.bottom-btn {
		position: fixed;
		bottom: 0;
		width: 100%;
		z-index: 95;
	}

	.bottom-btn button {
		width: 100%;
		height: 90rpx;
		line-height: 90rpx;
		margin: 0 auto;
		background-color: #333;
		color: #fff;
		font-size: 32rpx;
		border-radius: 0;
	}

	.bottom-btn button::after {
		border-radius: 0;
	}

	.hidden {
		display: none;
	}

	.checkout-list {
		overflow: hidden;
	}

	.checkout-item {
		display: inline-block;
		float: left;
	}

	.checkout-item-c {
		padding: 4rpx 14rpx;
		border: 2rpx solid #ccc;
		margin-right: 10rpx;
		border-radius: 6rpx;
		color: #888;
	}

	.checkout-item-c checkbox {
		display: none;
	}

	.black {
		background-color: rgb(55, 55, 55);
		color: #fff;
		border: 2rpx solid rgb(55, 55, 55);
	}

	/*支付按钮样式*/

	.content-bot {
		margin-top: 18rpx;
	}

	.content-bot>view {
		padding: 16rpx 0;
		margin-bottom: 2rpx;
		position: relative;
		background-color: #fff;
		height: 75rpx;
	}

	.content-bot>view button {
		background-color: #fff;
		width: 100%;
		height: 100%;
		padding: 0;
		position: static;
		text-align: left;
	}

	.content-bot>view button::after {
		border: none;
	}

	.content-bot .left-img {
		display: inline-block;
		height: 82rpx;
		width: 94rpx;
		border-right: 2rpx solid #f4f4f4;
		position: absolute;
		left: 30rpx;
		top: 50%;
		transform: translateY(-50%);
	}

	.content-bot .left-img image {
		width: 64rpx;
		height: 64rpx;
		position: relative;
		top: 8rpx;
	}

	.content-bot-right {
		display: inline-block;
		margin-left: 150rpx;
		position: relative;
		top: 16rpx;
	}

	.modal-box {
		position: fixed;
		width: 100%;
		height: 100%;
		top: 0px;
		background: rgba(0, 0, 0, 0.4);
		overflow: hidden;
		z-index: 1000;
	}

	.modal-body {
		position: fixed;
		bottom: 0;
		background-color: #fff;
		width: 100%;
		z-index: 1001;
		font-size: 28rpx;
	}

	.modal-payment .item {
		height: 80rpx;
		width: 100%;
		line-height: 80rpx;
		text-align: center;
	}

	.modal-payment .immediate-pay {
		height: 80rpx;
		line-height: 80rpx;
		width: 100%;
		text-align: center;
		border: none;
		border-radius: 0;
		border-bottom: 2rpx solid #eee;
		box-sizing: border-box;
		background-color: #fff;
	}

	.modal-payment .immediate-pay::after {
		border: none;
	}

	.specs-goods-t {
		position: relative;
		padding: 30rpx;
		border-bottom: 2rpx solid #f3f3f3;
	}

	.specs-goods-information {
		width: 520rpx;
		display: inline-block;
	}

	.specs-goods-information .specs-goods-name {
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		display: block;
		font-size: 24rpx;
		margin-bottom: 20rpx;
	}

	.specs-goods-information .specs-goods-price {
		display: block;
		color: #ff3b44;
		font-size: 30rpx;
	}

	.close-btn {
		width: 40rpx;
		height: 40rpx;
		border-radius: 50%;
		display: inline-block;
		position: absolute;
		right: 30rpx;
	}

	.close-btn image {
		width: 100%;
		height: 100%;
	}

	.modal-body .detail-footer-right {
		width: 100%;
	}

	.gray-text {
		color: #a5a5a5;
		font-size: 28rpx;
	}

	.salespromotion-service-name {
		color: #a5a5a5;
		margin-right: 26rpx;
	}

	.color .salespromotion-service-name {
		float: left;
	}

	.salespromotion-service-body,
	.salespromotion-service-body view {
		display: inline-block;
	}

	.sales-promotion .salespromotion-service-body {
		margin: auto;
	}

	.sales-promotion text.salespromotion-service-body {
		background-color: #ff3b44;
		color: #fff;
		font-size: 18rpx;
		margin-left: 0rpx;
		border-radius: 10rpx;
		height: 28rpx;
		line-height: 28rpx;
		padding: 0 10rpx;
	}

	.salespromotion-service-body view {
		width: 170rpx;
		height: 40rpx;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		position: relative;
		left: -6rpx;
	}

	.salespromotion-service-body view:first-child {
		margin-right: 8rpx;
	}

	.color-number {
		font-size: 28rpx;
		border-bottom: 14rpx solid #f3f3f3;
	}

	.color,
	.specifications,
	.number {
		padding: 22rpx 25rpx;
		border-bottom: 2rpx solid #f3f3f3;
		overflow: hidden;
	}

	.color {
		padding-bottom: 8rpx;
	}

	.color .salespromotion-service-b,
	.specifications .salespromotion-service-b {
		width: 600rpx;
		display: inline-block;
		float: left;
	}

	.color .salespromotion-service-b>view,
	.specifications .salespromotion-service-b>view {
		padding: 2rpx 20rpx;
		display: inline-block;
		text-align: center;
		border: 2rpx solid #e0e0e0;
		border-radius: 8rpx;
		color: #666;
		margin-right: 22rpx;
		margin-bottom: 12rpx;
	}

	.pitch-on {
		border: 2rpx solid #ff3b44;
		background-color: #ff3b44;
		color: #fff !important;
	}

	.nothing {
		border: 2rpx dashed #e0e0e0 !important;
		color: #c9c9c9 !important;
	}

	.specs-goods-c {
		margin-bottom: 100rpx;
		max-height: 432rpx;
	}

	.number {
		padding: 22rpx 25rpx;
	}

	.number>text {
		color: #999;
		position: relative;
		font-size: 28rpx;
	}

	.detail-footer {
		overflow: hidden;
		height: 100rpx;
		position: fixed;
		bottom: 0;
		width: 750rpx;
		text-align: center;
		z-index: 1000;
	}

	.detail-footer-left {
		width: 30%;
		height: 100rpx;
		font-size: 24rpx;
		color: #666;
		background-color: #f7f7f7;
		padding-top: 10rpx;
		box-sizing: border-box;
		display: inline-block;
	}

	.detail-footer-left>view {
		width: 50%;
		box-sizing: border-box;
		float: left;
		display: inline-block;
	}

	.detail-footer-left>view image {
		height: 36rpx;
		width: 36rpx;
	}

	.detail-footer-left>view text {
		display: block;
	}

	.detail-footer-right {
		width: 70%;
		display: inline-block;
		height: 100rpx;
		line-height: 100rpx;
		float: right;
		font-size: 28rpx;
		color: #fff;
		box-sizing: border-box;
	}

	.detail-footer-right>view {
		width: 100%;
		display: inline-block;
	}

	.modal-body .detail-footer-right {
		width: 100%;
	}

	.detail-footer-right>view {
		background-color: #333;
	}

	.order-num {
		display: block;
		min-width: 16rpx;
		height: 28rpx;
		line-height: 28rpx;
		background-color: #ff3b44;
		color: #fff;
		font-size: 16rpx;
		border-radius: 50rpx;
		position: absolute;
		right: 0rpx;
		top: 0rpx;
		padding: 0 6rpx;
		text-align: center;
	}

	.uni-list-cell-pd {
		/* width: 200upx; */
		margin-right: 40upx;
	}

	.invoice-type-icon,
	.invoice-type-c {
		display: inline-block;
	}

	.lvvpopref {
		z-index: 100;
	}
	.ib-item-start{
		justify-content: flex-start;
	}
</style>
