// thats/form/form.js
const app = getApp(); //全局app
Page({

  /**
   * 页面的初始数据
   */
  data: {
    hiddenForm:true,
    indicatorDots: true, //商品轮播图底部圆点
    autoplay: true, //商品轮播图自动播放
    interval: 3000, //商品轮播图切换间隔
    duration: 500, //商品轮播图切换动画时间
    slideImg: [], //幻灯片广告数据
    minusStatus: 'disabled', // 使用data数据对象设置样式名
    gotoType: 1,
    animationData: {},
    opacityData: {},
    hide: 'animathide',
    _vsi: 0,
    submitId: 0, //提交表单id
    formMoney: 0.00, //表单金额
    showcoupon: false,//是否显示付款方式
    formType: 'nopay', //是否需要支付
    region: ['河南省', '郑州市', '中原区'], //开户行地区
    areaId: 410102, //地区id
    pics: [], //图片
    goodsNums: 0,
    cart: [],
    currentKey: 0, //当前下单的商品的Key
    currentGoodsId: 0, //当前选中的商品ID
    goodsTotalMoney: '0.00', //商品总额
    originForm: [], //原始表单
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    //todo 邀请人ID
    var id = options.id;
    app.db.set("formId", id);
    if (!id) {
      app.common.errorToShow("路径错误");
      return false;
    }
    var that = this;
    var userToken = app.db.get('userToken');

    app.api.getFormDetial({
      'id': id,
      'token': userToken
    }, function (res) {
      if (!res.status) {
        if (res.data.need_login){
          //跳转首页
          app.common.errorToShow("请先登录");
          setTimeout(function () {
            wx.navigateTo({
              url: '../../member/login/level1/level1'
            });
          }, 500);
        }else{
          //跳转提示页面
          wx.redirectTo({
            url: '../other/payfailed?type=1' //过期类型为1
          });
        }
        return false;
      }

      if (res.data.type == '1' || res.data.type == '2') {
        that.getPaymentType(); //获取支付方式
        if (res.data.type == '1') { //订单
          that.data.payment_type = app.config.payment_type.form_order
        } else if (res.data.type == '2') { //付款码
          that.data.payment_type = app.config.payment_type.form_pay
        }
        that.data.formType = 'pay';
      }
      //判断是否登录，没登录去登录
      if (res.data.is_login == 1) {
        that.checkLogin();
      }
      //设置title名称
      wx.setNavigationBarTitle({
        title: res.data.name
      })
      //console.log(res.data);
      that.data.originForm = res.data;
      that.setData({
        hiddenForm:false,//显示表单
        form: res.data,
        formType: that.data.formType
      });
    });
  },
  onShow(){
    if (this.data.hiddenForm && app.db.get("formId")){
      var options = { id: app.db.get("formId")}
      //console.log("调试开始");
      //console.log(this.data.hiddenForm);
      //console.log(app.db.get("formId"));
      //console.log("调试结束");
      this.onLoad(options);
    }
  },

  //输入文本
  changeInput: function (e) {
    var id = e.currentTarget.dataset.id;
    var items = this.data.form.items;
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == id) {
        items[i].default_value = e.detail.value;
      }
    }
    this.data.form.items = items;
    this.setData({
      form: this.data.form
    });
  },

  // 选择性别
  sexChange: function (e) {
    var sexItems = this.data.sexItems;
    for (var i = 0, len = sexItems.length; i < len; ++i) {
      sexItems[i].checked = sexItems[i].value == e.detail.value;
    }
    this.setData({
      sexItems: sexItems
    });
  },

  // 选择日期
  bindDateChange: function (e) {
    var id = e.currentTarget.dataset.id;
    var items = this.data.form.items;
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == id) {
        items[i].default_value = e.detail.value;
      }
    }
    this.data.form.items = items;
    this.setData({
      form: this.data.form
    });

  },

  // 选择时间
  bindTimeChange: function (e) {
    var id = e.currentTarget.dataset.id;
    var items = this.data.form.items;
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == id) {
        items[i].default_value = e.detail.value;
      }
    }
    this.data.form.items = items;
    this.setData({
      form: this.data.form
    });
  },

  // 单选
  radioChange: function (e) {
    //console.log('radiook发生change事件，携带value值为：', e.detail.value);
    var id = e.currentTarget.dataset.value;
    var items = this.data.form.items;
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == id) {
        items[i].default_value = e.detail.value;
      }
    }

    this.data.form.items = items;
    this.setData({
      form: this.data.form
    });
  },

  /*   
  复选
  */
  checkboxChange: function (e) {
    var id = e.currentTarget.dataset.value;
    var items = this.data.form.items;
    var key = '';
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == id) {
        items[i].default_value = e.detail.value;
        key = i;
      }
    }

    items[key].checbox_value.forEach(function (item, index) {
      item.checked = false;
      if (items[key].default_value.indexOf(item.value) > -1) {
        item.checked = true;
      }
    });

    this.data.form.items = items;
    this.setData({
      form: this.data.form
    });
  },


  //商品减一
  bindMinus: function () {
    var nums = this.data.goodsNums;
    if (nums > 1) {
      nums--;
    }
    this.setData({
      goodsNums: nums
    });
    this.setNumsData();
  },


  //商品加一
  bindPlus: function () {
    var nums = this.data.goodsNums;
    nums++;
    this.setData({
      goodsNums: nums
    });
    this.setNumsData();
  },

  /* 输入框事件 */
  bindManual: function (e) {
    var num = e.detail.value;
    // 将数值与状态写回
    this.setData({
      num: num
    });
  },




  //选择位置
  chooseLocation: function (e) {
    var that = this;
    var id = e.currentTarget.dataset.id;
    var items = this.data.form.items;
    wx.chooseLocation({
      success(e) {
        for (var i = 0, len = items.length; i < len; ++i) {
          if (items[i].id == id) {
            items[i].default_value = e.latitude + "," + e.longitude;
          }
        }
        that.data.form.items = items;
        that.setData({
          form: that.data.form
        });
      },
      fail(e) {
        wx.getSetting({
          success(res) {
            if (!res.authSetting['scope.userLocation']) {
              wx.openSetting();
            }
          }
        })
      }
    });
  },


  //地区选择
  regionChange: function (e) {
    let province_name = e.detail.value[0];
    let city_name = e.detail.value[1];
    let county_name = e.detail.value[2];
    let postal_code = 0;
    let that = this;
    let data = {
      province_name: province_name,
      city_name: city_name,
      county_name: county_name,
      postal_code: postal_code
    }

    let regionName = [province_name, city_name, county_name];
    that.setData({
      region: regionName
    });

    var id = e.currentTarget.dataset.id;
    var items = that.data.form.items;
    app.api.getAreaId(data, function (res) {
      if (res.status) {
        for (var i = 0, len = items.length; i < len; ++i) {
          if (items[i].id == id) {
            items[i].default_value = res.data;
          }
        }
        that.data.form.items = items;
        that.setData({
          form: that.data.form
        });
      } else {
        wx.showModal({
          title: '提示',
          content: '地区选择出现问题，请重新选择地区',
          showCancel: false
        });
      }
    });
  },

  //选择图片
  pic_choose: function (e) {

    var that = this;
    var id = e.currentTarget.dataset.id;
    var items = that.data.form.items;
    var key = 0;
    var item = [];
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == id) {
        key = i;
        item = items[i];
      }
    }
    var array = [];
    var demo = [];
    var p = [];
    var img = [];
    var imgs;
    app.api.uploadImage(5, function (res) {
      if (res.status) {
        if (!item.pics) {
          item.pics = [];
        }
        item.pics.push({
          src: res.data.url.replace(/\\/g, "/"),
          image_id: res.data.image_id
        });
        array.push(res.data.image_id);
        if (!item.images) {
          item.images = array;
        } else {
          item.images = item.images.concat(array)
        }
        that.data.form.items[key] = item;
        that.setData({
          form: that.data.form
        });
      }
    });
  },
  //表单提交
  formSubmit(e) {
    var that = this;
    var data = e.detail.value;
    //订单时需要合并购物车信息
    if (that.data.form.type == 1) {
      if (that.data.cart.length < 1) {
        app.common.errorToShow("请先选择商品");
        return true;
      }
      var tempArray = [];
      that.data.cart.forEach(function (item, index, input) {
        tempArray[item.key + '_' + index] = item;
      });
      data = Object.assign(data, tempArray);
    }
    var userToken = app.db.get('userToken');
    app.api.addSubmitForm({
      data: data,
      'token': userToken,
      'id': this.data.form.id
    }, function (res) {
      if (!res.status) {
        app.common.errorToShow(res.msg);
        if (res.data.need_login){
          //跳转首页
          setTimeout(function () {
            wx.switchTab({
              url: '../../index/index'
            })
          }, 1500);
        }
        return false;
      }

      //表单类型判断是否需要支付，支付金额多少
      if (that.data.form.type == '1' || that.data.form.type == '2') {
        that.data.submitId = res.data.id;
        that.data.formMoney = res.data.money;
        //出来支付按钮
        that.payTypeBtn(e);
      } else {
        that.formReset();
        app.common.errorToShow(res.msg);
        //跳转首页
        setTimeout(function () {
          wx.switchTab({
            url: '../../index/index'
          })
        }, 1500);
      }
    });
  },
  //获取支付类型
  getPaymentType: function () {
    var that = this;
    app.api.getPaymentType(function (res) {
      if (res.status) {
        that.setData({
          paymentType: res.data
        });
      } else {
        app.common.errorToBack('获取支付方式失败', 0);
      }
    });
  },

  //微信支付触发
  wechatPay: function () {
    //要支付的订单号
    var that = this;
    var data = {
      ids: this.data.submitId,
      payment_code: 'wechatpay',
      payment_type: this.data.payment_type,
      params: {
        formid: this.data.formId
      }
    };
    //去支付
    app.api.pay(data, function (res) {
      if (res.status) {
        that.formReset(); //清空表单
        wx.requestPayment({
          'timeStamp': '' + res.data.timeStamp,
          'nonceStr': res.data.nonceStr,
          'package': res.data.package,
          'signType': res.data.signType,
          'paySign': res.data.paySign,
          'success': function (e) {
            if (e.errMsg == "requestPayment:ok") {
              //成功地址待处理
              wx.redirectTo({
                url: '../other/paysuccess?payment_id=' + res.data.payment_id
              });
            } else if (res.errMsg == 'requestPayment:cancel') {
                app.common.errorToBack('支付已取消', 0);
            }
          },
          'fail': function (e) {
              app.common.errorToBack('支付失败请重新支付', 0);
          }
        });
      } else {
          app.common.errorToBack('支付订单出现问题，请返回重新操作', 0);
      }
    });
  },
  //余额支付
  balancepay: function () {
    var that = this;
    //要支付的订单号
    var data = {
      ids: this.data.submitId,
      payment_code: 'balancepay',
      payment_type: this.data.payment_type,
      params: {
        formid: this.data.formId
      }
    };
      //去支付
      app.api.pay(data, function (res) {
        if (res.status) {
          that.formReset(); //清空表单
          app.common.errorToShow(res.msg);
          //成功地址待处理
          wx.redirectTo({
            url: '../other/paysuccess?payment_id=' + res.data.payment_id
          });

        } else {
          app.common.errorToShow(res.msg);
        }
      });
  },

  // 弹出付款方式
  payTypeBtn: function (e) {
    var currentStatu = e.detail.target.dataset.statu;
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
      animationData: animation.export(),
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
  //弹出付款方式隐藏
  hidePayTypeBtn: function (e) {
    var currentStatu = e.currentTarget.dataset.statu;
    this.anim(currentStatu);
  },

  //检查是否登录
  checkLogin() {
    var userToken = app.db.get('userToken');
    if (!userToken) {
        app.common.successToShow('请登录...', function () {
            wx.navigateTo({
                url: '../../member/login/level1/level1'
            });
        });
    }
  },


  //表单清空
  formReset: function () {
    app.db.set("formId", '');
    this.data.cart = []; //初始化，刷新当前页面
    this.setData({
      form: this.data.originForm
    });
  },
  /**
 * 生命周期函数--监听页面卸载
 */
  onUnload: function () {
    app.db.set("formId", '');
  },
  
  // 选择规格弹出
  specifications: function (e) {
    var key = e.currentTarget.dataset.id;
    var goods_id = e.currentTarget.dataset.goods;
    this.setData({
      goodsInfoImage: '',
      goodsSpesDesc: '',
      productId: '',
      status: '',
      goodsInfoName: '',
      goodsInfoPrint: '',
      goodsInfoNumber: '',
      gotoType: e.currentTarget.dataset.type,
      select_goods_id: goods_id,
      select_id: key
    });
    this.data.currentKey = key; //当前选中的key
    this.data.currentGoodsId = goods_id; //当前选中的商品ID
    this.getGoodsInfo(key, goods_id);
    var currentStatu = e.currentTarget.dataset.statu;
    this.anims(currentStatu);
    this.opac(currentStatu);
  },


  //获取商品详情
  getGoodsInfo: function (key, goods_id) {
    var that = this;
    var data = {
      id: goods_id
    }

    var items = that.data.form.items;
    var goods = [];
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == key) {
        goods = items[i].goods;
      }
    }
    var st = true; //是否有货
    if (goods.product.stock < 1) {
      st = false;
    }
    var spes_desc = that.getSpes(goods.product);

    var goodsNums = that.getNumsByKey(key, goods.product.id);


    that.setData({
      goodsSpesDesc: spes_desc,
      productId: goods.product.id,
      status: st,
      goodsInfoName: goods.product.name,
      goodsInfoPrint: goods.product.price,
      goodsInfoNumber: goods.product.stock,
      goodsNums: goodsNums
    });

    that.setNumsData(goods);
  },

  //数量改变设置
  setNumsData: function () {


    var nums = this.data.goodsNums;
    var stock = this.data.goodsInfoNumber;
    var minStatus = 'normal';
    var maxStatus = 'normal';

    if (nums <= 0) {
      nums = 0;
      minStatus = 'disabled';
    }
    if (nums > stock) {
      nums = stock;
      maxStatus = 'disabled';
    }
    this.setData({
      goodsNums: nums,
      minStatus: minStatus,
      maxStatus: maxStatus
    });
  },


  getSpes: function (product) {
    if (!product.default_spes_desc) {
      return [];
    }
    return product.default_spes_desc;
  },
  //获取规格信息
  selectSku: function (obj) {
    var id = obj.currentTarget.dataset.key;
    if (id == "" || id == "0") {
      app.common.errorToBack("出错了", 0);
      return false;
    }

    var data = {
      id: id,
    }
    var that = this;
    app.api.productInfo(data, function (res) {
      if (res.status) {
        var st = true;
        if (res.data.stock < 1) {
          st = false;
        }
        var spes_desc = that.getSpes(res.data);

        var currentKey = that.data.currentKey;

        var goodsNums = that.getNumsByKey(currentKey, res.data.id);

        that.setData({
          goodsSpesDesc: spes_desc,
          productId: res.data.id,
          status: st,
          goodsInfoName: res.data.name,
          goodsInfoPrint: res.data.price,
          goodsInfoNumber: res.data.stock,
          goodsNums: goodsNums
        });
      }
    });
  },

  anims: function (currentStatu) {
    var animation = wx.createAnimation({
      duration: 500,
      timingFunction: "ease",
      delay: 0
    });
    this.animation = animation;
    animation.opacity(0.5).translateY(375).step();
    this.setData({
      animationData: animation.export(),
      hide: 'animathide'
    });
    setTimeout(function () {
      animation.opacity(1).translateY(0).step();
      this.setData({
        animationData: animation,
        hide: 'animatshow'
      });
      if (currentStatu == "closespecs") {
        this.setData({
          showspecs: false
        });
      }
    }.bind(this), 500)
    if (currentStatu == "openspecs") {
      this.setData({
        showspecs: true
      });
    }
  },

  opac: function (currentStatu) {
    var animation = wx.createAnimation({
      duration: 500,
      timingFunction: "ease",
      delay: 0
    });
    this.animation = animation;
    animation.opacity(0).step();
    this.setData({
      opacityData: animation.export(),
    });
    setTimeout(function () {
      animation.opacity(1).step();
      this.setData({
        opacityData: animation
      });
      if (currentStatu == "closespecs") {
        this.setData({
          showspecs: false
        });
      }
    }.bind(this), 500)
    if (currentStatu == "openspecs") {
      this.setData({
        showspecs: true
      });
    }
  },



  //加入购物车
  goodsAddCart: function () {
    var that = this;

    var productId = that.data.productId;
    var currentKey = that.data.currentKey;
    //无加入购物车
    if (that.data.goodsNums<1){
      that.getCartNums();
      that.anims('closespecs');
      return true;
    }

    if (that.data.cart.length < 1) {
      that.data.cart.push({
        key: currentKey,
        productId: productId,
        goodsId: that.data.select_goods_id,
        nums: that.data.goodsNums,
        price: that.data.goodsInfoPrint
      });
    } else {
      var isIn = false;
      for (var i = 0; i < that.data.cart.length; i++) {
        if (that.data.cart[i].key == currentKey && that.data.cart[i].productId == productId) {
          that.data.cart[i] = {
            key: currentKey,
            productId: productId,
            goodsId: that.data.select_goods_id,
            nums: that.data.goodsNums,
            price: that.data.goodsInfoPrint
          };
          isIn = true;
        }
      }
      if (!isIn) {
        that.data.cart.push({
          key: currentKey,
          productId: productId,
          goodsId: that.data.select_goods_id,
          nums: that.data.goodsNums,
          price: that.data.goodsInfoPrint
        });
      }
    }

    that.getCartNums();
    that.anims('closespecs');
    return true;
    //暂时不加入购物车
    /*
    var data = {
      product_id: that.data.productId,
      nums: that.data.goodsNums
    }
    app.api.goodsAddCart(data, function(res) {
      app.common.successToShow(res.msg);
    });*/
  },

  /*获取key的数量 */
  getNumsByKey: function (key, productId) {
    var that = this;
    if (that.data.cart.length < 1) {
      return 0;
    } else {
      for (var i = 0; i < that.data.cart.length; i++) {
        if (that.data.cart[i].key == key && that.data.cart[i].productId == productId) {
          return that.data.cart[i].nums
        }
      }
      return 0;
    }
  },

  //获取购物车数量
  getCartNums: function () {
    var that = this;
    var items = that.data.form.items;
    var itemKey = '';
    for (var i = 0, len = items.length; i < len; ++i) {
      if (items[i].id == that.data.currentKey) {
        itemKey = i;
      }
    }

    if (that.data.form.items[itemKey].goods.id == that.data.currentGoodsId) {
      if (that.data.form.items[itemKey].cart_count > 0) {
        var cart_count = 0;
        var currentKey = that.data.currentKey;
        that.data.cart.forEach(function (item, index, input) {
          //console.log(item);
          if (item.key == currentKey) {
            cart_count += item.nums;
          }
          that.data.form.items[itemKey].cart_count = cart_count;
        })
      } else {
        that.data.form.items[itemKey].cart_count = that.data.goodsNums;
      }
    } else {
      that.data.form.items[itemKey].cart_count = that.data.goodsNums;
    }


    that.setData({
      form: that.data.form
    });
    that.getGoodsTotalMoney(); //计算总价
  },
  //获取商品总额
  getGoodsTotalMoney: function () {
    var that = this;
    var goodsTotalMoney = 0;
    that.data.cart.forEach(function (item, index, input) {
      goodsTotalMoney += item.price * item.nums;
    })
    that.setData({
      goodsTotalMoney: app.common.formatMoney(goodsTotalMoney, 2, '')
    });
  }

})