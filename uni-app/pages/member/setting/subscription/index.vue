<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group right-img'>
				<view class='cell-item' v-for="(item, i) in msgList" :key="i" v-if="item.status">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>{{item.name}}</view>
                        <view class='cell-hd-desc'>{{item.desc}}</view>
					</view>
					<view class='cell-item-ft'>
						<view class='subscription-btn' @click="subscription(item.func)">添加通知</view>
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
    		msgList: [
                {
    				name: '下单通知',
                    desc: '商城下单成功后通知我',
    				func: 'order',
                    tmpl: '',
                    status: false
    			},
    			{
    				name: '支付通知',
                    desc: '订单支付后通知我',
    				func: 'pay',
                    tmpl: '',
                    status: false
    			},
                {
                	name: '待付通知',
                    desc: '未支付订单取消前通知我',
                	func: 'cancel',
                    tmpl: '',
                    status: false
                },
                {
                	name: '发货通知',
                	desc: '订单发货后通知我',
                    func: 'ship',
                    tmpl: '',
                    status: false
                },
                {
                	name: '售后通知',
                	desc: '订单售后结果通知我',
                    func: 'after_sale',
                    tmpl: '',
                    status: false
                },
                {
                	name: '退款通知',
                	desc: '售后退款结果通知我',
                    func: 'refund',
                    tmpl: '',
                    status: false
                },
    		]
    	}
    },
    onLoad(){
        this.getSubscriptionTmplIds();
    },
    methods: {
        //获取模板
        getSubscriptionTmplIds: function () {
            this.$api.getSubscriptionTmplIds(res => {
                if (res.status) {
                    for (let i = 0; i < this.msgList.length; i++) {
                        this.msgList[i].tmpl = res.data[this.msgList[i].func].template_id;
                        if (this.msgList[i].tmpl != '') {
                            this.msgList[i].status = true;
                        }
                    }
                } else {
                    this.$common.errorToShow('消息订阅配置信息获取失败');
                }
            });
        },
        //发起订阅
        subscription: function(func){
            var tmplIds = ['TT7opZ2JmxkynB7363sCq1J38pP2kaD0TIsuaxHI5fg', 'dR_j3i31KJREAJk-3gKz0BXO903M0AdXzMWRcsw9QRM'];
            switch(func){
                case 'order':
                    
                    break;
                case 'pay':
                
                    break;
                case 'cancel':
                    tmplIds = ['dR_j3i31KJREAJk-3gKz0BXO903M0AdXzMWRcsw9QRM'];
                    break;
                case 'ship':
                    tmplIds = ['TT7opZ2JmxkynB7363sCq1J38pP2kaD0TIsuaxHI5fg'];
                    break;
                case 'after_sale':
                
                    break;
                case 'refund':
                
                    break;
            }
            uni.requestSubscribeMessage({
                tmplIds: tmplIds,
                success (res) {
                    //console.log(res);
                }
            });
        }
    }
}
</script>
<style>
.agreement{
	position: fixed;
	bottom: 30rpx;
	width: 100%;
	margin: 20rpx 0;
	text-align: center;
}
.color-o{
	margin: 0 10rpx;
}
.cell-hd-title{
    font-size: 30rpx;
    color: #000000;
    display: block;
    width: 100%;
}
.cell-hd-desc{
    font-size: 24rpx;
    display: block;
    width: 100%;
    color: #888888;
    margin-top: 50rpx;
}
.subscription-btn{
    background-color: #333333;
    color: #ffffff;
    padding: 0 20rpx;
    line-height: 50rpx;
    margin-right: 20rpx;
}
</style>