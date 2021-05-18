<template>
	
	<view class="hx-navbar" >
		
		<view
			:class="{'hx-navbar--fixed': fixed,'hx-navbar--shadow':shadow,'hx-navbar--border':border}"
			:style="{'background': backgroundColorRgba}"
			class="hx-navbar__content">
			<block v-if="backgroundImg">
				<image class="navbgimg" :src="backgroundImg" mode=""></image>
			</block>
			
			<view :style="{ height: statusBarHeight ,'background': statusBarBackground}" class="hx-status-bar" v-if="statusBar" ></view>
			<view :style="{color:colorInfo,height: height,'line-height': '40px'}" class="hd hx-navbar__header hx-navbar__content_view">
				<view class="hx-navbar__header-btns hx-navbar__content_view"  @tap="onClickLeft" v-if="leftSlot" :style="{'color': colorInfo}">
					<block v-if="leftText.length || leftIcon.length || back">
						<view
							v-if="leftIcon.length || back"
							:class="back ? 'left_back' : ''"
							class="hx-navbar__content_view" >
							<image class="left-cus-img" v-if="leftImg" src="/static/images/ap_return.svg" mode="scaleToFill"></image>
							<uni-icons v-else :type="back ? 'arrowleft' : leftIcon" :color="colorInfo" size="28"/>
						</view>
						<view
							v-if="leftText.length"
							:class="{'hx-navbar-btn-icon-left':!leftIcon.length}"
							class="hx-navbar-btn-text hx-navbar__content_view">{{ leftText }}</view>
					</block>
					<block v-else>
						<slot name="leftAfter" v-if="leftSlidiSwitch && slotSlidiSwitch == 1" />
						<slot name="left" v-else/>
					</block>
				</view>
				<view class="hx-navbar__header-btns hx-navbar__content_view"  v-else :style="{'color': colorInfo}">
					<slot name="left"/>
				</view>
			  
			  
				<view class="hx-navbar__header-container hx-navbar__content_view">
					<view v-if="title.length" 
					 class="hx-navbar__header-container-inner hx-navbar__content_view">{{ title }}</view>
					<!-- 标题插槽 -->
				
					<block v-else>
						<slot name="centerAfter" v-if="centerSlidiSwitch && slotSlidiSwitch == 1"/>
						<slot v-else/>
						
					</block>
				</view>
				
				<view :class="title.length?'hx-navbar__header-btns-right':''"
					class="hx-navbar__header-btns hx-navbar__content_view"
					@tap="onClickRight"
					v-if="rightSlot">
					<!-- 优先显示图标 -->
					<block v-if="rightIcon.length || rightText.length">
						<view  class="hx-navbar__content_view" v-if="rightIcon.length">
							<uni-icons :type="rightIcon" :color="colorInfo" size="28"/>
						</view>
						<view v-if="rightText.length" class="hx-navbar-btn-text hx-navbar__content_view">{{ rightText }}</view>
					</block>
					<block v-else>
						<slot name="rightAfter"  v-if="rightSlidiSwitch && slotSlidiSwitch == 1"/>
						<slot name="right" v-else/>
					</block>
					
					
				</view>
			  
			</view>
		</view>
		
		<view
		  v-if="placeholder" 
		  class="hx-navbar__placeholder">
		  <view :style="{ height: statusBarHeight}" class="hx-status-bar" v-if="statusBar" ></view>
		 
		  <view :style="{ height: height}" />
		</view>
	</view>
	
</template>

<script>
	import uniIcons from '../uni-icons/uni-icons.vue'
	//获取系统状态栏高度
	var statusBarHeight = uni.getSystemInfoSync().statusBarHeight  + 'px';
	export default {
		name: "hx-navbar",
		components: {
		  uniIcons
		},
		data() {
			return {
				 statusBarHeight: statusBarHeight,
				 transparentValue: 0,
				 navTransparentFixedFontColor: '#fff',
				 statusBarFontColorInfo: [],
				 backgroundColorRgba: 'rgba(255,255,255,1)',
				 backgroundColorRgb: 'rgb(222,222,222)',
				 colorInfo: '#000000',
				 placeholder: false,
				 colorContainer: null,
				 slotSlidiSwitch: 0
				 
			};
		},
		props:{
			leftImg: {
				type: Boolean,
				default: false
			},
			height:{
				type: String,
				default: "44px"
			},
			//导航栏占位符 显示（show），隐藏（hidden），自动（auto：如果头部为固定fiexd ，则显示占位符）
			barPlaceholder:{
				type: String,
				default: "auto"
			},
			//返回上一页
			back:{
				type: [Boolean, String],
				default: true
			},
			//标题
			title: {
			  type: String,
			  default: ''
			},
			//是否开启左插槽
			leftSlot:{
				type: [Boolean, String],
				default: true
			},
			//是否开启右插槽
			rightSlot:{
				type: [Boolean, String],
				default: true
			},
		
			//左边文字
			leftText: {
			  type: String,
			  default: ''
			},
			//右插槽文字
			rightText: {
			  type: String,
			  default: ''
			},
			//左插槽图标
			leftIcon: {
			  type: String,
			  default: ''
			},
			//右插槽图标
			rightIcon: {
			  type: String,
			  default: ''
			},
			//是否固定头
			fixed: {
			  type: [Boolean, String],
			  default: false
			},
			//文字颜色
			color: {
			  type: [Array,String],
			  default: "#000000"
			},
			//导航栏背景颜色
			backgroundColor: {
			  type: Array,
			  default: function(){
				  return new Array([255,255,255],[255,255,255]);
			  }
			},
			//线性渐变角度
			backgroundColorLinearDeg: {
				type: String,
				default: '45'
			},
			//背景图片
			backgroundImg: {
				type: String,
				default: ''
			},
			//背景透明（show,hidden,auto）
			transparent: {
				type: String,
				default: 'show'
			},
			//状态栏字体颜色，只支持黑（#000000）和白（#FFFFFF）两种颜色。（,）
			statusBarFontColor:{
				type: [Array,String],
				default:function(){
				  return new Array("#000000","#000000");
				} 
			},
			//是否包含状态栏
			statusBar: {
			  type: [Boolean, String],
			  default: true
			},
			//状态栏背景颜色
			statusBarBackground:{
				type: String,
				default: ''
			},
			//导航栏阴影
			shadow: {
			  type: [String, Boolean],
			  default: false
			},
			//导航栏边框
			border: {
			  type: [String, Boolean],
			  default: false
			},
			//跳至普通页面
			defaultBackUrl: {
			  type: String,
			  default: ''
			},
			//跳至tabber页面
			backTabbarUrl: {
			  type: String,
			  default: '/pages/index/index'
			},
			//滑动后切换左插槽
			leftSlidiSwitch:{
				type: [Boolean,String],
				default: false,
			},
			//滑动后切换中间插槽
			centerSlidiSwitch:{
				type: [Boolean,String],
				default: false
			},
			//滑动后切换右插槽
			rightSlidiSwitch:{
				type: [Boolean,String],
				default: false
			},
			//页面的onPageScroll
			pageScroll:{
				type: Object,
				default:function(){
				  return {}
				} 
			},
			
		},
		created(){
			var that = this;
			//是否添加占位符
			switch (that.barPlaceholder){
				case 'show':
					that.placeholder = true;
					break;
				case 'hidden':
					that.placeholder = false;
					break;
				case 'auto':
					if(that.fixed){
						that.placeholder = true;
					}
					break;
			}
			
			//设置状态栏文字颜色
			that.setStatusBarFontColor();

			//文字颜色
			that.colorContainer = typeof that.color == 'object' ?  that.color : [that.color,that.color];
			that.colorInfo = that.colorContainer[0];
			//导航栏透明设置 及监听滚动
			switch (that.transparent){
				case 'show':
					that.transparentValue = 1;
					break;
				case 'hidden':
					that.transparentValue = 0;
					break;
				case 'auto':
					this.setTVAuto(that.pageScroll)
					break;
			}
			that.setBgColor();
			
			//滑动切换
			if(that.fixed && (that.leftSlidiSwitch || that.centerSlidiSwitch || that.rightSlidiSwitch)){
				that.doScroll(that.pageScroll);
			}
			
		},
		watch:{
			pageScroll(val,oldVal){
				var that = this;
				//导航栏透明设置 及监听滚动
				switch (that.transparent){
					case 'show':
						that.transparentValue = 1;
						break;
					case 'hidden':
						that.transparentValue = 0;
						break;
					case 'auto':
						this.setTVAuto(val)
						break;
				}
				//滑动切换
				if(that.fixed && (that.leftSlidiSwitch || that.centerSlidiSwitch || that.rightSlidiSwitch)){
					that.doScroll(val);
				}
			},
			//监控透明度变化 
			transparentValue(val,oldVal) {
				var that = this;
				//this.settingColor();
				
				//头条小程序不支持setNavigationBarColor方法
				// #ifndef MP-TOUTIAO || H5
				if(oldVal > 0.8){
					uni.setNavigationBarColor({
						frontColor: that.statusBarFontColorInfo[1],
						backgroundColor: that.backgroundColorRgb
					});
				}else if(oldVal < 0.2){
					uni.setNavigationBarColor({
						frontColor: that.statusBarFontColorInfo[0],
						backgroundColor:  that.backgroundColorRgb
					});
				}
				// #endif
				
				// #ifdef MP-TOUTIAO
				if (tt.setNavigationBarColor) {
				 if(oldVal > 0.8){
				 	tt.setNavigationBarColor({
				 	  frontColor: that.statusBarFontColorInfo[1],
				 	  backgroundColor: that.backgroundColorRgb,
				 	  success(res) {},
				 	  fail(res) {}
				 	});
				 }else if(oldVal < 0.2){
				 	tt.setNavigationBarColor({
				 	    frontColor: that.statusBarFontColorInfo[0],
				 	    backgroundColor: that.backgroundColorRgb,
				 		success(res) {},
				 		fail(res) {}
				 	});
				 }
				} else {
				  console.log("hx-navbar 提示：当前客户端版本过低，无法使用状态栏颜色修改功能，请升级（基础库1.40+）。")
				}
				// #endif
			},
			//监听背景颜色
			backgroundColor(val,old){
				var that = this;
				that.setBgColor()
			},
		
			color(val,old){
				var that = this;
				//文字颜色
				/* that.colorContainer = typeof val == 'object' ?  val : [val,val];
				that.colorInfo = that.colorContainer[0]; */
				
			}
		},
		methods: {
			
			onClickLeft () {
				if(this.back){
					if(getCurrentPages().length>1){
						uni.navigateBack();
					}else{
						// #ifdef H5
						history.back()
						// #endif
						// #ifndef H5
						if(this.defaultBackUrl){
							uni.redirectTo({
								url:this.defaultBackUrl
							})
						}else{
							if(this.backTabbarUrl){
								uni.reLaunch({
									url: this.backTabbarUrl
								});
							}
							
						}
						// #endif
					}
					
				}else{
					this.$emit('click-left')
				}
		    },
		    onClickRight () {
				this.$emit('click-right')
		    }, 
			
			//监听滚动后的操作
			doScroll(e){
				let that = this;
				that.$emit('scroll', e);
				if (e.scrollTop > 100) {
					that.slotSlidiSwitch = 1;
				} else {
					that.slotSlidiSwitch = 0
				}
			},
			//滑动渐变
			setTVAuto(e){
				let that = this;
				that.$emit('scroll', e);
				if (e.scrollTop > 100) {
					that.transparentValue = 1;
					that.colorInfo = that.colorContainer[1];
				} else {
					that.transparentValue = e.scrollTop / 100;
					that.colorInfo = that.colorContainer[0];
				}
				that.setBgColor();
			},
			//背景颜色
			setBgColor(){
				
				var that = this;
				//如果存在背景图片则背景颜色失效
				// if(that.backgroundImg){
				// 	that.backgroundColorRgba = "url(" + that.backgroundImg + ")";
				// 	return;
				// }
				
				//背景颜色
				if(typeof that.backgroundColor[0] == 'object'){
					let l = that.backgroundColor.length;
					if( l >= 2){
						let rgbStr = "linear-gradient("+ that.backgroundColorLinearDeg +"deg,";
						let c = null;
						for(var i in that.backgroundColor){
							c = that.backgroundColor[i];
							rgbStr += "rgba("+ c[0] + "," + c[1] + "," + c[2] +"," + that.transparentValue+")";
							
							if(l != (i*1)+1){
								rgbStr += ",";
							}
						}
						rgbStr += ")"; 
						that.backgroundColorRgba = rgbStr;
					}
					
				}else{
					let rgbStr = that.backgroundColor[0] + ','+  that.backgroundColor[1] + ','+  that.backgroundColor[2];
					that.backgroundColorRgb= 'rgb('+ rgbStr + ')';
					that.backgroundColorRgba = 'rgba('+ rgbStr +',' + that.transparentValue+')';
				}
			},
			setStatusBarFontColor(){
			  var that = this;
			  if(typeof that.statusBarFontColor == 'string'){
				that.statusBarFontColorInfo = [that.statusBarFontColor,that.statusBarFontColor];
			  }else if(typeof that.statusBarFontColor == 'object'){
				if (that.statusBarFontColor.length==1){
					that.statusBarFontColorInfo = [that.statusBarFontColor[0],that.statusBarFontColor[0]];
				}else if(that.statusBarFontColor.length>=2){
					that.statusBarFontColorInfo = [that.statusBarFontColor[0],that.statusBarFontColor[1]];
				}
			  }
			  // #ifndef MP-TOUTIAO || H5
			  uni.setNavigationBarColor({
				frontColor: that.statusBarFontColorInfo[0],
				backgroundColor: that.backgroundColorRgb
			  });
			   // #endif
			   
			  // #ifdef MP-TOUTIAO
			  if (tt.setNavigationBarColor) {
			    tt.setNavigationBarColor({
			      frontColor: that.statusBarFontColorInfo[0],
			      backgroundColor: that.backgroundColorRgb
			    });
			  } else {
			     console.log("hx-navbar 提示：当前客户端版本过低，无法使用状态栏颜色修改功能，请升级（基础库1.40+）。")
			  }
			  // #endif
			}
		  
		},
		destroyed(){
			
		},
		
		
	}
</script>

<style lang="scss">
	$nav-height: 44px;
	
	.hd{
		overflow: hidden;
	}
	
	//防止其他ui影响
	.hx-navbar uni-view,
	.hx-navbar uni-scroll-view,
	.hx-navbar uni-swiper,
	.hx-navbar uni-button,
	.hx-navbar uni-input,
	.hx-navbar uni-textarea,
	.hx-navbar uni-label,
	.hx-navbar uni-navigator,
	.hx-navbar uni-image {
		box-sizing: unset;
	}
	.hx-navbar {
		position: relative;
		padding-top: 0;
		overflow: hidden;
		
		&__content {
			display: block;
			position: relative;
			width: 100%;
			/*background-color: $uni-bg-color*/;
			overflow: hidden;
			.navbgimg{
				position: absolute;
				top: 0;
				left: 0;
				z-index: 0;
				width: 100%;
			}
			
			.hx-navbar__content_view {
				// line-height: $nav-height;
				display: flex;
				// align-items: center;
				
			}
			.hx-status-bar {
				display: block;
				width: 100%;
				height: 40px;
				height: var(--status-bar-height);
				position: relative;
				z-index: 1;
			}
		}
	
		&__header {
			position: relative;
			z-index: 1;
			display: flex;
			flex-direction: row;
			width: 100%;
			height:  $nav-height;
			line-height: $nav-height;
			font-size: 36upx;
			transition: color 0.5s ease 0s;
			&-btns {
				display: inline-flex;
				flex-wrap: nowrap;
				flex-shrink: 0;
				min-width: 30px;
				padding: 0 6px;
				// padding-left: 26rpx;
	
				&:first-child {
					// min-width: 54px;
					width: 60px;
					.hx-navbar__content_view {
						// padding:0  6px;
					}
					// padding-left: 0;
				}
	
				&:last-child {
					min-width: 54px;
				}
	
	    &-right:last-child{
	      width: 120upx;
	      text-align: right;
	      flex-direction: row-reverse;
	    }
			}
	
			&-container {
				width: 100%;
				margin: 0 10upx;
	
				&-inner {
					// position: fixed;
					// left: 0;
					// width: 100%;
					margin: 0 auto;
					display: flex;
					justify-content: center;
					font-size: 34rpx;
					// font-size: 14px;
					font-weight: bold;
					text-align: center;
					// padding-right: 60upx;
				}
			}
		}
	
		&__placeholder {
			&-view {
				height: $nav-height;
			}
		}
	
		&--fixed {
			position: fixed;
			top:0;
			z-index: 998;
		}
	
		&--shadow {
			box-shadow: 0 2upx 12upx #ccc;
		}
	
		&--border:after {
			position: absolute;
			z-index: 3;
			bottom: 0;
			left: 0;
			right: 0;
			height: 1px;
			content: '';
			-webkit-transform: scaleY(.5);
			transform: scaleY(.5);
			background-color: #efefef;
		}
	}
	.left_back{
		padding-left: 6rpx;
		padding-right: 12rpx;
	}
</style>
