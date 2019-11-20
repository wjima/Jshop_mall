<template>
  <view class="wrapper" v-show="redBagShow">
    <view class="modal-bg" >
    </view>
    <view class="rb-wrapper">
      <view class="rb-content" @click="handleBtn">

      </view>
      <view class="close" @click="handleClose">
        <image src='/static/image/close.png' class="img"></image>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  name: 'redBag',
  components: {},
  props: {
    
  },
  data() {
    return {
      redBagShow: true
    }
  },
  watch: {},
  computed: {},
  methods: {
    handleClose() {
      this.redBagShow=false
    },
    handleBtn() {
      this.$emit('click')
      this.redBagShow=false
    }
  },
  created() {},
  mounted() {}
}
</script>
<style lang="scss" scoped>
.modal-bg {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.4);
}
.rb-wrapper {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 60%;
  height: 600upx;
  transform: translate3d(-50%, -50%, 0);
  background: red;
  padding: 40upx;
  .rb-content{
    height: 100%;
  }
  .close {
    position: absolute;
    bottom: -120upx;
    left: 50%;
    margin-left: -30upx;
    width: 60upx;
    height: 60upx;
    border-radius: 50%;
    background: #ddd;
    .img {
      width: 100%;
      height: 100%;
    }
  }
}
</style>