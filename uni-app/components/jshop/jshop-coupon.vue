<template>
	<view class="coupon bottom-cell-group" v-if="count">
		<view class="coupon-item" v-for="item in jdata.params.list" :key="item.id" @click="receiveCoupon(item.id)">
			<view class="coupon-i-l">
				<view class="coupon-i-l-t">
					<image class="icon" src="/static/image/element-ic.png" mode=""></image>
					<text>{{ item.name }}</text>
				</view>
				<view class="coupon-i-l-b">
					{{ item.expression1 + item.expression2 }}
				</view>
			</view>
			<view class="coupon-i-r">
				<image class="coupon-logo" src="/static/image/coupon-element.png" mode=""></image>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	name: "jshopcoupon",
	props: {
		jdata:{
			// type: Array,
			required: true,
		}
	},
	computed: {
		count() {  
			return (this.jdata.params.list.length > 0)
		}
	},
	methods: {
		// 用户领取优惠券
		receiveCoupon(couponId) {
			let jdata = {
				promotion_id: couponId
			}
			this.$api.getCoupon(jdata, res => {
				if (res.status) {
					this.$common.successToShow(res.msg)
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		},
	}
}	
</script>

<style>
.coupon {
	padding: 0 26upx;
	background-color: #f8f8f8;
}

.coupon-item {
	padding: 20upx;
	margin-bottom: 20upx;
	background-color: #fff;
}

.coupon-i-l {
	width: 400upx;
	display: inline-block;
}

.coupon-i-l-t {
	font-size: 32upx;
	position: relative;
	margin-bottom: 10upx;
}

.coupon-i-l-t .icon {
	position: absolute;
	/* top: 50%; */
	/* transform: translateY(-50%); */
}

.coupon-i-l-t text {
	margin-left: 60upx;
}

.coupon-i-l-b {
	font-size: 24upx;
	color: #999;
}

.coupon-i-r {
	width: 258upx;
	display: inline-block;
	text-align: center;
}

.coupon-logo {
	width: 130upx;
	height: 100upx;
}

</style>
