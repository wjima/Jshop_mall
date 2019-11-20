# 海报生成组件

## 组件使用

> 运行下面demo时，主要图片路径

```html
<template>
    <view>
        <posters-layer
            :postersData="postersData"
            @success="onSuccessCreatePosters"
            @error="onPostersError">
        </posters-layer>
        <img :src="posterImg.path" />
    </view>
</template>
<script>
import postersLayer from '../../components/posters-layer/index';
export default {
    data() {
        return {
            postersData: {},
            posterImg: {}
        };
    },
    components: {
        postersLayer
    },
    onLoad() {
        this.initPostersConfig();
    },
    methods: {
        initPostersConfig() {
            const config = {
                clear: true,
                width: 660,
                height: 850,
                background: '#ffffff',
                views: [
                {
                    type: 'image',
                    width: 660,
                    height: 660,
                    top: 0,
                    left: 0,
                    // 封面图，测试的时候填上
                    url: 'http://127.0.0.1:8080/static/images/test/1.jpg'
                },
                {
                    type: 'text',
                    width: 400,
                    height: 50,
                    left: 20,
                    top: 680,
                    fontSize: 30,
                    lineHeight: 40,
                    bolder: true,
                    breakWord: true,
                    content: '        Apple/苹果 iPhone XR 移动联通电信全网通版 苹果xr iphonexr 苹果xr手机 iphone xr',
                    MaxLineNumber: 2
                },
                {
                    type: 'rect',
                    width: 70,
                    height: 34,
                    left: 20,
                    top: 684,
                    background: '#ff4201',
                    radius: 8
                },
                {
                    type: 'text',
                    width: 400,
                    height: 50,
                    left: 20,
                    top: 690,
                    fontSize: 24,
                    lineHeight: 40,
                    bolder: true,
                    breakWord: true,
                    content: ' 活动',
                    color: '#ffffff',
                    MaxLineNumber: 2
                },
                {
                    type: 'text',
                    width: 400,
                    left: 20,
                    top: 770,
                    fontSize: 54,
                    bolder: true,
                    breakWord: true,
                    content: '￥0.0',
                    color: '#F40',
                    MaxLineNumber: 2
                },
                {
                    type: 'image',
                    width: 140,
                    height: 140,
                    top: 680,
                    left: 500,
                    // 二维码图片路径，测试的时候填上
                    url: 'http://127.0.0.1:8080/static/images/test/qr.png'
                },
                ]
            };
            this.postersData = config;
        },
        onSuccessCreatePosters(res) {
            this.posterImg = res;
        },
        onPostersError(res) {}
    }
}
</script>

```


## 组件参数解释

### config字段


| 字段             | 类型                     | 必填 | 描述                                       |
| --------------- | ------------------------ | ---- | ------------------------------------------ |
| width           | Number(单位:px)         | 是   | 画布宽度                                   |
| height          | Number(单位:px)         | 是   | 画布高度                                   |
| background      | String                 | 否   | 画布背景颜色                                   |
| radius          | Number                  | 否   | 圆角 |
| views           | Array                  | 否   | 海报的所有元素             |

### views字段

#### 文本
| 字段             | 类型                     | 必填 | 描述 |
| --------------- | ------------------------ | ---- | ------------------------------------------ |
| type            | String                   | 是   | 类型，值：text                               | 
| width           | Number(单位:px)           | 是   | 宽度                                   | 
| height          | Number(单位:px)           | 否   | 高度                                   | 
| left            | Number(单位:px)           | 否   | 距离海报左边距                          |
| top             | Number(单位:px)           | 否   | 距离海报上边距                          |
| fontSize        | Number(单位:px)           | 否   | 字体大小，默认：16                       |
| lineHeight      | Number(单位:px)           | 否   | 行高，默认：20                           |
| breakWord       | Boolean                  | 否   | 是否自动换行，默认：false                   |
| bolder          | Boolean                  | 否   | 是否加粗，默认：false                      |
| textAlign       | String                   | 否   | 对齐方式，可选值：left、center、right，默认：left    |
| color           | String                   | 否   | 字体颜色                                  |
| content         | String                   | 是   | 文本内容                                 |
| MaxLineNumber   | Number                   | 否   | 显示多少行，超出省略                        |


### 矩形
| 字段             | 类型                     | 必填 | 描述 |
| --------------- | ------------------------ | ---- | ------------------------------------------ |
| type            | String                   | 是   | 类型，值：rect                              | 
| width           | Number(单位:px)           | 是   | 宽度                                       | 
| height          | Number(单位:px)           | 是   | 高度                                       | 
| left            | Number(单位:px)           | 否   | 距离海报左边距                               | 
| top             | Number(单位:px)           | 否   | 距离海报上边距                               | 
| radius          | Number(单位:px)           | 否   | 圆角半径，如果radius === width / 2，则是个圆，和CSS一样   | 
| background      | String                   | 否   | 填充背景色                                   | 

### 图片
| 字段             | 类型                     | 必填 | 描述 |
| --------------- | ------------------------ | ---- | ------------------------------------------ |
| type            | String                   | 是   | 类型，值：image                              | 
| tailor          | Number(单位:px)           | 否   | 裁剪方式，可选值：center                           | 
| radius          | Number(单位:px)           | 否   | 圆角半径，如果radius === width / 2，则是个圆，和CSS一样   | 
| width           | Number(单位:px)           | 是   | 宽度                               | 
| height          | Number(单位:px)           | 是   | 高度                               | 
| left            | Number(单位:px)           | 否   | 距离海报左边距                               | 
| top             | Number(单位:px)           | 否   | 距离海报上边距                               | 
| url             | String                   | 是   | 图片路径                               | 


## 事件

### `success` 海报生成成功时触发

### `error` 海报生成失败时触发