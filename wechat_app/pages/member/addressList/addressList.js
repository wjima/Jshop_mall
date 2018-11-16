//收货地址管理
const app = getApp(); //获取全局app.js

Page({
    //页面的初始数据
    data: {
        address: [],
        select: 'false'
    },


    //新增收货地址跳转
    address: function () {
        wx.navigateTo({
            url: '../address/address',
        });
    },


    //初次加载
    onLoad: function (e) {
        let select = 'false';
        if(e.select == 'true'){
            select = 'true';
        }
        this.setData({
            select: select
        });
    },


    //页面加载
    onShow: function () {
        this.getDataList();
    },


    //获取收货地址列表
    getDataList: function () {
        var page = this;
        app.api.getUserShip(function (res) {
            page.setData({
                address: res.data
            });
        });
    },


    //删除收货地址
    delAddress: function (e) {
        let page = this;
        let id = e.currentTarget.dataset.id;
        let data = {
            'id': id
        }
        app.api.removeship(data, function(res){
            if (res.status) {
                wx.showToast({
                    title: '删除成功',
                    icon: 'success',
                    mask: true,
                    complete: function () {
                        setTimeout(function () {
                            page.getDataList();
                        }, 1500);
                    }
                });
            } else {
                wx.showModal({
                    title: '提示',
                    content: res.msg,
                    showCancel: false
                });
            }
        });
    },


    //设置默认地址
    setDefault: function (e) {
        let page = this;
        let id = e.currentTarget.dataset.id;
        let data = {
            'id': id
        }
        app.api.setdefship(data, function (res) {
            if (res.status) {
                wx.showToast({
                    title: '设置成功',
                    icon: 'success',
                    mask: true,
                    complete: function () {
                        setTimeout(function () {
                            page.getDataList();
                        }, 1500);
                    }
                });
            } else {
                wx.showModal({
                    title: '提示',
                    content: res.msg,
                    showCancel: false
                });
            }
        });
    },


    //修改收货地址
    editAddress: function (e) {
        let page = this;
        let id = e.currentTarget.dataset.id;
        if (page.data.select == 'false'){
            wx.navigateTo({
                url: '../address/editAddress?id=' + id,
            });
            return false;
        } else {
            let data = {
                'id': id
            }
            app.api.getshipdetail(data, function (res) {
                if (res.status) {
                    let pages = getCurrentPages();
                    let prevPage = pages[pages.length - 2];
                    let region = res.data.area_name.split(" ");
                    prevPage.setData({
                        isAddress: true,
                        name: res.data.name,
                        mobile: res.data.mobile,
                        address: res.data.address,
                        areaId: res.data.area_id,
                        userShipId: id,
                        area: region,
                    });
                    wx.navigateBack(1);
                } else {
                    wx.showModal({
                        title: '提示',
                        content: '该地址存在问题，无法使用',
                        showCancel: false
                    });
                }
            });
        }
    },


    //微信地址获取
    wechatAddress: function () {
        let page = this;
        app.db.userToken(function (token) {
            wx.chooseAddress({
                success: function (res) {
                    if (res.errMsg == "chooseAddress:ok") {
                        //获取成功
                        //存储这个收获地区信息到数据库
                        let data = {
                            province_name: res.provinceName,
                            city_name: res.cityName,
                            county_name: res.countyName,
                            postal_code: res.postalCode
                        };
                        let areaId = 0;
                        app.api.getAreaId(data, function (res1) {
                            if (res1.status) {
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
                                    is_def: 2
                                }
                                app.api.saveUserShip(userShipData, function (res2) {
                                    if (res2.status) {
                                        wx.showToast({
                                            title: '存储微信地址成功',
                                            icon: 'success',
                                            mask: true,
                                            complete: function () {
                                                setTimeout(function () {
                                                    page.getDataList();
                                                }, 1500);
                                            }
                                        });
                                    }else{
                                        wx.showModal({
                                            title: '提示',
                                            content: '存储微信地址失败',
                                            showCancel: false
                                        });
                                    }
                                });
                            }else{
                                wx.showModal({
                                    title: '提示',
                                    content: '获取微信地址失败',
                                    showCancel: false
                                });
                            }
                        });
                    } else {
                        wx.showModal({
                            title: '提示',
                            content: '获取微信地址失败',
                            showCancel: false
                        });
                    }
                }
            });
        });
    }
});