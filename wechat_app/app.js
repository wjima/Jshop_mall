App({
  //全局配置信息
  config: require('utils/config.js'),
  //小程序启动之后触发
  onLaunch: function (e) {
    var ext_config_data = wx.getExtConfigSync();
    if (JSON.stringify(ext_config_data) != "{}") {
      this.config.api_url = ext_config_data.attr.api_url;
      this.config.cdn_url = ext_config_data.attr.cdn_url;
      this.config.app_title = ext_config_data.attr.app_title;
      this.config.app_description = ext_config_data.attr.app_description;
      this.config.app_logo = ext_config_data.attr.app_logo;
      this.config.site_token = ext_config_data.attr.site_token;
      this.config.store_type = 2;
    } else {
      if (e.query.scene) {
        wx.setStorageSync('site_token', e.query.scene);
        this.config.site_token = e.query.scene;
      } else if (wx.getStorageSync('site_token')) {
        this.config.site_token = wx.getStorageSync('site_token');
      }
      this.config.store_type = 1;
    }

    //取用户信息
    // this.db.userAuth(function(res){});
    //登陆
    // this.db.userLogin(function (res) {
    //   console.log('登陆成功');
    //   console.log(res);
    // });
  },
  api: require('utils/api.js'),       //接口文档
  db: require('utils/db.js'),         //本地存储
  common: require('utils/common.js'),
  //全局数据
  globalData: {
    userInfo: [], //用户数据
    sdata:[{
      'd':'1'
    }]
    //sellerInfo: [] //商家信息
  }
});