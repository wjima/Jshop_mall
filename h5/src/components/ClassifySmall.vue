<template>
    <div class="classify-small">
        <div class="classify-small-list">
            <div class="classify-small-item"
                 v-for="(item, index) in goodsType"
                 :key="index"
                 @click="clickHandler(item.id)"
            >
                <div class="csm-img" ref="csmimg">
                    <img v-lazy="item.image_url"/>
                </div>
                <div class="csm-p">
                    {{ item.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ClassifySmall",
    props: {
        goodsType: {
            type: Array,
            default () {
                return []
            }
        }
    },
    methods: {
        clickHandler (id) {
            this.$emit('clickHandler', {cat_id: id})
        }
    }
}
</script>

<style scoped>
.classify-small{
    width: 100%;
    padding: .2rem;
}
.classify-small-list{
    width: 100%;
    overflow: hidden;
}
.classify-small-item{
    width: 32%;
    margin-right: 2%;
    float: left;
    margin-bottom: .2rem;
}
.classify-small-item:nth-child(3n){
    margin-right: 0;
}
.csm-img{
    width: 100%;
    height: 0;
    margin-bottom: .1rem;
    position: relative;
        padding: 50% 0;
}
.csm-img img{
    width: auto !important;
    height: auto !important;
    max-width: 100%;
    max-height: 100%;
    margin: 0 auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
}
</style>