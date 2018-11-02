<template>
    <div class="ordercoupon">
        <yd-cell-item @click.native="showHandler">
            <span slot="left">优惠券</span>
            <span slot="right" class="demo-list-price">{{ couponName }}</span>
        </yd-cell-item>
        <yd-popup v-model="showList" position="bottom" height="40%" class="ordercoupon-content">
            <yd-cell-group>
                <div v-if="coupon.length">
                    <yd-cell-item @click.native="selectHandler(index)" v-for="(item, index) in coupon" :key="index">
                        <span slot="left">{{ item.name }}</span>
                        <span slot="right" class="demo-list-price"><img :src="radio[0]" v-show="!item.checked"/><img :src="radio[1]" v-show="item.checked"/></span>
                    </yd-cell-item>
                </div>
                <div v-else>
                    没有优惠券 快去领取一个吧
                </div>
            </yd-cell-group>
            <yd-button type="warning" style="margin: 30px;" @click.native="showList=false">关闭</yd-button>
        </yd-popup>
    </div>
</template>

<script>
export default {
    data () {
        return {
            couponName: '未使用',
            radio: [
                'static/image/kong.png', // 未选中
                'static/image/xuanzhong.png' // 选中
            ],
            showList: false
        }
    },
    props: {
        coupon: [Array, Object],
        default () {
            return []
        }
    },
    methods: {
        showHandler () {
            this.showList = true
        },
        selectHandler (index) {
            // 点击优惠券后关闭弹窗
            this.showList = false
            this.couponName = this.coupon[index].checked ? '未使用' : this.coupon[index].name
            // 向父组件传递选中的优惠券 选中状态
            this.$emit('couponChecked', [index, this.coupon[index].checked])
        }
    }
}
</script>

<style>
    .ordercoupon{
        background-color: #fff;
    }
    .ordercoupon-content .yd-btn{
        width: 100%;
        background-color: #FF3B44;
        position: fixed;
        bottom: 0;
        left: 0;
        margin: 0 !important;
        color: #fff;
        border-radius: 0;
        height: .7rem;
    }
    .ordercoupon-content .yd-cell .demo-list-price img{
        width: .35rem;
        height: .35rem;
    }
</style>
