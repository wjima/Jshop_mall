// import util from '../utils/utils';
export default {
    created() {
        const self = this;

    },
    mounted() {
        const self = this;
        //self.changeNavigationBarColor()
    },
    methods: {
        //打开页面，传递参数
        // openPage(options, callback) {
        //     //options.CustomWindow参数控制是否重置自定义组件
        //     if(!options.CustomWindow){
        //         getApp().globalData.CustomWindow=[];
        //     }
        //     return util.openPage(options, callback);
        // },
        //关闭页面
        closePage: function(options) {
            const self = this;
            uni.navigateBack(options);
        },
        //回调方法
        callbackPage(data) {
            const eventChannel = this.getOpenerEventChannel()
            eventChannel.emit('callbackFromOpenedPage', data);
        },
        //设置导航栏背景色
        /* changeNavigationBarColor() {
            const self = this;
            let navBgColor = '';
            let fontColor = '#ffffff';
            navBgColor = getApp().globalData.navBgColor;
            if(!navBgColor){
                navBgColor = uni.getStorageSync('navBgColor');
                if(navBgColor){
                    getApp().globalData.navBgColor=navBgColor;
                }else{
                    navBgColor='#007AFF';
                    uni.setStorageSync('navBgColor',navBgColor);
                    getApp().globalData.navBgColor=navBgColor;
                }
            }
            console.log("navBgColor",navBgColor);
            self.filtrateColor(fontColor,navBgColor)
        }, */
        //处理主题的颜色
        /* filtrateColor(fontColor,navBgColor){
            let white = ['#FFFFFF','#EEEEEE','#CCCCCC','#999999','#ffffcc','#ffff99','#ffff66','#ffcccc'];
            white.find(function(value) {  //淡色背景，需要把字体改为黑色
                if(value === navBgColor) {
                    fontColor = '#000000';
                }});
            uni.setNavigationBarColor({
                frontColor: fontColor,
                backgroundColor:navBgColor
            });
        }, */
        //设置字体
        getRootFontSize(){
            const self = this;
            var fontSize = getApp().globalData.rootFontSize;
            if(fontSize){
                return fontSize;
            }else{
                fontSize = uni.getStorageSync('root_font_size');
                if(fontSize){
                    getApp().globalData.rootFontSize=fontSize;
                }else{
                    fontSize='12px';
                    self.setRootFontSize(fontSize);
                }
                return fontSize;
            }
        },
        setRootFontSize(fontSize){
            uni.setStorageSync('root_font_size',fontSize);
            getApp().globalData.rootFontSize=fontSize;
        },

    }
}
