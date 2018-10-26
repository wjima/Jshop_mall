<template>
    <div class="indexgrids">
        <yd-grids-group rows="4">
            <yd-grids-item link="/classify" >
                <img slot="icon" src="../../static/image/group10.png">
                <span slot="text">全部分类</span>
            </yd-grids-item>
            <yd-grids-item link="/goodslist?hot=1" >
                <img slot="icon" src="../../static/image/group11.png">
                <span slot="text">热卖商品</span>
            </yd-grids-item>
            <yd-grids-item link="/allorder" >
                <img slot="icon" src="../../static/image/group14.png">
                <span slot="text">我的订单</span>
            </yd-grids-item>
            <yd-grids-item link="/collect" >
                <img slot="icon" src="../../static/image/group15.png">
                <span slot="text">我的收藏</span>
            </yd-grids-item>
        </yd-grids-group>
    </div>
</template>

<script type="text/babel">
export default {
}
</script>
<style type="text/css">
</style>
