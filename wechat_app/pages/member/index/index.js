//个人中心首页
var app = getApp() //全局APP

Page({
  //页面初始数据
  data: {
    nickname: '',
    point: 0, //用户积分
    balance: '0.00', //用户余额
    isPoint: false, //开启积分
    avatar: '../../image/default_avatar.png',
    bindMobile: false,
    statusData: [0, 0, 0, 0], //状态数据
    isClerk: false, //是不是店员
    hasLogin: false
  },
  init() {
    var page = this
    console.log(this.data.hasLogin)
    if (app.db.get('userToken')) {
      this.setData({
        hasLogin: true
      })
      app.api.userInfo(function(res) {
        if (res.status) {
          //如果没有头像，设置本地默认头像
          var avatar = '../../image/default_avatar.png'
          if (res.data.avatar) {
            avatar = res.data.avatar
          }
          page.setData({
            nickname: res.data.nickname,
            avatar: avatar,
            point: res.data.point,
            balance: res.data.balance
          })

          app.api.getOrderStatusNum('1,2,3,4', function(res) {
            page.setData({
              statusData: res.data
            })
          })

          //是否店员
          app.api.isClerk(function(res) {
            page.setData({
              isClerk: res.flag
            })
          })

          //是否开启积分
          page.setData({
            isPoint: app.config.point_switch
          })
        }
      })
    } else {
      this.setData({
        hasLogin: false
      })
      this.resetData()
    }
  },
  //加载执行
  onShow: function() {
    this.init()
  },
  resetData() {
    this.setData({
      nickname: '',
      point: 0, 
      balance: '0.00', 
      isPoint: false, 
      avatar: '../../image/default_avatar.png',
      bindMobile: false,
      statusData: [0, 0, 0, 0],
      isClerk: false, 
    })
  },
  checkLogin() {
    wx.showToast({
      title: '请先登录!',
      icon: 'none',
      duration: 800,
      success: function(res) {
        setTimeout(() => {
          wx.navigateTo({
            url: '../login/level1/level1'
          })
        }, 800)
      }
    })
  },

  //查看全部订单
  orderAll: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../order/orderList/orderList?type=all'
    })
  },

  //待支付订单
  orderNoPay: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../order/orderList/orderList?type=pendingpayment'
    })
  },

  //待发货订单
  orderNoShip: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../order/orderList/orderList?type=pendingdelivery'
    })
  },

  //待收货订单
  orderNoReceiving: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../order/orderList/orderList?type=goodstobereceived'
    })
  },

  //退换货
  orderAftermarket: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../order/aftersalesList/aftersalesList'
    })
  },

  //我的积分
  myPoint: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../point/point'
    })
  },

  //我的优惠券
  coupon: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../coupon/coupon'
    })
  },

  //余额提现
  withdrawCash: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../remainingSum/withdrawCash/withdrawCash'
    })
  },

  //我的关注
  attention: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../collect/collect'
    })
  },

  //前往我的足迹
  browsingHistory: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../browsingHistory/browsingHistory'
    })
  },

  // 邀请好友
  invite: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../invite/invite'
    })
  },

  //更换头像
  chooseAvatar: function() {
    var page = this
    app.api.uploadImage(1, function(res) {
      if (res.status) {
        app.api.changeAvatar(res.data.url, function(res1) {
          if (res1.status) {
            app.common.successToShow('更换头像成功', function() {
              page.setData({
                avatar: res.data.url
              })
            })
          } else {
            app.common.errorToBack(res1.msg, 0)
          }
        })
      } else {
        app.common.errorToBack(res.msg, 0)
      }
    })
  },

  //收货地址管理
  addressList: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../addressList/addressList'
    })
  },

  //设置
  setting: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../userSetting/userSetting'
    })
  },

  //提货单列表
  ladingList: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../../other/lading/lading'
    })
  },

  //提货单核销
  ladingWrite: function() {
    if (!this.data.hasLogin) {
      return this.checkLogin()
    }
    wx.navigateTo({
      url: '../../other/lading/write'
    })
  }
})
