const app = getApp(); //获取全局app.js
var countdown = 60;
var settime = function (that) {
  if (countdown == 0) {
    that.setData({
      is_show: true
    })
    countdown = 60;
    return;
  } else {
    that.setData({
      is_show: false,
      last_time: countdown
    })

    countdown--;
  }
  setTimeout(function () {
    settime(that)
  }
    , 1000)
}
Page({

  /**
   * 页面的初始数据
   */
  data: {
    mobile:'',
    code:'',
    last_time: '',
    is_show: true
  },
  clickVerify: function () {
    //console.log(this.data.mobile);
    var that = this;
    if (this.data.mobile == '') {
      wx.showToast({
        title: '请输入手机号码',
        icon: 'success',
        duration: 2000
      })
      return false;
    }
    app.api.sms(this.data.mobile,'login', function (res) {
      //console.log(res);
      if (res.status) {
        // 将获取验证码按钮隐藏60s，60s后再次显示
        that.setData({
          is_show: (!that.data.is_show)  //false
        })
        settime(that);

        wx.showToast({
          title: '发送成功',
          icon: 'success',
          duration: 2000
        })
      } else {
        //报错了
        wx.showToast({
          title: res.msg,
          icon: 'success',
          duration: 2000
        })
      }
    });
    
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
  
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },
  //跳转到微信登陆页面
  showWxLogin: function(e) {
    wx.redirectTo({
      url: '../level1/level1'
    })
  },


  mobileChange: function(e) {
    this.setData({
      mobile: e.detail.value
    });
  },
  codeChange: function (e) {
    this.setData({
      code: e.detail.value
    });
  },
  //提交按钮
  showTopTips: function () {
    if (this.data.mobile == '') {
      wx.showToast({
        title: '请输入手机号码',
        icon: 'success',
        duration: 2000
      })
      return false;
    }
    if (this.data.code == '') {
      wx.showToast({
        title: '请输入验证码',
        icon: 'success',
        duration: 2000
      })
      return false;
    }
    var data = {
      mobile: this.data.mobile,
      code: this.data.code
      };
    app.api.smsLogin(data, function (res) {
      if (res.status) {
        app.db.set('userToken', res.data);
        wx.showToast({
          title: '登陆成功',
          icon: 'success',
          duration: 2000,
          success: function(res) {
            wx.navigateBack({
              delta: 1
            })
          }
        });
        
      } else {
        //报错了
        wx.showToast({
          title: res.msg,
          icon: 'success',
          duration: 2000
        })
      }
    });

  },
})