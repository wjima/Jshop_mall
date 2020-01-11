<template>
  <view>
    <block v-for="(item,index) in jdata" :key="index">
      <jshopsearch :jdata="item" v-if="item.widget_code=='search' "></jshopsearch>
	  <jshoptabbar :jdata="item" v-if="item.widget_code=='tabBar' "></jshoptabbar>
      <jshopnotice :jdata="item" v-if="item.widget_code=='notice' "></jshopnotice>
      <jshopimgSlide :jdata="item" v-if="item.widget_code=='imgSlide' "></jshopimgSlide>
      <jshopcoupon :jdata="item" v-if="item.widget_code=='coupon' "></jshopcoupon>
      <jshopblank :jdata="item" v-if="item.widget_code=='blank' "></jshopblank>
      <jshoptextarea :jdata="item" v-if="item.widget_code=='textarea' "></jshoptextarea>
      <jshopvideo :jdata="item" v-if="item.widget_code=='video' "></jshopvideo>
      <jshopimgWindow :jdata="item" v-if="item.widget_code=='imgWindow' "></jshopimgWindow>
      <jshopimgSingle :jdata="item" v-if="item.widget_code=='imgSingle' "></jshopimgSingle>
      <jshopgoods :jdata="item" v-if="item.widget_code=='goods' "></jshopgoods>
      <jshoparticle :jdata="item" v-if="item.widget_code=='article' "></jshoparticle>
      <jshoparticleClassify :jdata="item" v-if="item.widget_code=='articleClassify' "></jshoparticleClassify>
      <jshopnavBar :jdata="item" v-if="item.widget_code=='navBar' "></jshopnavBar>
      <jshopgroupPurchase :jdata="item" v-if="item.widget_code=='groupPurchase' "></jshopgroupPurchase>
      <jshoprecord :jdata="item" v-if="item.widget_code=='record' "></jshoprecord>
	  <jshoppintuan :jdata="item" v-if="item.widget_code=='pintuan' "></jshoppintuan>
	  <jshopadpop :jdata="item" v-if="item.widget_code=='adpop' "></jshopadpop>
    </block>
  </view>
</template>

<script>
/**
 * 吉海科技jshop小程序插件集合。
 * author:novice
 * date:2019:05:20
 */
import uniCountdown from '@/components/uni-countdown/uni-countdown.vue'
import jshopimgSlide from '@/components/jshop/jshop-imgSlide.vue'
import jshopsearch from '@/components/jshop/jshop-search.vue'
import jshopnotice from '@/components/jshop/jshop-notice.vue'
import jshopcoupon from '@/components/jshop/jshop-coupon.vue'
import jshopblank from '@/components/jshop/jshop-blank.vue'
import jshoptextarea from '@/components/jshop/jshop-textarea.vue'
import jshopvideo from '@/components/jshop/jshop-video.vue'
import jshopimgWindow from '@/components/jshop/jshop-imgWindow.vue'
import jshopimgSingle from '@/components/jshop/jshop-imgSingle.vue'
import jshopgoods from '@/components/jshop/jshop-goods.vue'
import jshoparticle from '@/components/jshop/jshop-article.vue'
import jshoparticleClassify from '@/components/jshop/jshop-articleClassify.vue'
import jshopnavBar from '@/components/jshop/jshop-navBar.vue'
import jshopgroupPurchase from '@/components/jshop/jshop-groupPurchase.vue'
import jshoprecord from '@/components/jshop/jshop-record.vue'
import jshoppintuan from '@/components/jshop/jshop-pintuan.vue'
import jshoptabbar from '@/components/jshop/jshop-tabbar.vue'
import jshopadpop from '@/components/jshop/jshop-adpop.vue'

export default {
  name: 'jshop',
  components: {
    jshopimgSlide,
    jshopsearch,
    jshopnotice,
    jshopcoupon,
    jshopblank,
    jshoptextarea,
    jshopvideo,
    jshopimgWindow,
    jshopimgSingle,
    jshopgoods,
    jshoparticle,
    jshoparticleClassify,
    jshopnavBar,
    jshopgroupPurchase,
    jshoprecord,
	jshoppintuan,
	jshoptabbar,
	jshopadpop
  },
  props: {
    jdata: {
      default: function() {
        return []
      }
    }
  }
}
</script>
