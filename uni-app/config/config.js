export const apiBaseUrl = 'https://demo.jihainet.com/'
export const h5Url = apiBaseUrl + "wap/" //H5端网站地址,
// export const h5Url = "http://localhost:8080/wap/" //H5端网站地址,

// #ifdef H5
export const baseUrl=process.env.NODE_ENV === 'development'?window.location.origin+'/':apiBaseUrl
// #endif
export const paymentType = {
  //支付单类型
  order: 1, //订单
  recharge: 2, //充值
  form_order: 5, //表单付款码
  form_pay: 6 //表单订单
}

// #ifdef MP-TOUTIAO
export const ttPlatform = 'toutiao'; //toutiao=今日头条小程序, douyin=抖音小程序, pipixia=皮皮虾小程序, huoshan=火山小视频小程序
// #endif