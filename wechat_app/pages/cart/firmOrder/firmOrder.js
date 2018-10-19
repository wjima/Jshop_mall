//订单确认页面
var app = getApp();

Page({
  //当前页面数据
  data: {
    cartIds: '', //购物车ID
    productData: [], //货品信息
    isAddress: false, //是否选择售后信息
    areaId: 0, //地区ID
    area: '', //地区信息
    address: '', //地址信息
    name: '', //收货人姓名
    mobile: '', //收货人电话
    totalPrice: 0, //总价
    userShipId: 0, //用户收货信息ID
    buyerMessage: '', //买家留言
    goodsAmount: 0.00,
    goodsPmt: 0.00,
    orderPmt: 0.00,
    orderPromotionList: [],
    totalAmount: 0.00,
    costFreight: 0.00,
    showcoupon: false,
    couponitems: [], //用户优惠券列表
    usedCoupon: '', //使用的优惠券号
    usedCouponName: '未使用', //使用的优惠券名
    couponPmt: 0.00, //优惠券优惠金额
  },

  //页面加载
  onLoad: function (options) {
    var cart_id = JSON.parse(options.data);
    if(cart_id.length < 0){
      //todo:数据异常处理
      return false;
    }
    this.getDefaultShip();
    this.getUserCoupon();
    this.setData({
      cartIds: cart_id
    });
  },

  //获取默认收货地址
  getDefaultShip: function () {
    var page = this;
    app.db.userToken(function (token) {
      app.api.getDefaultShip(function (res) {
        if(res.status){
          page.setData({
            isAddress: true,
            areaId: res.data.area_id,
            area: res.data.area_name,
            address: res.data.address,
            name: res.data.name,
            mobile: res.data.mobile,
            userShipId: res.data.id
          });
        }
        page.getProductData();
      });
    });
  },

  //获取货品信息
  getProductData: function () {
    var page = this;
    var cart_id = page.data.cartIds;
    app.db.userToken(function (token) {
      var data = {
        ids: cart_id,
        area_id: page.data.areaId,
        coupon_code: page.data.usedCoupon
      };
      app.api.cartList(data, function (res) {
        page.setData({
          cartIds: cart_id,
          productData: res.data.list,
          goodsAmount: app.common.formatMoney(res.data.goods_amount, 2, ''),
          goodsPmt: app.common.formatMoney(res.data.goods_pmt, 2, ''),
          orderPmt: app.common.formatMoney(res.data.order_pmt, 2, ''),
          couponPmt: app.common.formatMoney(res.data.coupon_pmt, 2, ''),
          orderPromotionList: res.data.promotion_list,
          totalAmount: app.common.formatMoney(res.data.amount, 2, ''),
          costFreight: app.common.formatMoney(res.data.cost_freight*1, 2, '')
        });
        page.setSpes();
      });
    });
  },

  //货品参数设置
  setSpes: function () {
    var productData = this.data.productData;
    for (var key in productData) {
      var spes_desc = productData[key].products.spes_desc;
      if (spes_desc) {
        if (spes_desc.length > 0) {
          var spesArray = spes_desc.split(",");
          var spes = [];
          for (var key1 in spesArray) {
            var spesOne = spesArray[key1].split(":");
            spes.push(spesOne[1]);
            productData[key].products.spes = spes;
          }
        }
      }
    }
    this.setData({
      productData: productData
    });
  },

  //选择地址
  selectAddress: function () {
    var page = this;
    app.db.userToken(function (token) {
      wx.chooseAddress({
        success: function (res) {
          if (res.errMsg == "chooseAddress:ok") {
            //获取成功
            //存储这个收获地区信息到数据库
            var data = {
              province_name: res.provinceName,
              city_name: res.cityName,
              county_name: res.countyName,
              postal_code: res.postalCode
            };
            var areaId = 0;
            app.api.getAreaId(data, function (res1) {
              if(res1.status) {
                areaId = res1.data;
                page.setData({
                  areaId: areaId
                });
                //存储用户收货信息
                var userShipId = 0;
                var userShipData = {
                  area_id: areaId,
                  user_name: res.userName,
                  detail_info: res.detailInfo,
                  tel_number: res.telNumber,
                  is_def: 1
                }
                app.api.saveUserShip(userShipData, function (res2) {
                  if (res2.status) {
                    userShipId = res2.data
                    page.setData({
                      isAddress: true,
                      area: res.provinceName + res.cityName + res.countyName,
                      address: res.detailInfo,
                      name: res.userName,
                      mobile: res.telNumber,
                      userShipId: userShipId
                    });
                  }
                });
              }
              page.getProductData();
            });
          } else {
            //todo:获取失败处理
            page.setData({
              isAddress: false
            });
          }
        }
      });
    });
  },

  //留言信息
  buyerMessage: function (e) {
    this.setData({
      buyerMessage: e.detail.value
    });
  },

  //立即支付生成订单
  payNow: function () {
    var page = this;
    app.db.userToken(function (token) {
      if (!page.data.isAddress) {
        wx.showToast({
          icon: 'none',
          title: '请选择收货地址信息'
        });
        return false;
      } else {
        var order_data = {
          uship_id: page.data.userShipId,
          cart_ids: page.data.cartIds,
          memo: page.data.buyerMessage,
          area_id: page.data.areaId,
          coupon_code: page.data.usedCoupon
        }
        page.goPay(order_data);
      }
    });
  },

  //去支付接口
  goPay: function (order_data) {
    var page = this;
    app.api.createOrder(order_data, function (res) {
      if (res.status) {
        if (res.data.order_amount <= 0) {
          wx.redirectTo({
            url: '../paySuccess/paySuccess?payment_id=null&order_id=' + res.data.order_id
          });
        } else {
          wx.redirectTo({
            url: '../cashierDesk/cashierDesk?data=' + JSON.stringify(res.data),
          });
        }
      } else {
        wx.showModal({
          title: '错误提示',
          content: res.msg,
          showCancel: false,
          success: function (e) {
            if (e.confirm) {
              page.navigateBack();
            }
          }
        });
      }
    });
  },

  //返回上一页
  navigateBack: function () {
    var self = this;
    var pages = getCurrentPages();
    if (pages.length == 1) {
      if (self.data.circleId && self.data.circleId > 0) {
        wx.redirectTo({
          url: '../../circle/index/index?circleId=' + self.data.circleId
          + '&circleName=' + (self.data.circleName || '')
        });
      } else {
        wx.switchTab({
          url: "../../home/grouplist/grouplist"
        });
      }
    } else {
      wx.navigateBack({ changed: true });
    }
  },

  //获取用户优惠券
  getUserCoupon: function () {
    let page = this;
    app.db.userToken(function (token) {
      var data = {
        display: 'no_used'
      }
      app.api.myCouponList(data, function (res) {
        if(res.status){
          let coupon = [];
          for (let k in res.data){
            coupon.push({ name: res.data[k].coupon_code, value: res.data[k].name});
          }
          page.setData({
            couponitems: coupon
          });
        }
      });
    });
  },

  couponbtn: function (e) {
    var currentStatu = e.currentTarget.dataset.statu;
    this.anim(currentStatu);
  },

  anim: function (currentStatu) {    
    var animation = wx.createAnimation({
      duration: 100,
      timingFunction: "linear",
      delay: 0
    });
    this.animation = animation;
    animation.opacity(0.5).translateY(375).step();
    this.setData({
      animationData:animation.export(),
    });
    setTimeout(function () {
      animation.opacity(1).translateY(0).step();
      this.setData({
        animationData: animation
      }) 

      if (currentStatu == "close") {
        this.setData(
          {
            showcoupon: false
          }
        );
      }  
    }.bind(this), 100)
    if (currentStatu == "open") {
      this.setData(
        {
          showcoupon: true
        }
      );
    }  
  },

  move: function () {},

  //优惠券被选中
  clickCoupon: function (e) {
    let page = this;
    for (let k in page.data.couponitems) {
      if (page.data.couponitems[k].name == e.target.dataset.id) {
        page.data.couponitems[k].checked = 'true';
      }
    }
    this.setData({
      usedCoupon: e.target.dataset.id,
      usedCouponName: e.target.dataset.name,
      couponitems: page.data.couponitems,
      showcoupon: false
    });
    page.getProductData();
  }
});