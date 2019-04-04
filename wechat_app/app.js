App({
    config: require('utils/config.js'), //全局配置信息
    api: require('utils/api.js'), //接口文档
    db: require('utils/db.js'), //本地存储
    common: require('utils/common.js'),
    jshopConf:{},
    onLaunch: function (e) {
        //获取配置
        this.common.getJshopConf();
    }, //小程序启动之后触发
});