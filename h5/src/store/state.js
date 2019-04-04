export const state = {
    config: {
        shop_name: '', // 店铺名称
        shop_desc: '', // 店铺描述
        shop_logo: '', // 店铺logo
        upload_image_max: 5, // 图片上传最大限制
        store_switch: 2, // 是否开启门店 1开启 2不开启
        cate_style: 3, // 分类页样式
        cate_type: 1, // 分类页类型
        tocash_money_low: 0, // 最低提现金额
        tocash_money_rate: 0, // 提现服务费
        statistics: '', //统计代码
    },
    // 发票信息
    invoice: {
        type: 1,
        name: '',
        code: ''
    },
    wechatPayShow: false // 微信支付弹窗(仅微信H5支付成功后判断弹窗提示)
}