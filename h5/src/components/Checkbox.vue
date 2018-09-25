<template>
    <div>
        <div style="padding-left:12px;margin-bottom: 10px;">
            <yd-checkbox v-model="isCheckAll" shape="circle" :change="checkAll">全选 {{isCheckAll}}</yd-checkbox>
        </div>

        <yd-checklist v-model="checklist3" ref="checklistDemo" :callback="change" :label="false">
            <yd-checklist-item val="1">
                <div style="height: 50px;line-height: 50px;">1 -- 值：{{checklist3}}</div>
            </yd-checklist-item>
            <yd-checklist-item val="2">
                <div style="height: 50px;line-height: 50px;">2</div>
            </yd-checklist-item>
            <yd-checklist-item val="3">
                <div style="height: 50px;line-height: 50px;">3</div>
            </yd-checklist-item>
        </yd-checklist>
    </div>
</template>

<script type="text/babel">
    export default {
        data() {
            return {
                checklist3: [],
                isCheckAll: false
            }
        },
        methods: {
            change(value, isCheckAll) {
                this.isCheckAll = isCheckAll;
            },
            checkAll() {
                this.isCheckAll = !this.isCheckAll
                this.$refs.checklistDemo.$emit('ydui.checklist.checkall', this.isCheckAll)
            }
        }
    }
</script>