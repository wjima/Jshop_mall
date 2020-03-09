<template>
	<view class="">
		<!-- #ifdef MP -->
		<image
			:lazy-load="node.attr.lazyLoad"
			:data-src="node.attr.src"
			:src="node.attr.src"
			@tap="wxParseImgTap"
			mode="widthFix"
			class="nodeclassStr"
		/>
		<!-- #endif -->
		<!-- #ifndef MP -->
		<image
			:lazy-load="node.attr.lazyLoad"
			:data-src="node.attr.src"
			:src="node.attr.src"
			@tap="wxParseImgTap"
			:mode="node.attr.mode"
			:class="node.classStr"
			:style="newStyleStr || node.styleStr"
			@load="wxParseImgLoad"
		/>
		<!-- #endif -->
		
	</view>
</template>

<script>
export default {
	name: 'wxParseImg',
	data() {
		return {
			newStyleStr: '',
			preview: true
		};
	},
	inject: ['parseWidth'],
	mounted() {},
	props: {
		node: {
			type: Object,
			default() {
				return {};
			}
		}
	},
	
	methods: {
		wxParseImgTap(e) {
			if (!this.preview) return;
			const { src } = e.currentTarget.dataset;
			if (!src) return;
			let parent = this.$parent;
			while (!parent.preview || typeof parent.preview !== 'function') {
				// TODO 遍历获取父节点执行方法
				parent = parent.$parent;
			}
			parent.preview(src, e);
		},
		// 图片视觉宽高计算函数区
		wxParseImgLoad(e) {
			const { src } = e.currentTarget.dataset;
			if (!src) return;
			let { width, height } = e.mp.detail;

			const recal = this.wxAutoImageCal(width, height);

			const { imageheight, imageWidth } = recal;
			const { padding, mode } = this.node.attr;//删除padding
			// const { mode } = this.node.attr;

			const { styleStr } = this.node;
			// console.log(styleStr)
			const imageHeightStyle = mode === 'widthFix' ? '' : `height: ${imageheight}px`;
			// console.log(imageHeightStyle)
			this.newStyleStr = `${styleStr}; ${imageHeightStyle}; width: ${imageWidth}px; padding: 0 ${+padding}px;`;//删除padding
			// this.newStyleStr = `${styleStr}; ${imageHeightStyle}; width: ${imageWidth}px;`;
			// console.log(this.newStyleStr);
		},
		// 计算视觉优先的图片宽高
		wxAutoImageCal(originalWidth, originalHeight) {
			// 获取图片的原始长宽
			const windowWidth = this.parseWidth.value;
			const results = {};

			if (originalWidth < 60 || originalHeight < 60) {
				const { src } = this.node.attr;
				let parent = this.$parent;
				while (!parent.preview || typeof parent.preview !== 'function') {
					parent = parent.$parent;
				}
				parent.removeImageUrl(src);
				this.preview = false;
			}

			// 判断按照那种方式进行缩放
			if (originalWidth > windowWidth) {
				// 在图片width大于手机屏幕width时候
				results.imageWidth = windowWidth;
				results.imageheight = windowWidth * (originalHeight / originalWidth);
			} else {
				// 否则展示原来的数据
				results.imageWidth = originalWidth;
				results.imageheight = originalHeight;
			}
			return results;
		}
	}
};
</script>
<style>
	.nodeclassStr{
		max-width: 100%;
		float:left;
		width: 750rpx; 
		padding: 0 0px;
	}
</style>