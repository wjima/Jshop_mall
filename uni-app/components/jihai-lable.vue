<template>
	<view>
		<radio-group class="uni-list" @change="radioChange">
			<label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in type_list" :key="index">
				<view class="invoice-type-icon">
					<radio class="a-radio" :id="item.name" :value="item.value" :checked="item.checked" :disabled="item.disabled"></radio>
				</view>
				<view class="invoice-type-c">
					<label class="label-2-text" :for="item.name">
						<text>{{item.name}}</text>
					</label>
				</view>
			</label>
		</radio-group>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				type_list: [
					{ value: '1', name: '仅退款', checked: true, disabled: false },
					{ value: '2', name: '退货退款', checked: false, disabled:false },
				],
			};
		},
		methods:{
			radioChange: function(evt) {
				this.type_list.forEach(item => {
					if (item.value === evt.target.value) {
						item.checked = true;
						this.aftersale_type = evt.target.value;
					}else{
						item.checked = false;
					}
				});
				if(this.type_list[0].checked){
					this.refund_input_noedit = true;
				}else{
					this.refund_input_noedit = false;
				}
			},
		}
	}
</script>

<style>

</style>
