//商品列表
var app = getApp(); //全局APP

Page({
    //页面的初始数据
    data: {
        key: '',
        keys: [],
        navType: 'toNav',
        recommend: [],
    },

    //加载执行
    onShow: function () {
        var search_key = wx.getStorageSync('search_key');
        this.setData({
            key: '',
            keys: search_key
        });
        this.getRecommend();
    },
    
    //获取推荐
    getRecommend: function () {
        let page = this;
        let key = app.db.get('recommend_key');
        if (key) {
            //有缓存
            page.setData({
                recommend: key
            });
        } else {
            //重新获取
            app.api.getRecommendKeys(function (res){
                if (res.status) {
                    //用来展示
                    page.setData({
                        recommend: res.data
                    });
                    //缓存数据
                    app.db.set('recommend_key', res.data);
                }
            });
        }
    },

    //搜索
    search: function () {
        var keys = this.data.key;
        if(keys != '') {
            var search_key = wx.getStorageSync('search_key');
            if (!search_key) {
                search_key = [];
            }
            var flag = true;
            for (var key in search_key) {
                if (search_key[key] == keys) {
                    flag = false;
                }
            }
            if (flag) {
                search_key.unshift(keys);
            }
            wx.setStorageSync('search_key', search_key);
            wx.redirectTo({
                url: '../itemList/itemList?key=' + keys
            });
        }
    },

    //关键字设置
    addkey: function (e) {
        this.setData({
            key: e.detail.value
        });
    },

    //清除
    delete: function () {
        //删除显示
        this.setData({
            keys: []
        });
        //删除存储
        app.db.del('search_key');
    },

    //删除操作
    delKey: function (e) {
        var key = e.target.dataset.key;
        var search_key = wx.getStorageSync('search_key');
        var index = search_key.indexOf(key);
        search_key.splice(index, 1);
        this.setData({
            keys: search_key
        });
        wx.setStorageSync('search_key', search_key);
    },

    //跳转操作
    toNav: function (e) {
        var key = e.target.dataset.key;
        wx.navigateTo({
            url: '../itemList/itemList?key=' + key
        });
    }
});