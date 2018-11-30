var app = getApp(); //全局APP
Page({
  data: {
    logo:'../../../image/default_avatar.png',
    open_id:""
  },
  //页面加载处理
  onLoad: function () {
    var page = this;
    this.getWxCode(function(code){
      var data = {
        code: code
      };
      app.api.login1(data, function (res) {
        if(!res.status){
          wx.showToast({
            title: res.msg,
            icon: 'success',
            duration: 2000,
            success: function (res) {
              wx.navigateBack({
                delta: 1
              })
            }
          })
        }else{
          page.setData({
            open_id: res.data
          });
        }
      });
    });
    //取店铺的配置信息，拿到店铺logo
    app.api.getSellerSetting('shop_logo',function(res){
      if(res.status){
        page.setData({
          logo:res.data
        });
      }
    });
  },
  //微信授权用户取得信息
  // getWxInfo: function (callback) {
  //   this.getWxCode(function(code){
  //     wx.getUserInfo({
  //       withCredentials: true,
  //       success: function (res1) {
  //         callback({
  //           code: code,
  //           iv: res1.iv,
  //           edata: res1.encryptedData
  //         });
  //       },
  //       fail: function (res) {
  //         //如果没有获取到用户信息，打开设置页面
  //         wx.openSetting({
  //           success: function (res) {

  //           }
  //         })
  //       }
  //     })
  //   });
  // },

  //单纯的取得微信的code
  getWxCode: function(callback){
    wx.login({
      success: function (res) {
        if (res.code) {
          callback(res.code);
          return res.code;
        } else {
          //wx.login成功，但是没有取到code
          wx.showToast({
            title: '未取得code',
            icon: 'warn',
            duration: 2000
          })
        }
      },
      fail: function (res) {
        //wx.login的fail
        wx.showToast({
          title: '用户授权失败wx.login',
          icon: 'warn',
          duration: 2000
        })
      }
    });
  },
  //提交按钮
  // mobileLogin: function () {
  // },
  getUserInfo: function (e) {
    var page = this;
    if (e.detail.errMsg == 'getUserInfo:fail auth deny') {
      wx.showModal({
        title: '提示',
        showCancel: false,
        content: '未授权',
        success: function (res) { }
      })
    } else {
      var data = {
        open_id: page.data.open_id,
        iv: e.detail.iv,
        edata: e.detail.encryptedData,
        signature: e.detail.signature
      };
      page.toLogin(data);
      //注意，这里不检查登陆态了，默认一直有效，这是个隐含的问题,因为wx.checkSession永远都是fail，不知道为啥，以后再来处理吧。
      // wx.checkSession({
      //   success: function () {
      //     var data = {
      //       open_id: this.open_id,
      //       iv: e.detail.iv,
      //       edata: e.detail.encryptedData
      //     };
      //     page.toLogin(data);
      //   },
      //   fail: function () {
      //     // session_key 已经失效，需要重新执行登录流程
      //     //wx.login() //重新登录
      //   }
      // })
    }
  },
  //实际的去登陆
  toLogin: function (data) {
    app.api.login2(data, function (res) {
      if(res.status){
        //判断是否返回了token，如果没有，就说明没有绑定账号，跳转到绑定页面
        if (typeof res.data.token == 'undefined'){
          wx.redirectTo({
            url: '../level2/level2?user_wx_id=' + res.data.user_wx_id
          })
        }else{
          //登陆成功，设置token，并返回上一页
          app.db.set('userToken', res.data.token);
          wx.navigateBack({
            delta: 1
          })
        }
      }else{
        wx.showModal({
          title: '提示',
          showCancel: false,
          content: '登陆失败，请重试',
          success: function (res) { }
        })
      }
    });
  },


});