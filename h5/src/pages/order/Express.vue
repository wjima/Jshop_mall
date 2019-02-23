<template>
    <div class="express-info">
        <div class="express-num"></div>
        <yd-timeline>
            <yd-timeline-item v-for="(item, index) in logisticsInfo.data" :key="index">
                <p>{{ item.context }}</p>
                <p style="margin-top: 10px;">{{ item.time }}</p>
            </yd-timeline-item>
        </yd-timeline>
    </div>
</template>

<script>
export default {
    
}
</script>

<style>
</style>