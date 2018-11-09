//门店列表
var app = getApp(); //全局APP

Page({
    //页面数据
    data: {
        storeList: [],
        key: ''
    },

    //加载执行
    onLoad: function (options) {
        this.getStoreList();
    },

    //获取门店列表
    getStoreList: function (){
        let page = this;
        let data = {
            'key': page.data.key
        }
        app.api.getStoreList(data, function(res){
            page.setData({
                storeList: res.data
            });
        });
    },

    //门店选择
    storeSelect: function(e){
        let id = e.currentTarget.dataset.id;
        let name = e.currentTarget.dataset.name;
        let mobile = e.currentTarget.dataset.mobile;
        let address = e.currentTarget.dataset.address;
        let pages = getCurrentPages();
        let prevPage = pages[pages.length - 2];
        prevPage.setData({
            store_id: id,
            store_name: name,
            store_mobile: mobile,
            store_address: address
        });
        wx.navigateBack(1);
    },

    //记录关键字
    addkey: function (e) {
        this.setData({
            key: e.detail.value
        });
    },

    //搜索
    search: function () {
        this.getStoreList();
    }
});