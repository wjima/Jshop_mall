<template>
    <div class="invoice">
    	<div class="invoice-item">
    		<yd-cell-group>
                <yd-cell-item>
                    <span slot="left">抬头类型</span>
                    <span slot="right" class="radio-right">
                        <div class="radio-right-item">
                        	<input type="radio" name="checkd" id="private"/>
                            <label for="private">个人或事业单位</label>
                        </div>
                        <div class="radio-right-item">
                        	<input type="radio" name="checkd" id="companies"/>
                            <label for="companies">企业</label>
                        </div>
                    </span>
                </yd-cell-item>
                <yd-cell-item>
                    <span slot="left">发票抬头</span>
                    <span slot="right" class="input-right"><input type="text" placeholder="抬头名称"></span>
                </yd-cell-item>
                <yd-cell-item>
                    <span slot="left">税号</span>
                    <span slot="right" class="input-right"><input type="number" placeholder="纳税人识别号"></span>
                </yd-cell-item>
            </yd-cell-group>
    	</div>
    	<div class="invoice-item">
            <yd-cell-group>
                <yd-cell-item >
                    <span slot="left">发票内容</span>
                    <span slot="right">明细</span>
                </yd-cell-item>
            </yd-cell-group>
        </div>
    	<div class="invoice-item">
            <yd-cell-group>
                <yd-cell-item arrow type="link" href="#">
                    <span slot="left">本次不开具发票，继续下单</span>
                </yd-cell-item>
            </yd-cell-group>
        </div>
        <div class="footer-bottom">
        	<yd-button type="warning" >完成</yd-button>
        </div>
    </div>
</template>

<script>

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
    .footer-bottom{
        position: fixed;
        bottom: 0;
        width: 100%;
        height: .8rem;
    }
    .footer-bottom .yd-btn{
        width: 100%;
        height: 100%;
        background-color: #FF3B44;
        /*text-align: center;*/
        padding: 0;
    }
</style>