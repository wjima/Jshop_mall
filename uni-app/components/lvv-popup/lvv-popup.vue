<template>
	<view class="lvv-popup" v-show="popshow" @touchmove.prevent>
		<view class="lvv-popupmark" :class="position=='top'&&!hideanimation?'pt':position=='left'&&!hideanimation?'pl':position=='right'&&!hideanimation?'pr':position=='bottom'&&!hideanimation?'pc':position=='top'&&hideanimation?'ht':position=='left'&&hideanimation?'hl':position=='right'&&hideanimation?'hr':position=='bottom'&&hideanimation?'hc':''" @click="close"></view>
		<view class="lvv-popupcontent" @click="close" :class="position=='top'&&!hideanimation?'pt':position=='left'&&!hideanimation?'pl':position=='right'&&!hideanimation?'pr':position=='bottom'&&!hideanimation?'pb':position=='top'&&hideanimation?'ht':position=='left'&&hideanimation?'hl':position=='right'&&hideanimation?'hr':position=='bottom'&&hideanimation?'hb':''">
			<view class="realcontent" @click.stop="">
				<slot></slot>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		props:{
			position:{
				type:String,
				default:null 
			}
		},
		data() { 
			return { 
				popshow:false,
				hideanimation:false,
			};
		},
		methods:{
			// Toshow popup page
			show:function(){
				this.popshow = true;
			},
			// Tohide popup page
			close:function(){
				let that = this;
				this.$emit("close");
				that.hideanimation = true;
				if(that.position==null){
					that.popshow = false;
				}else{
					setTimeout(function(){
						that.popshow = false;
						that.hideanimation = false;
					},500)
				}
			}
		},
	}
</script>

<style lang="scss">
.lvv-popup{
    top:0;
    left: 0;
    width: 100%;
    height: 100%;
    position: fixed;
		z-index: 98;
    .lvv-popupmark{
      top: 0; 
      left: 0;
      width: 100%;
      height: 100%;
			z-index: 99;
      position: absolute;
      background: rgba(0,0,0,0.5);	  
    }
		.lvv-popupmark.pt,.lvv-popupmark.ht{
			background: none
		}
		.lvv-popupcontent{
			width: 100%;
			height: 100%;
			top:0;
			left:0;
			position: absolute;
			z-index: 100;
			// overflow-y:;
		}
		.pt{
			animation: showtop 0.5s;
		}
		.pl{
			animation: showleft 0.5s;
		}
		.pr{
			animation: showright 0.5s;
		}
		.pb{
			animation: showbottom .5s;
		}
		.ht{
			animation: hidetop 0.5s;
		}
		.hl{
			animation: hideleft 0.55s;
		}
		.hr{
			animation: hideright 0.55s;
		}
		.hb{
			animation: hidebottom 1s;
		}
		.pc{
			animation: showcontent .55s;
		}
		.hc{
			animation: hidecontent .55s;
		}
		
  }
	@keyframes showtop{
	  0% {
			// top: -0px;
	    transform: translateY(-100%);
			// height: 0;
	  	opacity: 1;
	  }
	  100% {
			top: 0px;
			// height: 100%;
	    transform: translateY(0%);
			opacity: 1;
	  }
	}
	@keyframes showleft{
	  0% {
	    transform: translateX(-100%);
	  	opacity: 1;
	  }
	  50% {
	  	opacity: 0;
	  }
	  100% {
	    transform: translateX(0);
	  }
	}
	@keyframes showright{
	  0% {
	    transform: translateX(100%);
	  	opacity: 1;
	  }
	  50% {
	  	opacity: 0;
	  }
	  100% {
	    transform: translateX(0);
	  }
	}
	@keyframes showbottom{
	  0% {
	    transform: translateY(100%);
			opacity: 1;
	  }
	  50% {
			opacity: 0.5;
	  }
	  100% {
	    transform: translateY(0);
	  }
	}
	@keyframes hidetop{
	  0% {
	    transform: translateY(0%);
			// height: 100%;
			opacity: 1;
	  }
	  100% {
	    transform: translateY(-100%);
			// height: 0;
	  	opacity: 1;
	  }
	}
	@keyframes hideleft{
	  0% {
	    transform: translateX(0);
	  }
	  50% {
	  	opacity: 0;
	  }
	  100% {
	    transform: translateX(-100%);
			opacity: 1;
	  }
	}
	@keyframes hideright{
	  0% {
	    transform: translateX(0);
	  }
	  50% {
	  	opacity: 0;
	  }
	  100% {
	    transform: translateX(100%);
			opacity: 1;
	  }
	}
	@keyframes hidebottom{
	  0% {
	    transform: translateY(0);
	  }
	  50% {
			opacity: 0;
	  }
	  100% {
	    transform: translateY(100%);
			opacity: 1;
	  }
	}
	
	@keyframes showcontent{
	  0% {
			opacity: 0;
	  }
	  100% {
			opacity: 1;
	  }
	}
	@keyframes hidecontent{
		  0% {
				opacity: 1;
		  }
		  100% {
				opacity: 0;
		  }
		}
</style>
