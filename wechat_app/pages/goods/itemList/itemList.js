//商品列表
var app = getApp(); //全局APP

Page({
  //页面的初始数据
  data: {
    id: '',
    showView: false,
    goodsList: [],
    minPrice: '',
    maxPrice:'',
    ajaxStatus: false,
    loading: true,
    loadingComplete: false,
    nodata:false,
    toView: "",
    searchData: {
      where: {},
      limit: app.config.list_limit,
      page: 1,
      order: {
        key:'id',
        sort:'desc'
      }
    }
  },

  //加载执行
  onLoad: function (options) {
    var where = {};
    if (options.id) {
      where = {
        cat_id: options.id
      }
    }
    if (options.key) {
      where = {
        search_name: options.key
      }
    }
    if (options.type) {
      if (options.type == 'hot') {
        where = {
          hot: true
        }
      }
    }
    this.setSearchData({
      where: where
    });
    this.getGoods();
  },

  //设置查询条件
  setSearchData: function(searchData,clear=false){
    var sd = this.data.searchData;
    this.setData({
      searchData: app.common.deepCopy(sd, searchData),
    });
    if(clear){
      this.setData({
        goodsList: []
      });
    }
  },

  onChangeShowState: function () {
    var that = this;
    that.setData({
      showView: (!that.data.showView)
    })
  },

  //点击综合排序
  comprehensive:function() {
    this.setSearchData({
      order: {
        key: 'id',
        sort: 'desc'
      },
      page:1
    },true);
    this.getGoods();
  },

  //销量
  salesVolume: function () {
    if (this.data.searchData.order.key == 'buy_count'){
      if (this.data.searchData.order.sort == 'desc'){
        this.data.searchData.order.sort = 'asc';
      }else{
        this.data.searchData.order.sort = 'desc';
      }
    }else{
      this.data.searchData.order = {
        key: 'buy_count',
        sort: 'desc'
      };
    }
    this.data.searchData.page = 1;      //从第一页重新显示
    this.setSearchData(this.data.searchData,true);
    this.getGoods();
  },

  //价格排序
  priceSort: function () {
    if (this.data.searchData.order.key == 'price') {
      if (this.data.searchData.order.sort == 'desc') {
        this.data.searchData.order.sort = 'asc';
      } else {
        this.data.searchData.order.sort = 'desc'
      }
    } else {
      this.data.searchData.order = {
        key: 'price',
        sort: 'asc'
      };
    }
    this.data.searchData.page = 1;      //从第一页重新显示
    this.setSearchData(this.data.searchData,true);
    this.getGoods();
  },

  //设置查询价格区间
  maxPrice: function(e) {
    var reg = /^[0-9]+(.[0-9]{2})?$/;
    if (!reg.test(e.detail.value)) {
      app.common.errorToBack('请输入正确金额', 0);
    } else {
      this.setData({
        maxPrice: e.detail.value
      })
    }
  },

  minPrice: function (e) {
    var reg = /^[0-9]+(.[0-9]{2})?$/;
    if (!reg.test(e.detail.value)) {
      app.common.errorToBack('请输入正确金额',0);
    }else{
      this.setData({
        minPrice: e.detail.value
      })
    }



    
  },

  //查询价格区间
  searchPrice: function (event) {
    if (this.data.minPrice > this.data.maxPrice){
      app.common.errorToBack('价格区间有误', 0);
      return false;
    }



    this.setSearchData({
      page:1,
      where:{
        price_f:this.data.minPrice,
        price_t:this.data.maxPrice
      }
    },true);
    this.getGoods();
  },

  //页面相关事件处理函数--监听用户下拉动作
  onPullDownRefresh: function () {
    // wx.showNavigationBarLoading()
    // setTimeout(function () {
    //   // complete
    //   wx.hideNavigationBarLoading() //完成停止加载
    //   wx.stopPullDownRefresh() //停止下拉刷新
    // }, 1500);
  },

  //页面上拉触底事件的处理函数
  // onReachBottom: function () {
  //   // setTimeout(() => {
  //   //   this.setData({
  //   //     isHideLoadMore: true,
        
  //   //   })
  //   // }, 1000)
  // },

  //跳转到商品详情页面
  goodsDetail: function (e) {
    let ins = encodeURIComponent('id=' + e.currentTarget.dataset.id);
    wx.navigateTo({
        url: '../detail/detail?scene=' + ins
    });
  },

  //取得商品数据
  getGoods: function(){
    var page = this;
    if (page.data.ajaxStatus) {
      return false;
    }
    page.setData({
      ajaxStatus: true,
      loading:true,
      loadingComplete:false,
      nodata:false,
    });
    //如果已经没有数据了，就不取数据了，直接提示已经没有数据
    if (page.data.loadingComplete) {
      wx.showToast({
        title: '暂时没有数据了',
        icon: 'success',
        duration: 2000
      });
      return false;
    }
    app.api.goodsList(this.data.searchData,function(res){
      if(res.status){
        //判是否没有数据了，只要返回的记录条数小于总记录条数，那就说明到底了，因为后面没有数据了 
        var isEnd = false;
        if (res.data.list.length < page.data.searchData.limit){
          isEnd = true;
        }
        //判断是否为空
        var isEmpty = false;
        if (page.data.searchData.page == 1 && res.data.list.length == 0){
          isEmpty = true;
        }

        page.setData({
          goodsList: page.data.goodsList.concat(res.data.list),
          ajaxStatus: false,
          loading: !isEnd && !isEmpty,
          toView: '',
          loadingComplete: isEnd && !isEmpty,
          nodata: isEmpty,
        });
      }
    });
  },

  //上拉加载
  lower: function () {
    var page = this;
    page.setData({
      toView: 'loading'
    });
    if (!page.data.loadingComplete){
      page.setSearchData({
        page: page.data.searchData.page + 1
      });
      page.getGoods();
    }
  },

  //搜索
  // search: function (e) {
  //   var page = this;
  //   var key = e.detail.value;
  //   page.setSearchData({
  //     where: {
  //       search_name: key
  //     },
  //     page: 1
  //   }, true);
  //   page.getGoods();
  // },

  //跳转搜索
  searchNav: function () {
    wx.navigateTo({
      url: '../search/search'
    });
  }
});