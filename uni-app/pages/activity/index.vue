<template>
	<view class="conbox">
		<view class="container">
			<image src="/static/img/bg.png" class="cont" mode=""></image>
			<image src="/static/img/caidai.png" class="caidai" mode=""></image>
			<view class="header">
				<view class="header-title">
					<view class="left">
						免费次数：<text style="color: #E4431A;">{{chishu}}</text>
					</view>
					<view class="left">
						账户积分：<text style="color: #E4431A;">{{jifen}}</text>
					</view>
					<view class="right" @click="getmyPrize">
						抽奖记录 >
					</view>
				</view>
			</view>
			<view class="main">
				<view class="canvas-container">
					<view :animation="animationData" class="canvas-content" id="zhuanpano" style="">
					<!-- <view :animation="animationData" class="canvas-content" id="zhuanpano"  :style="[{transform:'rotate('+runDeg+')'}]"> -->
						<!-- <canvas class="canvas" canvas-id="canvas"></canvas> -->
						<view class="canvas-line">
							<view class="canvas-litem" v-for="(item,index1) in awardsList" :key="index1" :style="[{transform:'rotate('+item.lineTurn+')'}]"></view>
						</view>
						<view class="canvas-list">
							<view class="canvas-item" :style="[{zIndex:index2}]" v-for="(iteml,index2) in awardsList" :key="index2">
								<view class="canvas-item-text" :style="[{transform:'rotate('+iteml.turn+')'}]">
									<text>{{iteml.award}}</text>
									<image class="canvas-item-text-img" src="/static/img/kongjiang.png" v-if="iteml.type == 0"></image>
									<image class="canvas-item-text-img" src="/static/img/jifen.png" v-if="iteml.type == 1"></image>
									<image class="canvas-item-text-img" src="/static/img/youhuiquan.png" v-if="iteml.type == 2"></image>
									<image class="canvas-item-text-img" src="/static/img/yue.png" v-if="iteml.type == 3"></image>
									<image class="canvas-item-text-img" src="/static/img/shangpin.png" v-if="iteml.type == 4"></image>
								</view>
							</view>
						</view>
					</view>
					<view @tap="playReward" class="canvas-btn" v-bind:class="btnDisabled">开始 </view>
				</view>
			</view>
			<view class="typecheckbox"></view>
			<!-- 规则 -->
			<view class="guize">
				<view class="title">
					规则说明
				</view>
				<view class="g_item" v-for="(v, k) in awardsConfig.rule" :key="k">
					{{v}}
				</view>
			</view>
			<view class="typecheckbox2"></view>
			<!-- 抽奖记录 -->
			<view class="shadowbox" v-if="r_flg" @click="closeshadow">
				<view class="myrewards" @click.stop="openshadow">
					<view class="title">
						抽奖记录
					</view>
					<view class="itembox">
						<view class="item" v-for="(items,i) in myPrizelist" :key="i">
							<div class="t">
								<text class="left">{{items.name}}</text>
								<text class="right">{{items.ctime_name}}</text>
							</div>
							<div class="b">
								{{items.prize_content}}
							</div>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				awardsConfig: {
					chance: true, //是否有抽奖机会
					prize: [], //奖品列表 
				},
				awardsList: {},
				animationData: {},
				btnDisabled: '',
				thanksarr: [], //存储谢谢参与的索引
				chishu: 0,
				mold: 1,
				r_flg: 0,
				myPrizelist:[],
				jifen: 0
			}
		},
		onLoad: function() {
			// 获取奖品列表
			this.initdata(this)
		},
		onReady: function(e) {

		},
		methods: {
			// 查看奖品
			getmyPrize(){
				this.$api.myLottery({page: 1, limit: 1000}, res => {
					this.myPrizelist = res.data
					this.r_flg=1
				})
			},
			openshadow(){
				this.r_flg=1
			},
			closeshadow(){
				this.r_flg=0
			},
			// 初始化抽奖数据
			initdata:function(that){
				this.$api.lotteryConfig(res => {
					if(res.status){
						this.awardsConfig = res.data
						this.chishu = res.data.user.day_remaining;
						this.jifen = res.data.user.jifen;
						// 获取奖品的个数
						let awarrlength = this.awardsConfig.prize.length
						// 为每一项追加index属性
						this.awardsConfig.prize.forEach(function(element, index) {
							element.index = index
						});
						
						// 画转盘
						this.drawAwardRoundel();
					}else{
						this.$common.errorToShow(res.msg, () => {
							uni.navigateBack({
								delta: 1
							});
						});
					}
				});
			},
			//画抽奖圆盘  
			drawAwardRoundel: function() {
				// 拿到奖品列表
				var awards = this.awardsConfig.prize;
				var awardsList = [];
				// 每份奖品所占角度
				var turnNum = 1 / awards.length * 360; // 文字旋转 turn 值  
				// 奖项列表  
				for (var i = 0; i < awards.length; i++) {
					awardsList.push({
						turn: i * turnNum + 'deg', //每个奖品块旋转的角度
						lineTurn: i * turnNum + turnNum / 2 + 'deg', //奖品分割线的旋转角度
						award: awards[i].title, //奖品的名字,
						type: awards[i].type,
						id: awards[i].id,
					});
				}
				if(this.chishu < 1 && this.jifen < this.awardsConfig.integral_exchange) {
					this.btnDisabled = 'disabled';
				}else{
					if(!this.awardsConfig.user.lottery){
						this.btnDisabled = 'disabled';
					}else{
						this.btnDisabled = '';
					}
				}
				
				this.awardsList = awardsList;
			},
			//发起抽奖  
			playReward: function() {
				if(this.chishu < 1) {
					if(this.jifen < this.awardsConfig.integral_exchange) {
						this.$common.errorToShow('抽奖次数已经用完');
						return false;
					} else if (this.jifen >= this.awardsConfig.integral_exchange) {
						this.$common.modelShow('提示', '本次抽奖将消耗'+this.awardsConfig.integral_exchange+'积分，确认吗？', res => {
							this.lottery();
						});
					}
				}else{
					this.lottery();
				}
			},
			lottery: function () {
				// 抽奖
				this.$api.lottery(res => {
					if(res.status) {
						let awardIndex = 0;
						let awardInfo = res.data.result;
						
						//获取抽奖结果
						this.awardsList.forEach(function(element, index) {
							if (element.id == awardInfo.id) {
								awardIndex = index;
							}
						})
						
						//中奖index  
						let awardsNum = this.awardsConfig.prize;
						let runNum = 4; //旋转8周
						let duration = 3686; //时长
										
						// 旋转角度  
						this.runDeg = this.runDeg || 0;
						let preDeg = this.runDeg;
						this.runDeg = this.runDeg + (360 - this.runDeg % 360) + (360 * runNum - awardIndex * (360 / awardsNum.length)) + 1;
						
						//创建动画  
						if(process.env.VUE_APP_PLATFORM == 'h5'){
							// document.styleSheets[0]
							document.getElementById('zhuanpano').style='animation:rotate_before 4s 0ms ease forwards;'
							if(document.styleSheets[0].cssRules.length>0){
								Array.from(document.styleSheets[0].cssRules).forEach(function(element,index){
									if(element.name == 'rotate_before'){
										// 删除上次插入的动画
										document.styleSheets[0].deleteRule(index)
									}
								})
							}
							
							// console.log(document.styleSheets[0].cssRules)
							// console.log("@keyframes rotate_before{from{ transform: rotate("+preDeg+"deg); }to{ transform: rotate("+this.runDeg+"deg);}}")
							// 插入定义的动画
							document.styleSheets[0].insertRule("@keyframes rotate_before{from{ transform: rotate("+preDeg+"deg); }to{ transform: rotate("+this.runDeg+"deg);}}",8);
						}else{
							var animationRun = uni.createAnimation({
								duration: duration,
								timingFunction: 'ease'
							})
							animationRun.rotate(this.runDeg).step();
							this.animationData = animationRun.export();
						}
						// 					// #ifndef H5
						// 					console.log(document.styleSheets)
						// 					document.getElementById('zhuanpano')
						// 					// #endif
						this.btnDisabled = 'disabled';
						
						// 中奖提示  
						var awardsConfig = this.awardsConfig;
						var awardType = awardsConfig.prize[awardIndex].type;
						this.jifen = this.chishu <= 0 ? (this.jifen - awardsConfig.integral_exchange >= 0 ? this.jifen - awardsConfig.integral_exchange : 0) : this.jifen;
						this.chishu = this.chishu > 1 ? this.chishu - 1 : 0;
						if (awardType != 0) {
							let msg = this.getPrizeMsg(awardsConfig.prize[awardIndex].type, awardsConfig.prize[awardIndex].val);
							setTimeout(function() {
								this.$common.modelShow('恭喜', '获得' + (awardsConfig.prize[awardIndex].title) + '，' + msg, res => {
									setTimeout(function(){
										document.getElementById('zhuanpano').style=''
									},1000)
								}, false);
								if(!res.data.is_lottery.lottery){
									this.btnDisabled = 'disabled';
								}else{
									this.btnDisabled = '';
								}
							}.bind(this), duration);
						} else {
							setTimeout(function() {
								this.$common.modelShow('很遗憾', '没中奖，再接再厉！', res => {
									setTimeout(function(){
										document.getElementById('zhuanpano').style=''
									},1000)
								}, false);
								if(!res.data.is_lottery.lottery){
									this.btnDisabled = 'disabled';
								}else{
									this.btnDisabled = '';
								}
							}.bind(this), duration);
						}
					} else {
						this.$common.modelShow('提示', res.msg);
					}
				});
			},
			//获取显示的奖品信息
			getPrizeMsg: function(type, val){
				let msg = '';
				switch(type){
					case 1: //积分
						msg = '积分：' + val + '个';
						break;
					case 2: //优惠券
						msg = '优惠券：“' + val + '” 一张';
						break;
					case 3: //余额
						msg = '余额：' + val + '元';
						break;
					case 4: //商品
						msg = '商品：“' + val + '”';
						break;
					default: //默认
						break;
				}
				return msg;
			}
		}
	}
</script>
<style scoped>
	.conbox {
		width: 750upx;
		height: 100vh;
		overflow-x: hidden;
		overflow-y: scroll;
	}

	.container,
	image.cont {
		width: 750upx;
		min-height: 100vh;
		height: auto;
		position: relative;
	}

	image.cont {
		height: 100%;
		position: absolute;
		z-index: 0;
	}

	image.caidai {
		position: absolute;
		top: 0;
		left: 0;
		width: 750upx;
		height: 1024upx;
	}

	.header {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		/* height: 246upx; */
		padding-top: 48upx;
		padding-bottom: 40upx;
		box-sizing: border-box;
		position: relative;
		z-index: 3;
	}

	.header-title {
		width: 100%;
		height: 60upx;
		display: flex;
		align-items: center;
		padding: 0 48upx;
		box-sizing: border-box;
		justify-content: space-between;
	}

	.header-title>view {
		padding: 8upx 16upx;
		border: 1px solid #d89720;
		color: #d89720;
		font-size: 28upx;
		border-radius: 26upx;
	}

	/* 转盘 */
	.canvas-container {
		margin: 0 auto;
		position: relative;
		width: 600upx;
		height: 600upx;
        background: url("../../static/img/circle.png") no-repeat;
		background-size: cover;
		border-radius: 50%;
	}

	.canvas {
		width: 100%;
		height: 100%;
		display: block !important;
		border-radius: 50%;
	}

	.canvas-content {
		position: absolute;
		left: 0;
		top: 0;
		z-index: 1;
		display: block;
		width: 600upx;
		height: 600upx;
		border-radius: inherit;
		/* background-clip: padding-box; */
		/* background-color: #ffcb3f; */
	}

	.canvas-element {
		position: relative;
		z-index: 1;
		width: inherit;
		height: inherit;
		border-radius: 50%;
	}

	.canvas-list {
		position: absolute;
		left: 0;
		top: 0;
		width: inherit;
		height: inherit;
		z-index: 9999;
	}

	.canvas-item {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		color: #e4370e;
		/* text-shadow: 0 1upx 1upx rgba(255, 255, 255, 0.6); */
	}

	.canvas-item-text {
		position: relative;
		display: block;
		padding-top: 46upx;
		margin: 0 auto;
		text-align: center;
		-webkit-transform-origin: 50% 300upx;
		transform-origin: 50% 300upx;
		display: flex;
		flex-direction: column;
		align-items: center;
		color: #FB778B;
	}

	.canvas-item-text text {
		font-size: 30upx;
	}

	.canvas-item-text-img {
		width: 50upx;
		height: 50upx;
		padding-top: 30upx;
	}

	/* 分隔线 */
	.canvas-line {
		position: absolute;
		left: 0;
		top: 0;
		width: inherit;
		height: inherit;
		z-index: 99;
	}

	.canvas-litem {
		position: absolute;
		left: 300upx;
		top: 0;
		width: 3upx;
		height: 300upx;
		background-color: rgba(228, 55, 14, 0.4);
		overflow: hidden;
		-webkit-transform-origin: 50% 300upx;
		transform-origin: 50% 300upx;
	}

	/**  
* 抽奖按钮  
*/
	.canvas-btn {
		position: absolute;
		left: 260upx;
		top: 260upx;
		z-index: 400;
		width: 80upx;
		height: 80upx;
		border-radius: 50%;
		color: #f4e9cc;
		background-color: #e44025;
		line-height: 80upx;
		text-align: center;
		font-size: 26upx;
		text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.6);
		box-shadow: 0 3px 5px rgba(0, 0, 0, 0.6);
		text-decoration: none;
	}

	.canvas-btn::after {
		position: absolute;
		display: block;
		content: ' ';
		left: 12upx;
		top: -44upx;
		width: 0;
		height: 0;
		overflow: hidden;
		border-width: 30upx;
		border-style: solid;
		border-color: transparent;
		border-bottom-color: #e44025;
	}

	.canvas-btn.disabled {
		pointer-events: none;
		background: #b07a7b;
		color: #ccc;
	}

	.canvas-btn.disabled::after {
		border-bottom-color: #b07a7b;
	}

	.canvas-btn-table {
		color: #A83FDB;
		width: 120upx;
		text-align: center;
		position: absolute;
		left: 240upx;
		top: 360upx;
		font-size: 26upx;
		background-color: #FFFFFF;
		opacity: 0.9;
	}

	.typecheckbox {
		width: 100%;
		position: relative;
		z-index: 3;
		display: flex;
		justify-content: space-between;
		padding: 20upx;
		box-sizing: border-box;
		color: #fff;
		font-size: 28upx;
		top: -120upx;
		flex-direction: column;
		height: 180upx;
		align-items: flex-end;
		/* padding-top: 46upx; */
	}
	
	.typecheckbox2{
		width: 100%;
		position: relative;
		z-index: 3;
		display: flex;
		justify-content: space-between;
		padding: 20upx;
		box-sizing: border-box;
		color: #fff;
		font-size: 28upx;
		top: -120upx;
		flex-direction: column;
		height: 120upx;
		align-items: flex-end;
		/* padding-top: 46upx; */
	}

	.typecheckbox view {
		border: 1px solid #FF3637;
		background: transparent;
		color: #FF3637;
		display: flex;
		height: 60upx;
		width: 140upx;
		border-radius: 50upx;
		align-items: center;
		justify-content: center;
		display: flex;
		margin-left: 20upx;
	}

	.typecheckbox view.active {
		background: #FF3637;
		color: #fff;
	}

	.guize {
		width: 502upx;
		min-height: 300upx;
		display: flex;
		flex-direction: column;
		position: relative;
		z-index: 3;
		background-image: linear-gradient(-180deg, #F48549 0%, #F2642E 100%);
		border: 18upx solid #E4431A;
		border-radius: 16px;
		margin: 0 auto;
		margin-top: -104upx;
		padding: 48upx;
		/* box-sizing: border-box; */
		color: #fff;
	}

	.guize .title {
		text-align: center;
		margin-bottom: 28upx;
	}

	.guize .g_item {
		font-family: PingFang-SC-Medium;
		font-size: 24upx;
		color: #FFFFFF;
		letter-spacing: 0.5px;
		text-align: justify;
		line-height: 20px;
	}

	.shadowbox {
		width: 750upx;
		height: 100vh;
		position: fixed;
		top: 0;
		left: 0;
		z-index: 999;
		background: rgba(0, 0, 0, .6);
		display: flex;
		justify-content: center;
		align-items: center;

	}

	.myrewards {
		width: 600upx;
		min-height: 80upx;
		background: #FFEEDF;
		border: 10upx solid #F2692F;
		color: #333;
		font-size: 24upx;
		font-family: PingFang-SC-Medium;
		border-radius: 40upx;
		padding:0 24upx 20upx;
	}

	.myrewards .title {
		font-family: PingFang-SC-Bold;
		font-size: 16px;
		color: #E4431A;
		letter-spacing: 0.57px;
		display: flex;
		padding-top: 36upx;
		justify-content: center;
	}

	.myrewards .itembox {

		max-height: 320upx;
		overflow-y: auto;
	}

	.myrewards .item {
		width: 100%;
		padding: 12upx 0;
		box-sizing: border-box;
		border-bottom: 1upx dashed #CCCCCC;

	}
	.myrewards .item .t{
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom:10upx;
	}
	.myrewards .item .b{
		font-size: 12px;
		color:#999999;
		text-align: left;
	}
</style>
