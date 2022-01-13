<template>
    <view class="set-theme_vue">
        <button class="item-button" type="primary" @tap="showPickerColorPop">修改主题色</button>
        <button class="item-button" type="primary" @tap="onClose">关闭页面</button>
        <picker-color :isShow="showPickerColor" :bottom="bottomPickerColor"  @callback='getPickerColor' />
    </view>
</template>

<script>
    import pickerColor from "@/components/helang-pickerColor/helang-pickerColor.vue"
    import base from '@/common/base.js';
    export default {
        extends: base,
        components: {
            "picker-color":pickerColor,
        },
        data() {
            return {
                showPickerColor:true,
                bottomPickerColor:0,

                colorStart:uni.getStorageSync('navBgColor') + '',
                colorEnd:uni.getStorageSync('navBgColor') + '',
            }
        },
        onLoad(e) { //获取上个页面传过来的参数
            const self = this;
            const eventChannel = this.getOpenerEventChannel();
            eventChannel.on('acceptDataFromOpenerPage', function(data) {
                console.log("接收到数据",data);
            })
        },
        created() {
            const self = this;

        },
        mounted() {
            const self = this;

        },
        methods: {
            //显示获取颜色选择弹窗
            showPickerColorPop(){
                const self = this;
                self.showPickerColor=true;
            },
            //获取颜色选择回调
            getPickerColor(color){
                const self = this;
                self.showPickerColor=false;
                if(color){ //判断颜色值是否有效
                    self.colorEnd = color;
                    uni.setStorageSync('navBgColor',color);
                    getApp().globalData.navBgColor=color;
                    let fontColor = '#ffffff';
                    self.filtrateColor(fontColor,color)
                }
            },
            onClose(){
                const self = this;
                let params ={
                    xuan:'wangyuxuan',
                    feng:'daxiongmiao ',
                    yu:[],
                    hj:{}
                };
                self.callbackPage(params);
                self.closePage(); //关闭当前页面
            },
        },
        onUnload: function () {
            const self = this;
            if(self.colorStart !== self.colorEnd){
                uni.reLaunch({
                    url: '/pages/example/components'
                });
            }
            let params ={
                xuan:'wangyuxuan',
                feng:'daxiongmiao ',
                yu:[],
                hj:{}
            };
            self.callbackPage(params);
        },
    }
</script>

<style lang="scss">
    .set-theme_vue{
        padding: 0 20rpx;
        .item-button{margin-top: 50rpx;}
    }
</style>
