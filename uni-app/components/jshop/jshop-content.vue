<template>
	<view class="content">
		<u-parse :content="content" @preview="preview" @navigate="navigate" style="background-color: #fff;"/>
	</view>
</template>
<script>
	//视频和文本解析组件
	import uParse from '@/components/gaoyia-parse/parse.vue'
	export default {
		name: 'jshop-content',
		components: {
			uParse
		},
		props: {
			content: {}
		},
		created() {},
		methods: {
			preview(src, e) {
				// do something
			},
			navigate(href, e) {
				// do something
			}
		}
	}
</script>
