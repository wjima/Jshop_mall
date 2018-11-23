//购物车页面
var app = getApp();

Page({
  data: {
    startX: 0, //开始坐标
    startY: 0,
    cartData: {}, //购物车数据
    cartIds: [], //选中ids
    checkboxAll: false, //全选按钮
    total: 0.00, //总价
    goSettlement: false, //去结算按钮
    cartId: '',
    cartNum: '',
    isLoad: false,
    cartNums: 0
  },

  //获取购物车数据
  getCartData: function () {
    var page = this;
    var cartIds = page.arrayToStr(page.data.cartIds);
    var data = {
      ids: cartIds,
      display: 'all'
    };
    app.api.cartList(data, function (res) {
      if (res.status) {
        var data = res.data;
        page.showHandle(data); //数量设置
      }
    });
  },

  //数组转字符串
  arrayToStr: function (array) {
    return array.toString();
  },

  //渲染前配置数据
  showHandle: function (data) {
    var page = this;
    var goSettlement = false;
    for (var i in data.list) {
      //不可能购买0件
      if (data.list[i].nums < 1) {
        data.list[i].nums = 1;
      }
      //不能买大于库存的数量
      if (data.list[i].nums > data.list[i].products.stock) {
        data.list[i].nums = data.list[i].products.stock;
      }
      //设置样式
      data.list[i].minStatus = 'normal';
      data.list[i].maxStatus = 'normal';
      if (data.list[i].nums == 1) {
        data.list[i].minStatus = 'disabled';
      }
      if (data.list[i].nums == data.list[i].products.stock) {
        data.list[i].maxStatus = 'disabled'
      }
      //设置规格参数
      data.list[i].spes = [];
      if (data.list[i].products.spes_desc != null) {
        var spesArray = data.list[i].products.spes_desc.split(",");
        for (var key in spesArray) {
          var spesOne = spesArray[key].split(":");
          data.list[i].spes.push(spesOne[1]);
        }
      }
      //添加左滑效果
      data.list[i].isTouchMove = false;
      //是否可以去支付
      if(data.list[i].is_select) {
        goSettlement = true;
      }
    }
    data.goods_pmt = app.common.formatMoney(data.goods_pmt, 2, '');
    data.order_pmt = app.common.formatMoney(data.order_pmt, 2, '');
    data.amount = app.common.formatMoney(data.amount, 2, '');
    var isLoad = false;
    if(data.list.length < 1){
      isLoad = true;
    }
    var n = 0;
    for (var i in data.promotion_list) {
      n++;
    }
    page.setData({
      cartData: data,
      goSettlement: goSettlement,
      isLoad: isLoad,
      cartNums: n
    });
    page.isAllCheckbox();
  },

  //是否全选
  isAllCheckbox: function () {
    var page = this;
    var cartData = page.data.cartData.list;
    var goSettlement = false;
    var flag = true;
    for (var key in cartData) {
      if (cartData[key].is_select == false) {
        flag = false;
      }
      if (cartData[key].is_select == true) {
        goSettlement = true;
      }
    }
    if (cartData.length <= 0) {
      flag = false;
    }
    this.setData({
      checkboxAll: flag,
      goSettlement: goSettlement
    });
  },

  //选择操作
  checkbox: function (e) {
    var page = this;
    var id = e.currentTarget.dataset.id;
    var cartData = page.data.cartData;
    for (var key in cartData.list) {
      if (key == id) {
        if (cartData.list[key].is_select == true) {
          cartData.list[key].is_select = false;
        } else {
          cartData.list[key].is_select = true;
        }
      }
    }
    page.setData({
      cartData: cartData
    });
    page.setNumsData();
    page.isAllCheckbox();
  },

  //全选操作
  checkboxAll: function (e) {
    if (this.data.checkboxAll == true) {
      this.setData({
        checkboxAll: false
      });
      this.setAllCheckbox(false);
    } else {
      this.setData({
        checkboxAll: true
      });
      this.setAllCheckbox(true);
    }
  },

  //全选设置
  setAllCheckbox: function (e) {
    var page = this;
    var cartData = page.data.cartData;
    if (e) {
      //全选
      for (var key in cartData.list) {
        cartData.list[key].is_select = true;
      }
    } else {
      //全不选
      for (var key in cartData.list) {
        cartData.list[key].is_select = false;
      }
    }
    page.setData({
      cartData: cartData
    });
    page.setNumsData();
    page.isAllCheckbox();
  },

  //设置刷新数据
  setNumsData: function () {
    var page = this;
    var cartData = page.data.cartData;
    var cartIds = [];
    for (var key in cartData.list) {
      if (cartData.list[key].is_select) {
        cartIds.push(cartData.list[key].id);
      } else {
        if (cartData.list[key].products.promotion_list) {
          for (var k in cartData.list[key].products.promotion_list) {
            cartData.list[key].products.promotion_list[k].type = 1;
          }
        }
      }
    }
    page.setData({
      cartIds: cartIds,
      cartData: cartData
    });
    if (cartIds.length == 0) {
      var cartData = page.data.cartData;
      for (var k in cartData.promotion_list) {
        cartData.promotion_list[k].type = 1;
      }
      cartData.goods_pmt = '0.00';
      cartData.order_pmt = '0.00';
      cartData.amount = '0.00';
      page.setData({
        cartData: cartData
      });
    } else {
      page.getCartData();
    }
  },

  //数量减一
  bindMinus: function (e) {
    var page = this;
    var id = e.currentTarget.dataset.id;
    var num = 1;
    var cartData = page.data.cartData;
    for (var key in cartData.list) {
      if (cartData.list[key].id == id) {
        if (cartData.list[key].nums > 1) {
          cartData.list[key].nums--;
          num = cartData.list[key].nums;
          if (num <= 1) {
            cartData.list[key].minStatus = 'disabled';
          }
          if (num < cartData.list[key].products.stock) {
            cartData.list[key].maxStatus = 'normal';
          }
        } else {
          return false
        }
      }
    }
    page.setData({
      cartData: cartData,
      cartId: id,
      cartNum: num
    });
    app.common.throttle(page.bindMinusOperation, page, 350);
  },

  //数量减一操作
  bindMinusOperation: function () {
    var page = this;
    page.setCartNum(page.data.cartId, page.data.cartNum);
  },

  //数量加一
  bindPlus: function (e) {
    var page = this;
    var id = e.currentTarget.dataset.id;
    var num = 1;
    var cartData = page.data.cartData;
    for (var key in cartData.list) {
      if (cartData.list[key].id == id) {
        if (cartData.list[key].nums < cartData.list[key].products.stock) {
          cartData.list[key].nums++;
          num = cartData.list[key].nums;
          if (num >= cartData.list[key].products.stock){
            cartData.list[key].maxStatus = 'disabled';
          }
          if (num > 1) {
            cartData.list[key].minStatus = 'normal';
          }
        } else {
          return false;
        }
      }
    }
    page.setData({
      cartData: cartData,
      cartId: id,
      cartNum: num
    });
    app.common.throttle(page.bindPlusOperation, page, 350);
  },

  //数量加一操作
  bindPlusOperation: function () {
    var page = this;
    page.setCartNum(page.data.cartId, page.data.cartNum);
  },

  //输入框数量改变
  bindManual: function (e) {
    var page = this;
    var id = e.currentTarget.dataset.id;
    var num = 1;
    var cartData = page.data.cartData;
    for (var key in cartData.list) {
      if (cartData.list[key].id == id) {
        if (e.detail.value < 1){
          e.detail.value = 1;
          cartData.list[key].minStatus = 'disabled';
        }
        if (e.detail.value >= cartData.list[key].products.stock) {
          e.detail.value = cartData.list[key].products.stock;
          cartData.list[key].maxStatus = 'disabled';
        }
        cartData.list[key].nums = e.detail.value;
        num = cartData.list[key].nums;
      }
    }
    page.setCartNum(id, num);
  },

  //设置购物车数量
  setCartNum: function (id, nums) {
    var page = this;
    var data = {
      id: id,
      nums: nums
    }
    app.api.setCartNum(data, function (res) {
      page.getCartData();
    });
  },

  //删除事件
  del: function (e) {
    var page = this;
    //移除渲染
    page.data.cartData.list.splice(e.currentTarget.dataset.index, 1);
    page.setData({
      cartData: page.data.cartData
    });
    //移除数据库
    var data = {
      ids: e.currentTarget.dataset.cartid
    }
    app.api.goodsDelCart(data, function (res) {
      if (res.status) {
        wx.showToast({
          title: res.msg
        });
      }
      page.setNumsData();
      page.isAllCheckbox();
    });
  },

  //收藏
  collection: function (e) {
    var page = this;
    app.db.userToken(function (token) {
      var data = {
        goods_id: e.currentTarget.dataset.goodsid
      }
      app.api.goodsCollection(data, function (res) {
        for (var k in page.data.cartData.list) {
          if(page.data.cartData.list[k].products.goods_id == e.currentTarget.dataset.goodsid) {
            if(res.msg == '收藏成功'){
              page.data.cartData.list[k].isCollection = true;
            }else{
              page.data.cartData.list[k].isCollection = false;
            }
          }
        }
        wx.showToast({
          title: res.msg
        });
      });
    });
  },

  //去结算
  settlement: function (e) {
    var page = this;
    if (page.data.goSettlement) {
      var cartData = page.data.cartData.list;
      var newData = '';
      for (var key in cartData) {
        if (cartData[key].is_select == true) {
          newData += ',' + cartData[key].id;
        }
      }
      if (newData.substr(0, 1) == ',') {
        newData = newData.substr(1);
      }
      if (newData.length > 0) {
        wx.navigateTo({
          url: '../firmOrder/firmOrder?data=' + JSON.stringify(newData),
        });
      } else {
        //没有选择不跳转
      }
    }
  },

    //查看商品详情
    goodsDetail: function (e) {
        let ins = encodeURIComponent('id=' + e.currentTarget.dataset.id);
        wx.navigateTo({
            url: '../../goods/detail/detail?scene=' + ins
        });
    },

  //手指触摸动作开始 记录起点X坐标
  touchstart: function (e) {
    //开始触摸时 重置所有删除
    var page = this;
    page.data.cartData.list.forEach(function (v, i) {
      if (v.isTouchMove)//只操作为true的
        v.isTouchMove = false;
    })
    page.setData({
      startX: e.changedTouches[0].clientX,
      startY: e.changedTouches[0].clientY,
      cartData: page.data.cartData
    })
  },

  //滑动事件处理
  touchmove: function (e) {
    var page = this;
    var index = e.currentTarget.dataset.index; //当前索引
    var startX = page.data.startX; //开始X坐标
    var startY = page.data.startY; //开始Y坐标
    var touchMoveX = e.changedTouches[0].clientX; //滑动变化坐标
    var touchMoveY = e.changedTouches[0].clientY; //滑动变化坐标
    var angle = page.angle({ X: startX, Y: startY }, { X: touchMoveX, Y: touchMoveY }); //获取滑动角度
    page.data.cartData.list.forEach(function (v, i) {
      v.isTouchMove = false
      //滑动超过30度角 return
      if (Math.abs(angle) > 30) return;
      if (i == index) {
        if (touchMoveX > startX) //右滑
          v.isTouchMove = false
        else //左滑
          v.isTouchMove = true
      }
    })
    //更新数据
    page.setData({
      cartData: page.data.cartData
    })
  },

  //计算滑动角度
  angle: function (start, end) {
    var _X = end.X - start.X,
      _Y = end.Y - start.Y
    //返回角度 /Math.atan()返回数字的反正切值
    return 360 * Math.atan(_Y / _X) / (2 * Math.PI);
  },

  //页面加载
  onShow: function () {
    var page = this;
    app.db.userToken(function (token) {
      page.getCartData(); //获取购物车数据
    });
  }
});