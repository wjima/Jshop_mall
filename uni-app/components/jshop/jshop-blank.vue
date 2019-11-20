<template>
	<view class="blank" :style="{background:jdata.params.backgroundColor,height:jdata.params.height*2+'rpx'}"></view>
</template>

<script>
export default {
	name: "jshopblank",
	props: {
		jdata:{
			// type: Array,
			required: true,
		}
	},
	methods: {
		
	}
}
</script>

<style>
</style>
