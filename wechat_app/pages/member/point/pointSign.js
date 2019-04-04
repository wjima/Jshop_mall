// 积分签到
const app = getApp(); //获取全局app.js

Page({
    //页面初始数据
    data: {
        days: [],
        asi: [],
        cur_year: 0,
        cur_month: 0,
        total: 0,
        isSign: true,
        continuous: 0,
        next: 0,
        rule: []
    },


    //页面加载执行
    onLoad: function (options) {
        //获取当前年月  
        const date = new Date();
        const cur_year = date.getFullYear();
        const cur_month = date.getMonth() + 1;
        const weeks_ch = ['日', '一', '二', '三', '四', '五', '六'];
        this.calculateEmptyGrids(cur_year, cur_month);
        this.calculateDays(cur_year, cur_month);
        this.setData({
            cur_year,
            cur_month,
            weeks_ch,
        });

        this.getSignInfo();
    },


    //获取签到信息
    getSignInfo: function () {
        app.api.getSignInfo(res => {
            if(res.status){
                this.setData({
                    total: res.data.total,
                    asi: res.data.asi,
                    continuous: res.data.continuous,
                    isSign: res.data.isSign,
                    next: res.data.next,
                    rule: res.data.rule
                });
                this.onJudgeSign();
            }
        });
    },


    //获取当月共多少天
    getThisMonthDays: function (year, month) {
        return new Date(year, month, 0).getDate()
    },


    //获取当月第一天星期几
    getFirstDayOfWeek: function (year, month) {
        return new Date(Date.UTC(year, month - 1, 1)).getDay();
    },


    // 计算当月1号前空了几个格子，把它填充在days数组的前面
    calculateEmptyGrids: function (year, month) {
        var that = this;
        //计算每个月时要清零
        that.setData({ days: [] });
        const firstDayOfWeek = this.getFirstDayOfWeek(year, month);
        if (firstDayOfWeek > 0) {
            for (let i = 0; i < firstDayOfWeek; i++) {
                var obj = {
                    date: null,
                    isSign: false
                }
                that.data.days.push(obj);
            }
            this.setData({
                days: that.data.days
            });
        } else {
            //清空
            this.setData({
                days: []
            });
        }
    },


    //绘制当月天数占的格子，并把它放到days数组中
    calculateDays: function (year, month) {
        var that = this;
        const thisMonthDays = this.getThisMonthDays(year, month);
        for (let i = 1; i <= thisMonthDays; i++) {
            var obj = {
                date: i,
                isSign: false
            }
            that.data.days.push(obj);
        }
        this.setData({
            days: that.data.days
        });
    },


    //匹配判断当月与当月哪些日子签到打卡
    onJudgeSign: function () {
        var that = this;
        var signs = that.data.asi;
        var daysArr = that.data.days;
        for (var i = 0; i < signs.length; i++) {
            // var current = new Date(signs[i].date.replace(/-/g, "/"));
            // var year = current.getFullYear();
            // var month = current.getMonth() + 1;
            // var day = current.getDate();
            // day = parseInt(day);
            for (var j = 0; j < daysArr.length; j++) {
                //年月日相同并且已打卡
                if (signs[i][0] == that.data.cur_year && signs[i][1] == that.data.cur_month && daysArr[j].date == signs[i][2]) {
                    daysArr[j].isSign = true;
                }
            }
        }
        that.setData({ days: daysArr });
    },


    // 切换控制年月，上一个月，下一个月
    handleCalendar: function (e) {
        const handle = e.currentTarget.dataset.handle;
        const cur_year = this.data.cur_year;
        const cur_month = this.data.cur_month;
        if (handle === 'prev') {
            let newMonth = cur_month - 1;
            let newYear = cur_year;
            if (newMonth < 1) {
                newYear = cur_year - 1;
                newMonth = 12;
            }
            this.calculateEmptyGrids(newYear, newMonth);
            this.calculateDays(newYear, newMonth);
            this.setData({
                cur_year: newYear,
                cur_month: newMonth
            });
            this.onJudgeSign();
        } else {
            let newMonth = cur_month + 1;
            let newYear = cur_year;
            if (newMonth > 12) {
                newYear = cur_year + 1;
                newMonth = 1;
            }
            this.calculateEmptyGrids(newYear, newMonth);
            this.calculateDays(newYear, newMonth);
            this.setData({
                cur_year: newYear,
                cur_month: newMonth
            });
            this.onJudgeSign();
        }
    },


    //签到
    sign: function (e) {
        var page = this;
        page.setData({
            isSign: true
        });
        app.api.sign(function (e) {
            if (e.status) {
                app.common.successToShow('签到成功，获得' + e.data + '个积分奖励', function () {
                    setTimeout(function () {
                        page.getSignInfo();
                    }, 1000);
                });
            } else {
                app.common.errorToBack(e.msg, 0);
            }
        });
    }
})
