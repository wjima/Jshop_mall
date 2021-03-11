<template>
	<view>
		<!-- 日历头部 -->
		<view class="r-header" v-if="signData.type == 'year'">
			<view class="r-header-icon prev" v-if="indexYear > 0" @tap="nextYear(-1)">
				<view class="icon-direction left"></view>
			</view>
			<view class="title">{{year.year}}年</view>
			<view class="r-header-icon next" v-if="indexYear < (yearList.length - 1)" @tap="nextYear(1)">
				<view class="icon-direction right"></view>
			</view>
		</view>
		<view class="r-header" v-if="signData.type == 'month'">
			<view class="r-header-icon prev" v-if="indexMonth < (longList.length - 1)" @tap="nextDate(1)">
				<view class="icon-direction left"></view>
			</view>
			<view class="title">{{showList.year}}年{{showList.month}}月</view>
			<view class="r-header-icon next" v-if="indexMonth > 0" @tap="nextDate(-1)">
				<view class="icon-direction right"></view>
			</view>
		</view>
		<view class="r-header" v-if="signData.type == 'week'">
			<view class="r-header-icon prev" v-if="weekIndex > 0" @tap="nextWeekDate(-1)">
				<view class="icon-direction left"></view>
			</view>
			<view class="title">{{yeatTitle.year}}年{{yeatTitle.month}}月 第{{yeatTitle.week}}周</view>
			<view class="r-header-icon next" v-if="weekIndex < (weekList.length - 1)" @tap="nextWeekDate(1)">
				<view class="icon-direction right"></view>
			</view>
		</view>
		<!-- 日历星期 -->
		<view class="r-week" v-if="signData.type != 'year'">
			<view class="item">日</view>
			<view class="item">一</view>
			<view class="item">二</view>
			<view class="item">三</view>
			<view class="item">四</view>
			<view class="item">五</view>
			<view class="item">六</view>
		</view>
		<!-- 日历主体(年) -->
		<view class="r-content" v-if="signData.type == 'year'">
			<view class="r-content-box">
				<view class="item month-item" v-for="(item, index) in year.data" :key="index">
					<view class="text" :class="item.type == 'notday' ? 'after' : item.isSign ? 'active' : ''" @tap="monthClick(index, 'year')">{{item.month}}月
					</view>
				</view>
			</view>
		</view>
		<!-- 日历主体(月) -->
		<view class="r-content" v-if="signData.type == 'month'">
			<view class="r-content-box">
				<view class="item" v-for="(item, index) in showList.data" :key="index">
					<view class="text" :class="item.type == 'notday' ? 'after' : item.isSign ? 'active' : ''" @tap="signClick(index, 'month')">{{item.day}}
					</view>
				</view>
			</view>
		</view>
		<!-- 日历主体(周) -->
		<view class="r-content" v-if="signData.type == 'week'">
			<swiper class="week-box" :duration="300" :current="weekIndex" @change="touchendWeek">
				<swiper-item v-for="(itemp, indexp) in weekList" :key="indexp">
					<view class="swiper-item">
						<view class="r-content-week">
							<view class="item" v-for="(item, index) in itemp" :key="index">
								<view class="text" :class="item.type == 'notday' ? 'after' : item.isSign ? 'active' : ''" @tap="signClick(index, 'week')">{{item.day}}</view>
							</view>
						</view>
					</view>
				</swiper-item>
			</swiper>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			signData: {
				type: Object,
				default: () => {
					return []
				}
			},
			signList: {
				type: Object,
				default: () => {
					return []
				}
			}
		},
		data() {
			return {
				indexMonth: 0, // 当前显示月的index
				longList: [], // 从signData.start 到当前的年份月份
				nowList: {}, // 当前月日期数据
				showList: {}, // 显示月日期数据
				today: parseInt(new Date().getDate()), //本日
				toMonth: parseInt(new Date().getMonth() + 1), //本月
				toYear: parseInt(new Date().getFullYear()), //本年

				// 周单位滑动
				weekIndex: 0, // 当前周index
				weekList: [], // 周的列表
				yeatDay: [], // title信息
				yeatTitle: {},
				nextIndex: 0, // 记录已加载签到数据的周
				yearList: [],
				indexYear: 0,
				year: {}
			}
		},
		mounted() {
			this.getMonth()
			this.getYear()
		},
		methods: {
			getYear() {
				let _s = this.signData.start || '2019-08-01'
				let [_y, _m, _d] = _s.split('-')
				for (let k = Number(_y); k <= this.toYear; k++) {
					this.yearList.push({
						year: k,
						data: []
					})
				}
				for (let j = 0; j < this.yearList.length; j++) {
					let _dayData = []
					if (j == 0 && this.yearList[j].year != this.toYear) {
						// 大于开始时间
						for (let i = 1; i < 13; i++) {
							if (i >= _m) {
								_dayData.push({
									month: i,
									type: 'month',
									year: this.yearList[j].year,
									isSign: false
								})
							} else {
								_dayData.push({
									month: i,
									type: 'notday',
									year: this.yearList[j].year,
									isSign: false
								})
							}
						}
					} else if (this.yearList[j].year == this.toYear) {
						if (j == 0) {
							for (let i = 1; i < 13; i++) {
								if (i > this.toMonth || i < _m) {
									_dayData.push({
										month: i,
										type: 'notday',
										year: this.yearList[j].year,
										isSign: false
									})
								} else {
									_dayData.push({
										month: i,
										type: 'month',
										year: this.yearList[j].year,
										isSign: false
									})
								}
							}
						} else {
							for (let i = 1; i < 13; i++) {
								if (i > this.toMonth) {
									_dayData.push({
										month: i,
										type: 'notday',
										year: this.yearList[j].year,
										isSign: false
									})
								} else {
									_dayData.push({
										month: i,
										type: 'month',
										year: this.yearList[j].year,
										isSign: false
									})
								}
							}
						}
					} else {
						for (let i = 1; i < 13; i++) {
							_dayData.push({
								month: i,
								type: 'month',
								year: this.yearList[j].year,
								isSign: false
							})
						}
					}
					this.yearList[j].data = _dayData;
					// console.log(this.yearList[j]);
				}
				this.indexYear = this.yearList.length - 1;
				this.year = this.yearList[this.indexYear];
			},
			// 上下年份切换（上一年indexYear-1，下一年indexMonth+1）
			nextYear(index) {
				this.indexYear += index;
				if (this.indexYear < 0) {
					this.indexYear = 0
				}
				if (this.indexYear > this.yearList.length - 1) {
					this.indexYear = this.yearList.length - 1
				}
				this.year = this.yearList[this.indexYear];
				this.$emit('change', {
					type: "year",
					data: this.year
				})
			},
			monthClick(index, type) {
				if (type == 'year') {
					this.year.data.forEach((item, i) => {
						if (i != index) {
							item.isSign = false
						}
					})
					let days = this.year.data[index];
					if (!days.isSign) {
						if (days.type == 'notday') {
							days.isSign = false
						} else {
							days.isSign = true
							this.$set(this.year.data, index, days)
							this.$emit('showCount', {
								type: "click",
								data: days
							})
						}
					}
				}
			},
			// 获取longList数据
			getMonth() {
				// 获取start到当前月共多少月
				let _s = this.signData.start || '2019-08-01'
				let [_y, _m, _d] = _s.split('-')
				let _sy = this.toYear - _y,
					_sm = 0
				if (_sy > 0) { // 跨年
					if (_sy == 1) {
						_sm = 12 - _m + this.toMonth
					} else {
						_sm = (_sy - 1) * 12 + (12 - _m) + this.toMonth
					}
				} else {
					_sm = this.toMonth - _m
				}
				let y_index = this.toYear,
					m_index = this.toMonth
				// 遍历添加数组
				for (let i = 0; i <= _sm; i++) {
					let _month = m_index - i,
						_year = this.toYear
					for (let _i = 0; _i < _sy; _i++) {
						if (_month <= 0) {
							_month += 12
							_year -= 1
						}
					}
					this.longList.push({
						year: _year,
						month: _month,
						data: []
					})
					// 获取每个月的天数
					// let _day = new Date(this.toYear, this.toMonth, 0), _daycount = _day.getDate()
				}
				console.log("longList", this.longList);
				this.getDay()
			},
			// 获取每个月天数
			getDay() {
				let _s = this.signData.start || '2019-08-01'
				let [_y, _m, _d] = _s.split('-')
				let _sy = this.toYear - _y //当前月份
				for (let i = 0, _i = this.longList.length; i < _i; i++) {
					let _data = this.longList[i]
					let _day = new Date(_data.year, _data.month, 0),
						_daycount = _day.getDate()
					let _dayData = []
					// 当月一号前空格
					for (let j = 1; j <= _daycount; j++) { // 遍历每个月天数
						// 当前时间之后的不可点击
						if (_data.year == this.toYear && _data.month == this.toMonth && j > this.today) {
							_dayData.push({
								year: _data.year,
								month: _data.month,
								day: j,
								type: 'notday'
							})
						} else if (_data.year == Number(_y) && _data.month == Number(_m) && j < Number(_d)) {
							// 开始时间之前的不可点击
							_dayData.push({
								year: _data.year,
								month: _data.month,
								day: j,
								type: 'notday'
							})
						} else {
							_dayData.push({
								year: _data.year,
								month: _data.month,
								day: j,
								type: 'day'
							})
						}

					}
					this.longList[i].data = this.getDayEnd(_data.year, _data.month, _daycount, _dayData)
				}
				// 渲染当前显示页
				if (this.signData.type == 'month') {
					this.getNowData()
				}
				if (this.signData.type == 'week') {
					this.getNowWeek()
				}
			},
			/* 
			  补全当前月开头结尾
			  @param {Number} year 
			  @param {Number} month 
			  @param {Number} daycount 当前月天数
			*/
			getDayEnd(year, month, daycount, data) {
				// 获取当前月第一天和最后一天是星期几
				let first = this.getFirstDayOfWeek(year, month, 1),
					last = this.getFirstDayOfWeek(year, month, daycount)
				// 获取上个月最后一天
				let _month = month - 1 < 1 ? 12 : month - 1,
					_year = month - 1 < 1 ? year - 1 : year,
					month_ = month + 1 > 12 ? 1 : month + 1,
					year_ = month + 1 > 12 ? year + 1 : year
				let _prev = new Date(_year, _month, 0).getDate() // 上一月多少天
				// 处理数据
				for (let i = 0; i < first; i++) {
					data.unshift({
						year: _year,
						month: _month,
						day: _prev - i,
						type: 'notday'
					})
				}
				if (7 - last > 1) {
					for (let i = 1; i < 7 - last; i++) {
						data.push({
							year: year_,
							month: month_,
							day: i,
							type: 'notday'
						})
					}
				}
				return data
			},
			// 获取当前月日历
			getNowData() {
				this.longList.map((item, index) => {
					if (item.year == this.toYear) {
						if (item.month == this.toMonth) {
							this.nowList = item
							this.showList = item
						}
					}
				})
				this.signViewShow()
			},
			// 上下月份切换（上一月indexMonth+1，下一月indexMonth-1）
			nextDate(index) {
				this.indexMonth += index
				if (this.indexMonth < 0) {
					this.indexMonth = 0
				}
				if (this.indexMonth > this.longList.length - 1) {
					this.indexMonth = this.longList.length - 1
				}
				this.showList = this.longList[this.indexMonth]
				// this.signViewShow()
				this.$emit('change', {
					type: "month",
					data: this.showList
				})
			},
			// 获取当月第一天星期几
			getFirstDayOfWeek(year, month, day) {
				return new Date(Date.UTC(year, month - 1, day)).getDay()
			},
			// 加载已签到信息
			signViewShow() {
				let days = this.showList.data,
					dayArr = this.signList.list,
					day = this.year.data
				console.log("signList", this.signList);
				if (this.signList.type == "month") {
					for (let i = 0, _i = days.length; i < _i; i++) {
						for (let j = 0, _j = dayArr.length; j < _j; j++) {
							if (dayArr[j].year == days[i].year && dayArr[j].month == days[i].month && dayArr[j].day == days[i].day) {
								days[i].isSign = true
							}else{
								days[i].isSign = false
							}
						}
					}
					this.showList.data = days
				} else {
					for (let i = 0, _i = day.length; i < _i; i++) {
						for (let j = 0, _j = dayArr.length; j < _j; j++) {
							if (dayArr[j].year == day[i].year && dayArr[j].month == day[i].month) {
								day[i].isSign = true
							}else{
								day[i].isSign = false
							}
						}
					}
					this.year.data = day
				}

			},
			// // 签到操作
			// signClick(index, type) {
			//   let days = {}
			//   if(type == 'week') {
			//     days = this.weekList[this.weekIndex][index]
			//   }
			//   if(type == 'month') {
			//     days = this.showList.data[index]
			//   }
			//   let _y = days.year, _m = days.month, _d = days.day
			//   let click_data = _y + '-' + _m + '-' + _d, now_data = this.toYear + '-' + this.toMonth + '-' + this.today
			//   let _n = new Date(click_data), _z = new Date(now_data)
			//   let __n = _n.getTime(_n), __z = _z.getTime(_z)
			//   if(__z >= __n) {
			//     if(__z == __n) { // 当天签到
			//       if(!days.isSign) {
			//         days.isSign = true
			//         if(type == 'month') {
			//           this.$set(this.showList.data, index, days)
			//         }
			//         if(type == 'week') {
			//           this.$set(this.weekList[this.weekIndex], index, days)
			//         }
			// 	this.$emit('showCount', {
			// 		type: true,
			// 		data: days
			// 	})
			//       }
			//     }else { // 补签
			//       if(!this.signData.ismake) {return false}
			//       if(days.type == 'notday') {return false}
			//       if(!days.isSign) {
			//         days.isSign = true
			//         if(type == 'month') {
			//           this.$set(this.showList.data, index, days)
			//         }
			//         if(type == 'week') {
			//           this.$set(this.weekList[this.weekIndex], index, days)
			//         }
			// 	this.$emit('showCount', {
			// 		type: false,
			// 		data: days
			// 	})
			//       }
			//     }
			//   }
			// },
			// 点击操作
			signClick(index, type) {
				let days = {}
				if (type == 'week') {
					days = this.weekList[this.weekIndex][index]
					this.weekList[this.weekIndex].forEach((item, i) => {
						if (i != index) {
							item.isSign = false
						}
					})
				}
				if (type == 'month') {
					days = this.showList.data[index]
					this.showList.data.forEach((item, j) => {
						if (j != index) {
							item.isSign = false
						}
					})
					// 除点击日期之外全部都改状态
					this.longList.forEach((item) => {
						if (item.year == days.year && item.month == days.month) {
							item.data.forEach(today => {
								if (today.day != days.day) {
									today.isSign = false
								}
							})
						} else {
							item.data.forEach(today => {
								today.isSign = false
							})
						}
					})
				}
				let _y = days.year,
					_m = days.month,
					_d = days.day
				let click_data = _y + '-' + _m + '-' + _d,
					now_data = this.toYear + '-' + this.toMonth + '-' + this.today
				let _n = new Date(click_data),
					_z = new Date(now_data)
				let __n = _n.getTime(_n),
					__z = _z.getTime(_z)
				if (__z >= __n) {
					if (!days.isSign && days.type != "notday") {
						days.isSign = true
						if (type == 'month') {
							this.$set(this.showList.data, index, days)
						}
						if (type == 'week') {
							this.$set(this.weekList[this.weekIndex], index, days)
						}
						this.$emit('showCount', {
							type: 'click',
							data: days
						})
					}
				}
			},
			touchendWeek(e) {
				this.weekIndex = e.detail.current
				this.yeatTitle = this.yeatDay[this.weekIndex]
				if (this.nextIndex > this.weekIndex) {
					this.weekList = this.signViewShowWeek(this.weekList)
					this.nextIndex = this.weekIndex
				}
			},
			// 获取当前周的信息
			getNowWeek() {
				// month数据已做过处理，若当月一号不为周日，则往前面补位('', '', '', '1', '2')
				let _list = this.longList,
					_ = [],
					_yeatDay = []
				_list.reverse()
				_list.map((item, index) => {
					let num = Math.ceil(item.data.length / 7)
					// 在当前月只保留到当前周
					// 获取当前天是当前月第几周
					let __n = this.getMonthWeek(this.toYear, this.toMonth, this.today)
					if (index == _list.length - 1) {
						num = __n
					}
					for (let i = 0; i < num; i++) {
						_.push(item.data.slice(i * 7, i * 7 + 7))
						_yeatDay.push({
							year: item.year,
							month: item.month,
							week: i + 1
						})
					}
				})
				this.yeatDay = _yeatDay
				this.weekIndex = _.length - 1
				this.nextIndex = this.weekIndex
				this.weekList = this.signViewShowWeek(_)
				this.yeatTitle = this.yeatDay[this.weekIndex]
			},
			getMonthWeek(a, b, c) {
				let _k = new Date(a, b - 1, c),
					w = _k.getDay(),
					d = _k.getDate()
				if (w == 0) {
					w = 7
				}
				return Math.ceil((d + 6 - w) / 7)
			},
			// 二维数组换一维数组
			reduceWeek(arr) {
				let _arr = arr.reduce((a, b) => {
					return a.concat(b)
				})
				return _arr
			},
			// 加载已签到信息
			signViewShowWeek(data) {
				let days = data[this.weekIndex],
					dayArr = this.signList
				for (let i = 0, _i = days.length; i < _i; i++) {
					for (let j = 0, _j = dayArr.length; j < _j; j++) {
						if (dayArr[j].year == days[i].year && dayArr[j].month == days[i].month && dayArr[j].day == days[i].day) {
							days[i].isSign = true
						}
					}
				}
				data[this.weekIndex] = days
				return data
			},
			nextWeekDate(index) {
				this.weekIndex += index
				if (this.weekIndex < 0) {
					this.weekIndex = 0
					return false
				}
				if (this.weekIndex > this.weekList.length - 1) {
					this.weekIndex = this.weekList.length - 1
					return false
				}
				this.weekList = this.signViewShowWeek(this.weekList)
				this.yeatTitle = this.yeatDay[this.weekIndex]
			}
		}
	}
</script>

<style lang="scss">
	.r-header {
		background-color: #fff;
		color: #000;
		position: relative;

		.title {
			height: 88upx;
			line-height: 88upx;
			font-size: 32rpx;
			font-weight: 700;
			text-align: center;
		}
	}

	.r-header::before,
	.r-header::after,
	.r-content::after {
		position: absolute;
		left: 0;
		right: 0;
		height: 1px;
		content: '';
		background-color: #f2f2f2;
		transform: scaleY(.6);
	}

	.r-header::before {
		top: 0;
	}

	.r-header::after,
	.r-content::after {
		bottom: 0;
	}

	.r-header-icon {
		position: absolute;
		top: 0;
		bottom: 0;
		width: 88upx;
		display: flex;
		justify-content: center;
		align-items: center;
		color: #666;
		font-size: 26upx;
	}

	.r-header-icon.prev {
		left: 0;
	}

	.r-header-icon.next {
		right: 0;
	}

	.r-week {
		padding: 0 20upx;
		height: 88upx;
		display: flex;
		align-items: center;
		font-size: 24upx;
		color: #999;
		background-color: #fff;

		.item {
			flex: 1;
			text-align: center;
		}
	}

	.r-content {
		padding: 0 20upx 10upx;
		position: relative;
		background-color: #fff;
	}

	.r-content-box {
		display: flex;
		flex-wrap: wrap;

		.item {
			box-sizing: border-box;
			width: 14.285%;
			padding-bottom: 20upx;
		}

		.text {
			box-sizing: border-box;
			width: 60upx;
			height: 60upx;
			text-align: center;
			font-size: 22upx;
			line-height: 60upx;
			color: #999;
			border: 1upx solid #999;
			border-radius: 50%;
			margin: 0 auto;
		}

		// 选中样式
		.text.active {
			color: #64C9FD;
			border-color: #64C9FD;
		}

		// 漏签样式
		.text.not {
			color: #fff;
			border-color: #e4e4e4;
			background-color: #e4e4e4;
		}

		// 补全样式
		.text.after {
			color: #eee;
			border-color: #f2f2f2;
		}
	}

	.icon-direction {
		border: 20upx solid transparent;
	}

	.icon-direction.left {
		border-right-color: #666;
		transform: scale(1.1, .6);
	}

	.icon-direction.right {
		border-left-color: #666;
		transform: scale(1.1, .6);
	}

	.week-box {
		white-space: nowrap;
		height: 70upx;
	}

	.r-content-week {
		width: 100%;
		display: inline-block;
		vertical-align: top;
		white-space: nowrap;

		.item {
			display: inline-block;
			vertical-align: top;
			box-sizing: border-box;
			width: 14.285%;
			padding-bottom: 20upx;
		}

		.text {
			box-sizing: border-box;
			width: 60upx;
			height: 60upx;
			text-align: center;
			font-size: 22upx;
			line-height: 60upx;
			color: #999;
			border: 1upx solid #999;
			border-radius: 50%;
			margin: 0 auto;
		}

		// 选中样式
		.text.active {
			color: #4CD964;
			border-color: #4CD964;
		}

		// 漏签样式
		.text.not {
			color: #fff;
			border-color: #e4e4e4;
			background-color: #e4e4e4;
		}

		// 补全样式
		.text.after {
			color: #eee;
			border-color: #f2f2f2;
		}
	}

	.month-item {
		width: 25% !important;
		padding: 20rpx;
	}
</style>
