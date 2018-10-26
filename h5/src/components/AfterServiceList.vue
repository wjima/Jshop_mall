<template>
    <div class="afterservicelist">
        <yd-checklist v-model="checked" :label="true" color="#ff3b44">
            <yd-checklist-item :val="item.id" :disabled="!delivery" v-for="(item, index) in goods" :key="index">
                <yd-flexbox style="padding: .2rem 0;">
                    <img :src="item.image_url" class="goodsimg">
                    <yd-flexbox-item align="top">
                        <h3 class="goodsname">{{ item.name }}</h3>
                        <p class="standard">{{ item.addon }}</p>
                    </yd-flexbox-item>
                </yd-flexbox>
            </yd-checklist-item>
        </yd-checklist>
    </div>
</template>

<script>
export default {
    props: {
        goods: {
            type: [Array, Object],
            default () {
                return []
            }
        },
        delivery: {
            type: Boolean,
            default () {
                return false
            }
        }
    },
    computed: {
        checked: {
            get () {
                let check = []
                for (let i in this.goods) {
                    check.push(this.goods[i].id)
                }
                return check
            },
            set (check) {
                this.$emit('checked', check)
            }
        }
    }

}
</script>
