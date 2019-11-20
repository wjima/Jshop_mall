<template>
    <view>
        <web-view :webview-styles="webviewStyles" :src="url"></web-view>
    </view>
</template>

<script>
	import {apiBaseUrl} from '@/config/config.js'
	export default {
		data() {
			return {
				webviewStyles: {
					progress: {
						color: '#FF3333'
					}
				}
			}
		},
		computed: {
			url(){
				return apiBaseUrl + 'b2c/index/kefu.html'
			}
		}
	}
</script>

<style>
</style>