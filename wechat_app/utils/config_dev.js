//这里的配置文件除了api_url，其他值都会取后台的值，后台的值会覆盖这里的值，这里的仅作为初始的配置
module.exports = {
  api_url: 'https://b2c.jihainet.com/',
  shop_logo: 'https://b2c.jihainet.com/static/images/default.png',
  shop_name: 'jshop云商',
  image_max: 5,           //上传图片的时候，最多能够上传几张
  cate_style: 3,          //商品分类页样式，1一级大图，2一级小图，3二级小图
  payment_type: {      //支付单类型
    order: 1,    //订单
    recharge: 2, //充值
    form_order: 5, //表单付款码
    form_pay: 6 //表单订单
  }
}