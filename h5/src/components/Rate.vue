<template>
    <yd-cell-group title="普通使用">
        <yd-cell-item>
            <yd-rate slot="left" v-model="rate1"></yd-rate>
            <span slot="right">值是: 0</span>
        </yd-cell-item>
        <yd-cell-item>
            <yd-rate slot="left" v-model="rate2"></yd-rate>
            <span slot="right">设置默认值</span>
        </yd-cell-item>
        <yd-cell-item>
            <yd-rate slot="left" count="7" v-model="rate3"></yd-rate>
            <span slot="right">设置多个</span>
        </yd-cell-item>
        <yd-cell-item>
            <yd-rate slot="left" v-model="rate4" color="#00FF16" active-color="#0078D7"></yd-rate>
            <span slot="right">自定义颜色</span>
        </yd-cell-item>
        <yd-cell-item>
            <yd-rate slot="left" v-model="rate5" size="18px"></yd-rate>
            <span slot="right">自定义大小</span>
        </yd-cell-item>
        <yd-cell-item>
            <yd-rate slot="left" v-model="rate6" :readonly="true"></yd-rate>
            <span slot="right">只读</span>
        </yd-cell-item>
    </yd-cell-group>
</template>

<script type="text/babel">
export default {
    data() {
        return {
            rate1: 0,
            rate2: 3,
            rate3: 2,
            rate4: 1,
            rate5: 4,
            rate6: 3
        }
    }
}
</script>