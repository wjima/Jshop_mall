<template>
    <div class="invoice">
    	<div class="invoice-item">
    		<yd-cell-group>
                <yd-cell-item>
                    <span slot="left">抬头类型</span>
                    <span slot="right" class="radio-right">
                        <div class="radio-right-item"
                        v-for="(item, index) in items" :key="index"
                             @click="changeHandler"
                        >
                            <span slot="left" class="invoice-type">{{ item.name }}</span>
                            <span slot="right" class="demo-list-price"><img :src="checked[0]" v-show="!item.checked"/><img :src="checked[1]" v-show="item.checked"/></span>
                        </div>
                    </span>
                </yd-cell-item>
                <yd-cell-item>
                    <span slot="left">发票抬头</span>
                    <span slot="right" class="input-right"><input type="text" v-model="name" placeholder="抬头名称"></span>
                </yd-cell-item>
                <yd-cell-item v-if="items[1].checked">
                    <span slot="left">税号</span>
                    <span slot="right" class="input-right"><input type="text" v-model="code" placeholder="纳税人识别号"></span>
                </yd-cell-item>
            </yd-cell-group>
    	</div>
    	<div class="invoice-item">
            <yd-cell-group>
                <yd-cell-item>
                    <span slot="left">发票内容</span>
                    <span slot="right">明细</span>
                </yd-cell-item>
            </yd-cell-group>
        </div>
    	<div class="invoice-item">
            <yd-cell-group>
                <yd-cell-item arrow @click.native="goBack">
                    <span slot="left" >本次不开具发票，继续下单</span>
                </yd-cell-item>
            </yd-cell-group>
        </div>
        <div class="footer-bottom">
        	<yd-button type="warning" @click.native="completeHandler">完成</yd-button>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            items: [
                {name: '个人或事业单位', value: 2, checked: false},
                {name: '企业', value: 3, checked: true}
            ],
            checked: [
                'static/image/kong.png', // 未选中
                'static/image/xuanzhong.png' // 选中
            ],
            type: 1, // 发票类型 2=个人 3=企业 1=不开发票
            name: '', // 发票抬头
            code: '' // 发票税号
        }
    },
    mounted () {
        if(this.$store.state.invoice){
            this.type = this.$store.state.invoice.type
            this.name = this.$store.state.invoice.name
            this.code = this.$store.state.invoice.code
            if(this.type === 2){
                this.items[0].checked = true
                this.items[1].checked = false
            }
        }
    },
    methods: {
        // 更改默认选中的
        changeHandler () {
            this.items.forEach(item => {
                item.checked = !item.checked
                if (item.checked) {
                    this.type = item.value
                }
            })
        },
        // 不开具发票 返回
        goBack () {
            this.$store.commit('INVOICE', {name: '', type: 1, code: ''})
            this.$router.back(-1)
        },
        // 点击确定完成后
        completeHandler () {
            if(this.type === 1){
                this.type = 3
            }
            if (this.type === 0) {
                if (!this.name) {
                    this.$dialog.toast({mes: '请填写发票抬头', timeout: 1300})
                    return false
                }
            } else if (this.type === 3) {
                if (!this.name) {
                    this.$dialog.toast({mes: '请填写发票抬头', timeout: 1300})
                    return false
                } else if (!this.code) {
                    this.$dialog.toast({mes: '请填写发票税号', timeout: 1300})
                    return false
                }
            }
            this.$store.commit('INVOICE', {name: this.name, type: this.type, code: this.code})
            this.$router.back(-1)
        }
    }
}

</script>

<style>
    .invoice-item {
        
    }
    .radio-right input{
        width: 100%;
    }
    .input-right input{
        width: 100% !important;
        text-align: right !important;
    }
    .radio-right-item{
        display: inline-block;
        position: relative;
        margin-left: 10px;
    }
    .radio-right label{
        position: relative;
    }
    .radio-right label::before {
        content: "\a0"; /*不换行空格*/
        display: inline-block;
        vertical-align: middle;
        font-size: 18px;
        width: 1em;
        height: 1em;
        margin-right: .4em;
        border-radius: 50%;
        border: 1px solid #ff3b44;
        text-indent: .15em;
        line-height: 1; 
    }
    .radio-right input[type="radio"] {
        display: inline-block;
        width: 1.5em;
        height: 1.5em;
        position: absolute;
    }
    .radio-right input:checked + label::after {
        content: '';
        width: 10px;
        height: 6px;
        position: absolute;
        top: 4px;
        left: 4px;
        border: 2px solid #ff3b44;
        border-top: none;
        border-right: none;
        transform: rotate(-45deg);
    }
    .invoice .footer-bottom{
        position: fixed;
        bottom: 0;
        width: 100%;
        height: .8rem;
		max-width: 750px;
    }
    .invoice .footer-bottom .yd-btn{
        width: 100%;
        height: 100%;
        background-color: #FF3B44;
        /*text-align: center;*/
        padding: 0;
    }
    .invoice .radio-right-item .demo-list-price{
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 0;
    }
    .invoice .radio-right-item .demo-list-price img{
        width: 20px;
        height: 20px;
    }
    .invoice-type{
        margin-left: 25px;
        line-height: 1;
    }
	.invoice .yd-cell-item:not(:last-child):after{
		border-bottom: 1px solid #e9e9e9;
	}
</style>