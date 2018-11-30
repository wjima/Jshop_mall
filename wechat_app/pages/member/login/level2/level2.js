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
    is_show: true,
    wx_user_id:0
  },
  clickVerify: function () {
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
    this.setData({
      user_wx_id: options.user_wx_id
    });
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
      code: this.data.code,
      platform:2,     //平台id，标识是小程序登陆的
      user_wx_id: this.data.user_wx_id         //微信小程序接口存不了session，所以要绑定的id只能传到前台
    };
    //有推荐码的话，带上
    var invitecode = app.db.get('invitecode');
    if (invitecode){
      data.invitecode = invitecode;
    }
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