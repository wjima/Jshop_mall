<template>
	<view class="content">
		<view class="apply-c">
			<view class="apply-top">
				<view class='cell-group'>
					<view class='cell-item'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>姓名</view>
						</view>
						<view class='cell-item-bd'>
							<input type="text" class='cell-bd-input' placeholder='请填您的姓名' v-model="name"></input>
						</view>
					</view>
					<view class='cell-item'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>微信</view>
						</view>
						<view class='cell-item-bd'>
							<input type="text" class='cell-bd-input' placeholder='请填您的微信' v-model="weixin"></input>
						</view>
					</view>
					<view class='cell-item'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>QQ</view>
						</view>
						<view class='cell-item-bd'>
							<input type="number" class='cell-bd-input' placeholder='请填您的QQ' v-model="qq"></input>
						</view>
					</view>
					<view class='cell-item'>
						<view class='cell-item-hd'>
							<view class='cell-hd-title'>手机</view>
						</view>
						<view class='cell-item-bd'>
							<input type="number" class='cell-bd-input' placeholder='请填写您的手机号码' v-model="mobile"></input>
						</view>
					</view>
					
				</view>
				<view class="apply-tip color-6 fsz26">
					<label class="radio" @click="agreeAgreement"><radio value="1" :checked="checked" color="#FF7159"/>我已经阅读并接受</label><text class="agreement" @click="goAgreement()">"分销协议"</text>
				</view>
			</view>
			
			<view class="apply-bot">
				<button class="btn btn-square btn-o btn-all" hover-class="btn-hover" @click="goApplyState()">申请成为分销</button>
			</view>
		</view>
	</view>
</template>

<script>
export default {
    data() {
        return {
            name: '',
			weixin: '',
			qq: '',
            mobile: '',
			checked: false,
			is_agree: 'off'
        }
    },
    methods: {
		// 是否同意协议
		agreeAgreement(){
			
			if(this.checked){
				this.checked = false;
				this.is_agree = 'off';
			}else{
				this.checked = true;
				this.is_agree = 'on';
			}
			// console.log(this.checked)
		},
		// 信息验证
		checkData (data) {
			if (!data.name) {
				this.$common.errorToShow('请输入您的姓名')
				return false
			} else if (!data.weixin) {
				this.$common.errorToShow('请输入您的微信')
				return false
			} else if (!data.qq) {
				// console.log(this.is_agree)
				this.$common.errorToShow('请输入您的QQ')
				return false
			} else if (!data.mobile) {
				this.$common.errorToShow('请输入您的手机号')
				return false
			} else if (data.mobile.length !== 11) {
				this.$common.errorToShow('手机号格式不正确')
				return false
			} else if (data.agreement != 'on') {
				//console.log(data)
				this.$common.errorToShow('请勾选分销协议')
				return false
			} else {
				return true
			}
		},
		// 提交审核
		goApplyState() {
			let data = {
				name: this.name,
				weixin: this.weixin,
				qq: this.qq,
				mobile: this.mobile,
				agreement: this.is_agree,
			}
			
			if (this.checkData(data)) {
				this.$api.applyDistribution(data, res => {
					if(res.status){
						this.$common.successToShow(res.msg, function(){
							uni.navigateTo({
								url:'./apply_state'
							});
						});
					}else{
						this.$common.errorToShow(res.msg);
					}
				});
			}
		},
		goAgreement(){
			uni.navigateTo({
				url: './agreement'
			})
		}
        
    }
}
</script>

<style>
.content{
	background-color: #FF7159;
	height: calc(100vh - 44px);
	padding-top: 50upx;
}
.apply-c{
	margin: 40upx auto;
	padding: 26upx 0;
	border-radius: 30upx;
	box-shadow: 0 0 10px #aaa;
	width: 670upx;
	min-height: 400upx;
	background-color: #fff;
}
.apply-top .cell-item{
	width: 610upx;
}
.apply-top .cell-item:last-child{
	border-bottom: 1px solid #f3f3f3;
}
.apply-top .cell-item .cell-item-hd{
	min-width: 120upx;
}
.apply-top .cell-item .cell-bd-input{
	width: 100%;
}
.apply-tip{
	padding: 26upx;
}
.apply-bot{
	width: 100%;
	text-align: center;
}
.apply-bot .btn{
	border-radius: 50upx;
	width: 90%;
	margin: 40upx auto 0;
}
.agreement{
	text-decoration: underline;
	color: #FF7159;
}
</style>
