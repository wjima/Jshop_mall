App({
  config: require('utils/config.js'), //全局配置信息
  api: require('utils/api.js'), //接口文档
  db: require('utils/db.js'), //本地存储
  common: require('utils/common.js'),
  jshopConf:{},
  onLaunch: function (e) {
    let line_conf = this.db.get('config');
    if(line_conf){
      this.config = this.common.deepCopy(this.config,line_conf);
    }
    //取最新的店铺配置信息
    var jshop = this
    wx.request({
      url: jshop.config.api_url + 'api/common/jshopconf',//未开启伪静态时，请把api改成index.php/api
      data: {},
      method: 'POST',
      success: function (res) {
        if(res.statusCode == 200){
          jshop.db.set('config', res.data)
          jshop.config = jshop.common.deepCopy(jshop.config, res.data);
        }
      },
      fail: function (res) {
      },
      complete: function (res) {
      }
    });

  }, //小程序启动之后触发
});