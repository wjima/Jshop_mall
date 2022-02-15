<script>
export default {
	onLaunch() {
		uni.setNavigationBarColor({
			frontColor: '#ffffff',
			backgroundColor: '#1AAD19'
		})

		// #ifdef APP-PLUS || APP-PLUS-NVUE
		this.checkVersion()
		// #endif
	},
	onShow: function() {
		//console.log('App Show')
	},
	onHide: function() {
		//console.log('App Hide')
	},
	methods: {
		// #ifdef APP-PLUS || APP-PLUS-NVUE
		// app更新检测
		checkVersion() {
			// 获取应用版本号
			let version = plus.runtime.version;

			//检测当前平台，如果是安卓则启动安卓更新
			uni.getSystemInfo({
				success: res => {
					this.updateHandler(res.platform, version);
				}
			})
		},
		// 更新操作
		updateHandler(platform, version) {
			let data = {
				platform: platform,
				version: version
			}
			let _this = this;
			this.$api.getAppVersion(data,
				res => {
					if (res.status && res.data[0].version) {
						const info = res.data[0];
						if (info.version !== '' && info.version > version) {
							uni.showModal({
								//提醒用户更新
								title: '更新提示',
								content: info.note,
								success: res => {
									if (res.confirm) {
										plus.runtime.openURL(info.download_url)
									}
								}
							})
						}
					}
				}
			)
		}
		// #endif
	}
}
</script>

<style lang="scss">
/*每个页面公共css */
@import './static/css/style.css';

.bgf {
	background: #fff;
}

.flc {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.flc-inline {
	display: inline-flex;
	align-items: center;
}

.g5 {
	color: $g5;
}

.fz12 {
	font-size: $fz12;
}
</style>
<style>
@import url('/components/gaoyia-parse/parse.css');
</style>
