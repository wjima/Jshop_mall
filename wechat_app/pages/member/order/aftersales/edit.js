const app = getApp(); //获取全局app.js

Page({

  /**
   * 页面的初始数据
   */
  data: {
    delivers: ["请选择物流公司","顺丰", "中通", "圆通","韵达"],
    deliverIndex: 0,
    type_name:'',     //售后类型显示
    refund:0,         //退款金额
    images:[],        //图片
    reason: '暂无',       //问题描述
    ttype:1,          //售后类型
    status:1,         //售后单状态
    status_name: '审核中',   //售后单状态文字描述
    reship_status:0,        //退货单状态
    reship_name:'',
    refund_status:0,        //退款单状态
    refund_name:'',
    reship_info:[],         //退货单明细,如果售后单未审核呢，那么显示的是售后单明细，如果售后单审核通过了，显示退款单明细
    items:[],             //退货明细
    mark:"暂无",            //拒绝原因
    logi_no:'',            //回填物流信息
    logi_code:'',          //物流公司
    reship_id:'',
    mode: 'aspectFit',
  },
  bindDeliverChange: function (e) {
    this.setData({
      deliverIndex: e.detail.value
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var aftersales_id = options.aftersales_id;
    var page = this;
    app.api.aftersalesInfo(aftersales_id,function(res){
      if(res.status){
        var info = res.data.info;
        if (info.type == 1){
          page.data.type_name = '仅退款';
        }else{
          page.data.type_name = '退款退货';
        }
        page.data.refund = info.refund;
        page.data.images = info.images;
        page.data.reason = info.reason;
        page.data.reship_info = res.data.reship;
        if(info.status == 1)
        {
          page.data.status = 1;
          page.data.status_name = '审核中';
        }else if(info.status == 2)
        {
          page.data.status = 2;
          page.data.status_name = '申请通过';
          //退款单状态
          if (info.bill_refund)
          {
            if (info.bill_refund.status == 1)
            {
              page.data.refund_status = 1;
              page.data.refund_name = '退款中';
            } else if (info.bill_refund.status == 2)
            {
              page.data.refund_status = 2;
              page.data.refund_name = '退款成功';
            }else{

            }
          }

          //退货单状态
          if(info.bill_reship)
          {
            page.data.reship_id = info.bill_reship.reship_id
            if(info.bill_reship.status == 1)
            {
              page.data.reship_status = 1;
              page.data.reship_name = '待发退货';
            } else if (info.bill_reship.status == 2)
            {
              page.data.reship_status = 2;
              page.data.reship_name = '待收退货';
              page.data.logi_no = info.bill_reship.logi_no;
              page.data.logi_code = info.bill_reship.logi_code;
            }else{
              page.data.reship_status = 3;
              page.data.reship_name = '已收退货';
              page.data.logi_no = info.bill_reship.logi_no;
              page.data.logi_code = info.bill_reship.logi_code;
            }
          }
        }else{
          page.data.status = 3;
          page.data.status_name = '申请驳回';
        }
        //售后单明细,如果有退货单明细，就用退货单明细，否则就用售后单明细
        // if(info.bill_reship.items){
        //   page.data.items = info.bill_reship.items;
        // }else{
        //   page.data.items = info.items;
        // }
        

        page.setData(page.data);

      }else{
        app.common.errorToBack(res.msg);
      }
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

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },
  loginoChange: function (e) {
    this.setData({
      logi_no: e.detail.value
    })
  },
  logicodeChange: function (e) {
    this.setData({
      logi_code: e.detail.value
    })
  },
  //提交按钮
  submitBtn: function () {
    if (this.data.logino == '') {
      app.common.errorToBack('请输入退货快递信息',0);
      return false;
    }
    
    var data = {
      logi_no: this.data.logi_no,
      logi_code:this.data.logi_code,
      reship_id: this.data.reship_id,
    };
    app.api.sendReship(data, function (res) {
      if (res.status) {
        app.common.successToShow('提交成功',function(){
          wx.navigateBack({
            delta: 1
          });
        });
      } else {
        app.common.errorToBack(res.msg, 0);
      }
    });

  },
})