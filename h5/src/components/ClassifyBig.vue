<template>
    <div class="classify-big">
        <div class="classify-big-list">
            <div class="classify-big-item"
                 v-for="(item, index) in goodsType"
                 :key="index"
                 @click="clickHandler(item.id)"
            >
                <img v-lazy="item.image_url"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ClassifyBig",
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
.classify-big{
    width: 100%;
    padding: .2rem;
    /*height: 1rem;*/
}
.classify-big-list{
    width: 100%;
}
.classify-big-item{
    width: 100%;
    /*height: 2.5rem;*/
    margin-bottom: .2rem;
}
.classify-big-item img{
    width: 100%;
    height: 100%;
}
</style>