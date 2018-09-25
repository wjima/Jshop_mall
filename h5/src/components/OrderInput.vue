<template>
    <yd-cell-group>
        <yd-cell-item>
            <span slot="left">买家留言：</span>
            <yd-input slot="right" v-model="input" required :show-success-icon="false" :show-error-icon="false" max="50" placeholder="50字以内（选填）"></yd-input>
        </yd-cell-item>
    </yd-cell-group>
</template>

<script>
export default {
    data () {
        return {
            input: ''
        }
    },
    watch: {
        input () {
            this.$emit('msg', this.input)
        }
    }
}
</script>

<style>
</style>
