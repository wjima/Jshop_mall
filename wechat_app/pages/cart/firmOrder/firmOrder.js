//订单确认页面
var app = getApp();

Page({
  //当前页面数据
  data: {
    cartIds: '', //购物车ID
    productData: [], //货品信息
    isAddress: false, //是否选择收货信息
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
    pointSwitch: 2,
    pointStatus: false,
    point: 0,
    pointRmb: 0,
    available_point: 0,
    orderPromotionList: [],
    totalAmount: 0.00,
    costFreight: 0.00,
    showcoupon: false,
    couponitems: [], //用户优惠券列表
    usedCoupon: '', //即将使用的优惠券号
    usedCouponOk: '', //已经使用的优惠券号
    usedCouponName: '未使用', //使用的优惠券名
    couponPmt: 0.00, //优惠券优惠金额
    express: true,
    lifting: false,
    choose: true,
    entry: false,
    selected: true,
    selected1: false,
    storeSwitch: 2, //没有开启门店自提2
    receipt_type: 1, //收货方式1快递配送 2上门自提
    store_id: 0, //门店ID
    lading_name: '', //自提姓名
    lading_mobile: '', //自提电话
    store_name: '暂无门店', //门店名称
    store_mobile: '', //门店电话
    store_address: '暂无自提门店', //门店地址
    couponKey: '', //输入的优惠券号
    couponKey2: '', //存储的
    noCoupon: true, //没有使用优惠券
  },

  //页面加载
  onLoad: function (options) {
    var cart_id = JSON.parse(options.data);
    if(cart_id.length < 0){
      //todo:数据异常处理
      return false;
    }
    this.getStoreSwitch();
    this.getDefaultShip();
    this.getDefaultStore();
    this.getUserCoupon();
    this.setData({
      cartIds: cart_id
    });
  },

    //是否开启门店自提
    getStoreSwitch: function () {
        let page = this;
        app.api.getStoreSwitch(function(res){
            page.setData({
                storeSwitch: res.data
            });
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

    //获取默认门店
    getDefaultStore: function () {
        var page = this;
        app.db.userToken(function (token) {
            app.api.getDefaultStore(function (e) {
                if(e.status){
                    page.setData({
                        store_id: e.data.id,
                        store_name: e.data.store_name,
                        store_mobile: e.data.mobile,
                        store_address: e.data.all_address
                    });
                }
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
        coupon_code: page.data.usedCoupon,
        receipt_type: page.data.receipt_type
      };
      if(page.data.pointStatus){
          data['point'] = page.data.available_point;
      }
      app.api.cartList(data, function (res) {
          if(res.status){
              let couponName = '未使用';
              let noCoupon = true;
              if (res.data.coupon && res.data.coupon.length !== 0) {
                  couponName = '';
                  for (var key in res.data.coupon) {
                      couponName += res.data.coupon[key]+',';
                  }
                  couponName = couponName.substr(0, couponName.length - 1);
                  noCoupon = false;
              }
              page.setData({
                  cartIds: cart_id,
                  productData: res.data.list,
                  goodsAmount: app.common.formatMoney(res.data.goods_amount, 2, ''),
                  goodsPmt: app.common.formatMoney(res.data.goods_pmt, 2, ''),
                  orderPmt: app.common.formatMoney(res.data.order_pmt, 2, ''),
                  couponPmt: app.common.formatMoney(res.data.coupon_pmt, 2, ''),
                  orderPromotionList: res.data.promotion_list,
                  totalAmount: app.common.formatMoney(res.data.amount, 2, ''),
                  costFreight: app.common.formatMoney(res.data.cost_freight * 1, 2, ''),
                  usedCouponOk: res.data.coupon,
                  usedCouponName: couponName,
                  noCoupon: noCoupon
              });
              page.setSpes();
              if (!page.data.pointStatus) {
                  page.getUserPoint();
              }
          }else{
            wx.showToast({
                title: res.msg,
                icon: 'none',
                duration: 2000
            });

              let k2 = page.data.couponKey2;
              if (page.data.couponKey != ''){
                  k2 = page.data.couponKey2.substr(0, page.data.couponKey2.length*1-page.data.couponKey.length*1-1);
              }

                if (res.data == 15009 || res.data == 15013 || res.data == 15015){
                    //优惠券号码不存在
                    //优惠券已经使用过了
                    //同一类优惠券，只能使用一张
                    page.setData({
                        couponKey: '',
                        couponKey2: k2
                    });
                }
          }
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
    let page = this;
    wx.navigateTo({
      url: '../../member/addressList/addressList?select=true',
    });
  },

  //选择门店
  gostore: function () {
    wx.navigateTo({
      url: '../../other/store/store'
    });
  },

  //留言信息
  buyerMessage: function (e) {
    this.setData({
      buyerMessage: e.detail.value
    });
  },

  //立即支付生成订单
  payNow: function (e) {
    let page = this;
    let formId = e.detail.formId;
    app.db.userToken(function (token) {
        //验证是否填写完成
        let flag = true;
        if(page.data.receipt_type == 1){
            if (!page.data.isAddress) {
                wx.showToast({
                    icon: 'none',
                    title: '请选择收货地址信息'
                });
                flag = false;
                return false;
            }
        }else{
            if (!page.data.store_id){
                wx.showToast({
                    icon: 'none',
                    title: '请选择自提门店'
                });
                flag = false;
                return false;
            }
            if (!page.data.lading_name){
                wx.showToast({
                    icon: 'none',
                    title: '请输入提货人姓名'
                });
                flag = false;
                return false;
            }
            if (!page.data.lading_mobile) {
                wx.showToast({
                    icon: 'none',
                    title: '请输入提货人电话'
                });
                flag = false;
                return false;
            }
        }

        //组装数据
        if (flag) {
            let coupon = '';
            for (var key in page.data.usedCouponOk){
                coupon += key+',';
            }
            coupon = coupon.substr(0, coupon.length - 1);
            var order_data = {
                uship_id: page.data.userShipId,
                cart_ids: page.data.cartIds,
                memo: page.data.buyerMessage,
                area_id: page.data.areaId,
                coupon_code: coupon,
                formId: formId,
                receipt_type: page.data.receipt_type,
                store_id: page.data.store_id,
                lading_name: page.data.lading_name,
                lading_mobile: page.data.lading_mobile
            }
            if (page.data.pointStatus) {
                order_data['point'] = page.data.available_point
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

  //优惠券选择
  couponbtn: function (e) {
    var currentStatu = e.currentTarget.dataset.statu;
    this.anim(currentStatu);
  },

  //优惠券点击
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
      });
      if (currentStatu == "close") {
        this.setData({
            showcoupon: false
          });
      }
    }.bind(this), 100)
    if (currentStatu == "open") {
      this.setData({
          showcoupon: true
        });
    }  
  },

  move: function () {},

  //优惠券被选中
  clickCoupon: function (e) {
    let page = this;
    //只能选择一个优惠券
    for (let k in page.data.couponitems) {
      if (page.data.couponitems[k].name == e.target.dataset.id) {
        page.data.couponitems[k].checked = 'true';
      }else{
          delete page.data.couponitems[k].checked;
      }
    }
    //获取现在的优惠券ID
      let c1 = '';
      for (let key in page.data.couponitems) {
          if (page.data.couponitems[key].checked == 'true') {
              c1 += ',' + page.data.couponitems[key].name
          }
      }
      //获取输入的优惠券
      let c2 = page.data.couponKey;
      let s = c1 + "," + c2;
      s = s.replace(/^,+/, "").trim();
      s = s.replace(/,+$/, "").trim();

          page.setData({
              usedCoupon: s,
              showcoupon: false,
              pointStatus: false,
              couponitems: page.data.couponitems,
              //noCoupon: false,
          });
          page.getProductData();
  },

    //选择不使用优惠券
    clickNoCoupon: function(e){
        let page = this;
        for (let k in page.data.couponitems) {
            delete page.data.couponitems[k].checked;
        }
        this.setData({
            usedCoupon: '',
            usedCouponName: '未使用',
            couponitems: page.data.couponitems,
            showcoupon: false,
            pointStatus: false,
            noCoupon: true,
            couponKey: ''
        });
        page.getProductData();
    },

  //获取用户的可用积分
  getUserPoint: function (e) {
      let page = this;
      let data = {
          'order_money': page.data.totalAmount
      }
      app.api.getUserPoint(data, function(res){
        if(res.status){
            page.setData({
                pointSwitch: res.switch,
                point: res.data,
                available_point: res.available_point,
                pointRmb: res.point_rmb
            });
        }
      });
  },

  //开启积分
  checkboxChange: function (e) {
      let page = this;
      var status = false;
      if (e.detail.value.length > 0) {
          status = true;
      }
      page.setData({
          pointStatus: status
      });
      page.getProductData();
  },

  //优惠券使用方式
  choose: function (e) {
    this.setData({
      entry: false,
      choose: true
    });
  },

  //优惠券选择
  entry: function (e) {
    this.setData({
      choose: false,
      entry: true
    });
  },

    //配送方式切换
    express: function (e) {
        let outType = this.data.receipt_type;
        this.setData({
            lifting: false,
            express: true,
            receipt_type: 1,
            pointStatus: false
        });
        if (outType != 1) {
            this.getProductData();
        }
    },

    //上门自提
    lifting: function (e) {
        let outType = this.data.receipt_type;
        this.setData({
            express: false,
            lifting: true,
            receipt_type: 2,
            pointStatus: false
        });
        if (outType != 2) {
            this.getProductData();
        }
    },

    //提货人姓名
    ladingName: function (e) {
        let name = e.detail.value;
        this.setData({
            lading_name: name
        });
    },

    //提货人电话
    ladingMobile: function (e) {
        let mobile = e.detail.value;
        this.setData({
            lading_mobile: mobile
        });
    },

    //输入优惠券码
    couponKey: function (e) {
        let key = e.detail.value;
        this.setData({
            couponKey: key
        });
    },
    
    //输入优惠券确认
    couponKeyBtn: function(res){
        let page = this;
        let c1 = '';
        for (let key in page.data.couponitems) {
            if (page.data.couponitems[key].checked == 'true'){
                c1 += ',' + page.data.couponitems[key].name
            }
        }
        let c2 = page.data.couponKey;
        if (c2 == ''){
            //不使用优惠券
            page.setData({
                pointStatus: false,
                showcoupon: false,
            });
        }else{
            let d = page.data.couponKey2 + ',' + c2
            d = d.replace(/^,+/, "").trim();
            let s = c1 + "," + d;
            s = s.replace(/^,+/, "").trim();
            page.setData({
                usedCoupon: s,
                couponKey2: d,
                showcoupon: false,
                pointStatus: false
            });
            page.getProductData();
        }
    }
});