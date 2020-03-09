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
						<view v-if="!item.is" class='subscription-btn' @click="subscription(item.func, item.tmpl)">添加通知</view>
                        <view v-if="item.is" class='subscription-btn isTrue' @click="subscription(item.func, item.tmpl)">已加通知</view>
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
                    status: false,
                    is: false
    			},
    			{
    				name: '支付通知',
                    desc: '订单支付后通知我',
    				func: 'pay',
                    tmpl: '',
                    status: false,
                    is: false
    			},
                {
                	name: '待付通知',
                    desc: '未支付订单取消前通知我',
                	func: 'cancel',
                    tmpl: '',
                    status: false,
                    is: false
                },
                {
                	name: '发货通知',
                	desc: '订单发货后通知我',
                    func: 'ship',
                    tmpl: '',
                    status: false,
                    is: false
                },
                {
                	name: '售后通知',
                	desc: '订单售后结果通知我',
                    func: 'after_sale',
                    tmpl: '',
                    status: false,
                    is: false
                },
                {
                	name: '退款通知',
                	desc: '售后退款结果通知我',
                    func: 'refund',
                    tmpl: '',
                    status: false,
                    is: false
                },
    		]
    	}
    },
    onShow(){
        this.getSubscriptionTmplIds();
    },
    methods: {
        //获取模板
        getSubscriptionTmplIds: function () {
            this.$api.getSubscriptionTmplIds(res => {
                if (res.status) {
                    for (let i = 0; i < this.msgList.length; i++) {
                        this.msgList[i].tmpl = res.data[this.msgList[i].func].template_id;
                        this.msgList[i].is = res.data[this.msgList[i].func].is;
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
        subscription: function(func, tmpl){
            let _this = this;
            uni.requestSubscribeMessage({
                tmplIds: [tmpl],
                success (res) {
                    if (res.errMsg == "requestSubscribeMessage:ok") {
                        let data = {
                            'template_id': tmpl,
                            'status': res[tmpl]
                        }
                        _this.$api.setSubscriptionStatus(data, e => {
                            _this.getSubscriptionTmplIds();
                        });
                    } else {
                        _this.$common.errorToShow('操作失败，请稍候重试！', r => {
                            _this.getSubscriptionTmplIds();
                        });
                    }
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
    /* color: #000000; */
    display: block;
    width: 180rpx;
}
.cell-hd-desc{
    font-size: 24rpx;
    /* display: block; */
    width: 200rpx;
	
    color: #888888;
    /* margin-top: 50rpx; */
}
.subscription-btn{
    background-color: #333333;
    color: #ffffff;
    padding: 0 20rpx;
    line-height: 46rpx;
    margin-right: 20rpx;
    border: 1px solid #333333;
}
.isTrue{
    background-color: #ffffff;
    border: 1px solid #333333;
    color: #333333;
    line-height: 46rpx;
}
</style>