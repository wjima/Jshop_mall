<template>
	<view class="video bottom-cell-group" >
		<video :src="jdata.params.list[0].url" :poster="jdata.params.list[0].image" :autoplay="jdata.params.autoplay" controls></video>
	</view>
</template>

<script>
export default {
	name: "jshopvideo",
	props: {
		jdata:{
			type: Object,
			required: true,
		}
	},
	onLoad(){
		
	},
	methods: {
		
	}
}
</script>

<style>
.video video{
	width: 100%;
	min-height: 200upx;
}
</style>
