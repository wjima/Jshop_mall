//修改收货地址
const app = getApp(); //获取全局app.js

Page({
    //页面的初始数据
    data: {
        id: 0, //ID
        name: '', //姓名
        mobile: '', //手机号
        region: ['河南省', '郑州市', '中原区'], //开户行地区
        areaId: 410102, //开户行地区ID
        address: '', //详细地址
        is_def: 2
    },


    //生命周期函数--监听页面加载
    onLoad: function (options) {
        let id = options.id;
        this.getAddressInfo(id);
    },


    //获取地址详细信息
    getAddressInfo: function (id) {
        let page = this;
        let data = {
            'id': id
        }
        app.api.getshipdetail(data, function (res) {
            if(res.status){
                let region = res.data.area_name.split(" ");
                page.setData({
                    id: id,
                    name: res.data.name,
                    mobile: res.data.mobile,
                    region: region,
                    areaId: res.data.area_id,
                    address: res.data.address,
                    is_def: res.data.is_def
                });
            }else{
                wx.showModal({
                    title: '提示',
                    content: '获取收货地址信息出现问题',
                    showCancel: false
                });
            }
        });
    },


    //获取姓名
    getName: function (e) {
        let name = e.detail.value;
        this.setData({
            name: name
        });
    },


    //获取手机号
    getMobile: function (e) {
        let mobile = e.detail.value;
        this.setData({
            mobile: mobile
        });
    },


    //获取详细地址
    getAddress: function (e) {
        let address = e.detail.value;
        this.setData({
            address: address
        });
    },


    //地区选择
    regionChange: function (e) {
        let province_name = e.detail.value[0];
        let city_name = e.detail.value[1];
        let county_name = e.detail.value[2];
        let postal_code = 0;
        let page = this;
        let data = {
            province_name: province_name,
            city_name: city_name,
            county_name: county_name,
            postal_code: postal_code
        }
        let regionName = [province_name, city_name, county_name];
        page.setData({
            region: regionName
        });
        app.api.getAreaId(data, function (res) {
            if (res.status) {
                page.setData({
                    areaId: res.data
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


    //添加地址
    editAddress: function () {
        let page = this;
        let data = {
            'id': page.data.id,
            'name': page.data.name,
            'mobile': page.data.mobile,
            'area_id': page.data.areaId,
            'address': page.data.address,
            'is_def': page.data.is_def
        }

        app.db.userToken(function (token) {
            app.api.editship(data, function (res) {
                if (res.status) {
                    wx.showToast({
                        title: '保存成功',
                        icon: 'success',
                        mask: true,
                        complete: function () {
                            setTimeout(function () {
                                wx.navigateBack(1);
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
        });
    }
});