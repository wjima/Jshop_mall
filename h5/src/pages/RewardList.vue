<template>
    <div class="rewardlist">
        <ul>
            <li class="rewarddetail">
                <img class="user-img" src="../../static/image/headerimg.jpg"/>
                <div class="rewardcontent">
                    <div class="rewardcontent-left">
                        <h4>来自王海涛的奖励金</h4>
                        <p>[奖励金]</p>
                        <span>今天 11:20</span>
                    </div>
                    <div class="rewardcontent-right">
                        <h2>5.30</h2>
                    </div>
                </div>
            </li>
            <li class="rewarddetail">
                <img class="user-img" src="../../static/image/headerimg.jpg"/>
                <div class="rewardcontent">
                    <div class="rewardcontent-left">
                        <h4>来自王吉马的奖励金</h4>
                        <p>[奖励金]</p>
                        <span>昨天 18:35</span>
                    </div>
                    <div class="rewardcontent-right">
                        <h2>3.82</h2>
                    </div>
                </div>
            </li>
            <li class="rewarddetail">
                <img class="user-img" src="../../static/image/headerimg.jpg"/>
                <div class="rewardcontent">
                    <div class="rewardcontent-left">
                        <h4>来自潘昊的奖励金</h4>
                        <p>[奖励金]</p>
                        <span>06-19 11:53</span>
                    </div>
                <div class="rewardcontent-right">
                    <h2>11.40</h2>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
</script>

<style>
    .rewardlist{
        height: 100%;
        background: #fff;
    }
    .rewarddetail{
        text-align: left;
        padding: .3rem .2rem 0;
        width: 100%;
        box-sizing: border-box;
        position: relative;
    }
    .rewarddetail .user-img{
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
    .rewardcontent{
        display: inline-block;
        border-bottom: 1px solid #F2F2F2;
        padding-bottom: .3rem;
        overflow: hidden;
        width: calc(100% - 1.3rem);
        margin-left: 1.2rem;
    }
    .rewardcontent-left{
        display: inline-block;
    }
    .rewardcontent-left h4{
        margin-bottom: .1rem;
    }
    .rewardcontent-left p{
        color: #666;
        margin-bottom: .15rem;
    }
    .rewardcontent-left span{
        color: #999;
    }
    .rewardcontent .rewardcontent-right{
        color: #FF3B44;
        font-size: .6rem;
        display: inline-block;
        float: right;
    }
</style>
