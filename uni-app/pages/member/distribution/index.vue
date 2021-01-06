<template>
	<view class="content">
		<view class="content-top">
			<view class="dist-head">
				<view class="dist-head-top">
					<view class="dht-margin color-f fsz34" v-if="condition.condition_status">已达标</view>
					<view class="dht-margin color-f fsz34" v-if="!condition.condition_status">未达标</view>

					<cmd-progress
						class="dht-margin"
						:percent="condition.condition_progress"
						:stroke-width="23"
						stroke-color="linear-gradient(to right, #ef32d9, #89fffd)"
					></cmd-progress>

					<view class="dht-margin color-d fsz28">{{ condition.condition_msg }}</view>
				</view>
				<view class="dist-head-tip color-f fsz24">注：消费金额只算实付金额部分，储值抵扣/退款退货金额不算在内。</view>
			</view>
			<view class="dist-body">
				<view class="db-title color-3 fsz34">分销商须知</view>
				<view class="db-body color-6 fsz30">
					<text class="db-item">{{ distributionNotes }}</text>
				</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-o" hover-class="btn-hover" v-if="condition.condition_status" @click="goApply()">申请</button>
			<button class="btn btn-square btn-g" v-else>您的条件暂不满足</button>
		</view>
	</view>
</template>

<script>
import cmdProgress from '@/components/cmd-progress/cmd-progress.vue';
export default {
	components: { cmdProgress },
	data() {
		return {
			condition: {}
		};
	},
	methods: {
		goApply() {
			this.$common.navigateTo('./apply');
		}
	},
	computed: {
		distributionNotes() {
			return this.$store.state.config.distribution_notes;
		}
	},
	onLoad: function() {
		var _this = this;
		_this.$api.getDistributioninfo({}, function(res) {
			if (res.status) {
				_this.condition = res.data;
				console.log(res.data);
				if (_this.condition.hasOwnProperty('verify')) {
					if(_this.condition.verify == 1){//审核通过
						_this.$common.redirectTo('/pages/member/distribution/user');
					}else if(_this.condition.verify == 2 || _this.condition.verify == 3){//等等审核
						_this.$common.redirectTo('/pages/member/distribution/apply_state');
					}else{//检查条件是否满足
						if(_this.condition.need_apply &&  _this.condition.condition_status){//需要审核，并且条件满足
							_this.$common.redirectTo('/pages/member/distribution/apply'); 
						}else if(_this.condition.need_apply && !_this.condition.condition_status){//需要审核，并且条件不满足
							
						}else if(!_this.condition.need_apply && _this.condition.condition_status){//不需要审核，并且条件满足
							_this.$common.redirectTo('/pages/member/distribution/user');
						}else if(!_this.condition.need_apply && !_this.condition.condition_status){//不需要审核，并且条件不满足
							
						}
					}
				}
			} else {
				//报错了
				_this.$common.errorToShow(res.msg);
			}
		});
	}
};
</script>

<style>
.content {
	background-color: #fff;
	height: calc(100vh - 44px);
}
.dist-head {
	padding: 50upx 26upx 20upx;
	text-align: center;
	background: linear-gradient(#ff7159, #ff9785);
}
.dist-head-top {
	padding: 0upx 50upx 30upx;
}
.dht-margin {
	margin-bottom: 26upx;
}
.dht-margin.color-d {
	padding: 0 40upx;
}
.dht-mid {
	margin-bottom: 12upx;
}
.dist-head-tip {
	text-align: left;
}
.dist-body {
	padding: 26upx;
}
.db-title {
	border-bottom: 2upx solid #ccc;
	padding-bottom: 26upx;
}
.db-body {
	padding: 26upx 10upx;
}
.db-item {
	margin-bottom: 14upx;
}
pre {
	white-space: pre-wrap; /* css-3 */
	white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
	white-space: -pre-wrap; /* Opera 4-6 */
	white-space: -o-pre-wrap; /* Opera 7 */
	word-wrap: break-word; /* Internet Explorer 5.5+ */
}
</style>
