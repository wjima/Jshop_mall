<template>
	<view class="clearfix">
		<view class="textarea bottom-cell-group" >
			<jshopContent :content="jdata.params" v-if="jdata.params"></jshopContent>
		</view>
	</view>
</template>

<script>
import htmlParser from '@/common/html-parser'
import jshopContent from '@/components/jshop/jshop-content.vue'//视频和文本解析组件

export default {
	components: {
		jshopContent
	},
	name: "jshoptextarea",
	props: {
		jdata:{
			// type: Object,
			required: true,
		}
	},
	created() {
		//this.jdata.params = htmlParser(this.jdata.params)
	},
	onLoad() {
		
	},
	methods: {
		
	}
}
</script>

<style>
.textarea{
	width: 100%;
	background-color: #fff;
	padding: 10upx 26upx;
	/* height: 40upx; */
}
.textarea p img{
	width: 100% !important;
}
.textarea div{
	background-color: #000;
}
.textarea p {
	background-color: #000;
}
.clearfix:after {
  content: ".";
  display: block;
  height: 0;
  clear: both;
  visibility: hidden;
}
</style>
