<template>
    <div class="count-down">
        <p class="p2" :endTime="endTime" :callback="callback" :endText="endText">
            <i>{{day}}</i>天<i>{{hours}}</i>:<i>{{minutes}}</i>:<i>{{seconds}}</i>
        </p>
    </div>
</template>
<script>
    export default {
        data(){
            return {
                day: 0,
                hours:0,
                minutes:0,
                seconds:0,
                pageTimer:{}
            }
        },
        props:{
            endTime:{
                type: [String, Number],
                default: 0
            },
            endText:{
                type: String,
                default:'已结束'
            },
            callback: {
                type: Function,
                default: () => {
                    return []
                }
            }
        },
        mounted () {
            this.countdowm(this.endTime);

        },
        watch:{
            endTime(curVal,oldVal){
                if(this.pageTimer){   //清除所有的定时器，防止传过来的值变化出现闪烁的问题
                    for(let each in this.pageTimer){
                        clearInterval(this.pageTimer[each]);
                    }
                }
                this.countdowm(curVal,oldVal);
            }
        },
        methods: {
            countdowm(timestamp,oldtime){
                let self = this;
                this.pageTimer["timer1"] = setInterval(function(){
                    let nowTime = new Date();
                    let endTime = new Date(timestamp * 1000);
                    let t = endTime.getTime() - nowTime.getTime();
                    if(t>0){
                        let day = Math.floor(t/(1000 * 60 * 60 * 24));
                        let hour= Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        let min = Math.floor((t% (1000 * 60 * 60)) / (1000 * 60));
                        let sec = Math.floor((t% (1000 * 60)) / 1000);
                        hour = hour < 10 ? '0' + hour : hour;
                        min = min < 10 ? '0' + min : min;
                        sec = sec < 10 ? '0' + sec : sec;
                        self.day = day;
                        self.hours = hour;
                        self.minutes = min;
                        self.seconds = sec;
                    }else{
                        clearInterval(self.pageTimer["timer1"]);
                        self.day = 0;
                        self.hours = 0;
                        self.minutes = 0;
                        self.seconds = 0;
                        self._callback();
                    }
                },1000);
            },
            _callback(){
                if(this.callback && this.callback instanceof Function){
                    this.callback(...this);
                }
            }
        }
    }
</script>
