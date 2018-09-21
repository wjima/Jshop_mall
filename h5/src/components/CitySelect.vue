<template>
    <div>
        <yd-cell-group>
            <yd-cell-item arrow>
                <span slot="left">所在地区：</span>
                <input slot="right" type="text" @click.stop="show2 = true" v-model="model2" readonly placeholder="请选择收货地址">
            </yd-cell-item>
        </yd-cell-group>

        <yd-cityselect
                v-model="show2"
                ref="cityselectDemo"
                :callback="result2"
                :items="district"
                provance="新疆"
                city="乌鲁木齐市"
                area="天山区"
        ></yd-cityselect>
    </div>
</template>

<script type="text/babel">
/* 前提是已经安装了 ydui-district */
import District from 'ydui-district/dist/jd_province_city_area_id';

export default {
    data() {
        return {
            show2: false,
            model2: '新疆 乌鲁木齐市 天山区',
            district: District
        }
    },
    methods: {
        result2(ret) {
            this.model2 = ret.itemName1 + ' ' + ret.itemName2 + ' ' + ret.itemName3;
        }
    }
}
</script>

<style>
</style>