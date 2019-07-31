<template>
  <div class="author-body">
    <div class="author-c">
      <img src="../../../static/image/loading.gif" class="loading-img" />
      <p>信息加载中,请稍后...</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      type: '',
      openid: '',
      orderId: '',
      state: ''
    }
  },
  mounted() {
    // 获取url上的参数
    this.orderId = this.getUrlParam('order_id')
    this.recharge = this.getUrlParam('recharge')
    this.type = this.getUrlParam('type')
    this.state = this.getUrlParam('state')
    this.getCode()
  },
  methods: {
    getCode() {
      var code = this.getUrlParam('code')
      code && this.getOpenId(code)
    },
    getOpenId(code) {
      let data = {
        code: code,
        scope: 2,
        state: this.state
      }
      //模拟接口
      this.$api.getOpenId(data, res => {
        console.log(res)
        if (res.status) {
          this.openid = res.data.openid
          this.toPayHandler('wechatpay')
        } else {
          this.GLOBAL.errorToShow(res.msg)
        }
      })
    },
    checkWXJSBridge(data) {
      let that = this
      let interval = setInterval(() => {
        if (typeof window.WeixinJSBridge != 'undefined') {
          clearTimeout(interval)
          that.onBridgeReady(data)
        }
      }, 200)
    },
    onBridgeReady(data) {
      var _this = this
      window.WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        {
          appId: data.appid, // 公众号名称，由商户传入
          timeStamp: data.timeStamp, // 时间戳，自1970年以来的秒数
          nonceStr: data.nonceStr, // 随机串
          package: data.package,
          signType: data.signType, // 微信签名方式：
          paySign: data.paySign // 微信签名
        },
        function(res) {
          if (res.err_msg === 'get_brand_wcpay_request:ok') {
            _this.GLOBAL.successToShow('支付成功')
          } else if (res.err_msg === 'get_brand_wcpay_request:cancel') {
            _this.GLOBAL.errorToShow('取消支付')
          } else {
            _this.GLOBAL.errorToShow('支付失败')
          }
          setTimeout(() => {
            _this.$router.replace({
              path: '/payresult',
              query: { order_id: _this.orderId }
            })
          }, 1000)
        }
      )
    },
    toPayHandler(code) {
      let data = {
        payment_code: code,
        payment_type: this.type
      }
      data['ids'] = this.type == 1 ? this.orderId : this.uid
      console.log(data)
      if (this.type == 1 && this.orderId) {
        // 微信jsapi支付
        if (this.openid) {
          data['params'] = {
            trade_type: 'JSAPI_OFFICIAL',
            openid: this.openid
          }
        }
      } else if (this.type == 2 && this.recharge) {
        if (this.openid) {
          data['params'] = {
            money: this.recharge,
            openid: this.openid
          }
        }
      }
      this.$api.pay(data, res => {
        if (res.status) {
          const data = res.data
          this.checkWXJSBridge(data)
        } else {
          this.GLOBAL.errorToShow(res.msg)
        }
      })
    },
    getUrlParam(name, url) {
      var url = url || window.location.href
      var reg = new RegExp('(^|&|/?)' + name + '=([^&|/?]*)(&|/?|$)', 'i')
      var r = url.substr(1).match(reg)
      if (r != null) {
        return r[2]
      }
      return null
    },
  }
}
</script>
<style>
.author-body {
  position: relative;
  height: 100%;
}
.author-c {
  position: absolute;
  top: 50%;
  left: 50%;
  color: #666;
  transform: translate(-50%, -50%);
}
</style>
