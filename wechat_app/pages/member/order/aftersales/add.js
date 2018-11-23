var app = getApp();
Page({
  //页面的初始数据
  data: {
    type_list: [
      { value: '1', name: '仅退款', checked: true, disabled: false },
      { value: '2', name: '退货退款', checked: false, disabled:false },
    ],
    order_id:'',
    items:[],   //退货明细
    item_ids:[],  //选择的退货
    aftersale_type:1,     //售后类型1退款，2退款退货
    refund:0,   //退款金额，等于已支付的金额减去已退款的金额
    refund_show:0,
    images:[],      //图片
    reason:'',      //原因
    reason_size:0,
    image_max: app.config.image_max,    //用于前台判断上传图片按钮是否显示
    refund_input_noedit: true,
    mode: 'aspectFit',
  },
  
  //生命周期函数--监听页面加载
  onLoad: function (options) {
    var page = this;
    //options.order_id = '15203049601357';
    app.api.aftersalesStatus(options.order_id, function (res) {
      if (res.status) {
        //如果不是未支付的，已取消的，已完成的状态，就都可以售后
        if (res.data.text_status != 'pending_payment' && res.data.text_status != 'completed' && res.data.text_status != 'cancel'){
          //判断是已付款未发货，如果是，就禁用退货
          if (res.data.text_status == 'pending_delivery'){
            var type_list = page.data.type_list;
            type_list[1].disabled = true; 
          }

          //设置已选中的商品
          var nums = 0;
          for(var i=0;i<res.data.items.length;i++){
            //  if(res.data.items[i].sendnums > res.data.items[i].reship_nums){
            //    nums = res.data.items[i].sendnums - res.data.items[i].reship_nums;
            //  }
            nums = res.data.items[i].nums;
            page.data.item_ids = page.data.item_ids.concat({ id: res.data.items[i].id, nums: nums });
          }
          page.setData({
            order_id:options.order_id,
            items:res.data.items,
            item_ids: page.data.item_ids,
            refund: res.data.payed - res.data.refunded,
            refund_show: res.data.payed - res.data.refunded,
            type_list:type_list,
          });
        }else{
          app.common.errorToBack('订单不可以进行售后');
        }
      } else {
        app.common.errorToBack('没有找到此订单');
        
      }
    });
  },

  //描述的事件
  bindReasonChange: function (e) {
    this.setData({
      reason_size: e.detail.value.length,
      reason:e.detail.value
    })
  },

  //退货商品选择
  checkboxChange: function(e) {
    var nums = 0;
    this.data.item_ids = [];
    for (var i = 0; i < e.detail.value.length; i++) {
      var k = e.detail.value[i];
      if (this.data.items[k].sendnums > this.data.items[k].reship_nums) {
        nums = this.data.items[k].sendnums - this.data.items[k].reship_nums;
        this.data.item_ids = this.data.item_ids.concat({ id: this.data.items[k].id, nums: nums });
      }
    }
    this.setData({
      item_ids:this.data.item_ids,
    });
  
  },

  //更改事件
  bindRefundChange: function(e) {
    this.setData({
      refund: e.detail.value
    });


    
  },

  //选择服务类型
  // radioChange: function (e) {
  //   // this.setData({
  //   //   aftersale_type: e.datail.value
  //   // });
  // },

  radioChange: function (e) {
    //如果选择退款，就把退款金额不能手输入，默认全部可退的金额
     var refund_input_noedit = true;
     var refund = this.data.refund_show;
    if(e.detail.value == 1){
      refund_input_noedit = true;
    }else{
      refund_input_noedit = false;


    }
    this.setData({
      aftersale_type: e.detail.value,
      refund:refund,
      refund_input_noedit:refund_input_noedit
    });
  },

  //上传图片
  upImage: function(e){
    var page = this;
    var num = app.config.image_max - this.data.images.length;
    if(num >=0){
      app.api.uploadImage(num, function (res) {
        page.setData({
          images: page.data.images.concat(res.data)
        });
      });
    }
  },

  //删除图片
  delImage: function (e) {
    this.data.images.splice(e.target.dataset.index, 1);
    this.setData({
      images: this.data.images,
    });
  },

  //提交
  submitBtn:function(e){
    let formId = e.detail.formId;
    var images = [];
    for(var i=0;i<this.data.images.length;i++){
      images = images.concat(this.data.images[i].image_id);
    }

    //判断退款金额
    var reg = /^[0-9]+(.[0-9]{1,2})?$/;
    if (!reg.test(this.data.refund)) {
      app.common.errorToBack('请输入正确金额', 0);
      return false;
    } else {
      if (this.data.refund > this.data.refund_show) {
        app.common.errorToBack('退款金额过大', 0);
        return false;
      } 
    }

    //组装数据，提交数据
    var data = {
      order_id:this.data.order_id,
      type: this.data.aftersale_type,
      items:this.data.item_ids,
      images:images,
      refund: this.data.refund,
      reason:this.data.reason,
      formId: formId
    };
    app.api.addAftersales(data,function(res){
      if(res.status){
        app.common.successToShow('提交成功',function(){
          wx.navigateBack({
            delta: 1
          })
        });
      }else{
        app.common.errorToBack(res.msg,0);
      }
    });

  }


})