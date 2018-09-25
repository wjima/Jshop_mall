<template>
    <div class="afterservicetext">
        <yd-cell-group title="问题描述">
            <yd-cell-item>
                <yd-textarea v-model="reason" slot="right" placeholder="请您在此描述您的问题" maxlength="100"></yd-textarea>
            </yd-cell-item>
        </yd-cell-group>
    </div>
</template>

<script type="text/babel">
export default {
    data () {
        return {
            reason: ''
        }
    },
    watch: {
        reason () {
            this.$emit('reason', this.reason)
        }
    }
}
</script>

<style type="text/css">
    .afterservicetext .yd-textarea>textarea{
        font-size: .24rem;
    }
</style>
