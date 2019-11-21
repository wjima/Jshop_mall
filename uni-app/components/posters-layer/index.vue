<style lang="less">
.posters-layer {
  position: fixed;
  top: -5000px;
  left: -5000px;
//   top: 0;
//   left: 0;
}
</style>

<template>
    <canvas
        canvas-id="canvasdrawer"
        :style="{width: width + 'px', height: height + 'px'}"
        class="posters-layer">
    </canvas>
</template>
<script>
const CACHE_KEYS = 'temp_canvasdrawer_pic_cache';
export default {
    data() {
        return {
            width: 100,
            height: 100,
            paintingData: { views: [] },
            index: 0,
            imageList: [],
            tempFileList: [],
            isPainting: false,
            ctx: null,
            cache: {},
        }
    },
    props: {
        postersData: {
            type: Object,
            default() {
                return { views: [] };
            }
        }
    },
    watch: {
        postersData(newVal, oldVal) {
            newVal = newVal || { views: [] };
            this.createPosters(newVal);
        }
    },
    mounted() {
        uni.removeStorageSync('CACHE_KEYS');
        this.cache = uni.getStorageSync('CACHE_KEYS') || {};
        this.ctx = uni.createCanvasContext('canvasdrawer', this);
        this.createPosters(this.postersData);
    },
    methods: {
        createPosters(newVal) {
            if (!newVal.width || !newVal.height) {
                return;
            };
            // newVal = newVal || { views: [] };
            newVal.views = newVal.views || [];
            uni.removeStorageSync('CACHE_KEYS')
            this.paintingData = newVal;
            this.ctx && this.ctx.clearActions();
            if (!this.isPainting) {
                if (newVal.width && newVal.height) {
                    this.isPainting = true;
                    try {
                        this.readyPigment();
                    }
                    catch(err) {
                        this.$emit('error');
                    }
                }
            }
        },
        readyPigment() {
            const { width, height, views, background, radius = 0 } = this.paintingData;
            this.width = width;
            this.height = height;
            const inter = setInterval(() => {
                if (this.ctx) {
                    clearInterval(inter);
                    this.ctx.clearActions();
                    this.ctx.closePath();
                    // begin another path
                    this.ctx.beginPath();
                    this.drawRect({
                        //  || 'rgba(255, 255, 255, 0)'
                        background: background,
                        top: 0,
                        left: 0,
                        radius,
                        width,
                        height
                    })
                    this.getImageList(views);
                    this.downLoadImages(0);
                }
            }, 100);
        },
        getImageList(views) {
            const imageList = [];
            for (let i = 0; i < views.length; i++) {
                if (views[i].type === 'image') {
                    imageList.push(views[i].url);
                }
            }
            this.imageList = imageList;
        },
        downLoadImages(index) {
            const imageList = this.imageList;
            const tempFileList = this.tempFileList;
            if (index < imageList.length) {
                // console.log(imageList[index])
                this.getImageInfo(imageList[index]).then(imgInfo => {
                    tempFileList.push(imgInfo)
                    this.tempFileList = tempFileList;
                    this.downLoadImages(index + 1);
                })
            } else {
                this.startPainting();
            }
        },
        tailorImageDraw(view, imgInfo) {
            // _views.tailor == 'center'
            let px = 1;
            let wMultiple = (view.width * px) / imgInfo.originWidth;
            let hMultiple = (view.height * px) / imgInfo.originHeight;
            let sizeNormal = false;
            if (wMultiple <= 1 && hMultiple <= 1) {
                let multiple = wMultiple > hMultiple ? wMultiple : hMultiple;
                // let tempW = imgInfo.originWidth * multiple;
                // let tempH = imgInfo.originHeight * multiple;
                let tempW = (view.width * px) / multiple;
                let tempH = (view.height * px) / multiple;
                let sx = (imgInfo.originWidth - tempW) / 2;
                let sy = (imgInfo.originHeight - tempH) / 2;
                let ex = sx + tempW;
                let ey = sy + tempH;
                view['sx'] = sx || 0;
                view['sy'] = sy || 0;
                view['ex'] = ex || 0;
                view['ey'] = ey || 0;
                sizeNormal = true;
            }
            const data = {
                ...view,
                ow: imgInfo.originWidth,
                oh: imgInfo.originHeight
            };
            // console.log(JSON.stringify(data));
            if (!sizeNormal) {
                delete data.tailor;
            }
            // this.drawImage(data);
            return data;
        },
        async startPainting() {
            const { tempFileList, paintingData: { views } } = this;
            for (let i = 0, imageIndex = 0; i < views.length; i++) {
                if (views[i].type === 'image') {
                    let _img = tempFileList[imageIndex];
                    let _views = views[i];
                    let drawData = {};
                    if (_views.tailor) {
                        _views = this.tailorImageDraw(_views, _img);
                    }
                    drawData = {
                        ..._views,
                        url: _img.localPath
                    };
                    this.drawImage(drawData);
                    // if (_views.radius) {
                    //     this.drawRoundRect(drawData);
                    // }
                    // else {
                    // }
                    imageIndex++;
                } else if (views[i].type === 'text') {
                    if (!this.ctx.measureText) {
                        uni.showModal({
                            title: '提示',
                            content: '当前微信版本过低，无法使用 measureText 功能，请升级到最新微信版本后重试。'
                        });
                    } else {
                        this.drawText(views[i]);
                    }
                } else if (views[i].type === 'rect') {
                    this.drawRect(views[i]);
                }
                else if (views[i].type === 'round') {
                    this.drawRound(views[i]);
                }
            }
            this.ctx.draw(true, () => {
                uni.setStorageSync('CACHE_KEYS', this.cache);
                this.saveImageToLocal();
            })
        },
        old_drawImage(params) {
            // console.log(params)
            const {
                url,
                top = 0,
                left = 0,
                width = 0,
                height = 0,
                sx = 0,
                sy = 0,
                ex = 0,
                ey = 0
            } = params;
            if ('tailor' in params) {
                this.ctx.drawImage(url, sx, sy, ex, ey, left, top, width, height);
            }
            else if (params.round === true) {
                this.drawRoundImage(params);
            }
            else {
                this.ctx.drawImage(url, left, top, width, height);
            }
        },
        old_drawRoundImage(params) {
            let ctx = this.ctx;
            let x = params.left;
            let y = params.top;
            let w = params.width;
            let h = params.height;
            let url = params.url;
            let r = w / 2;
            ctx.save();
            ctx.beginPath();
            ctx.arc(x + r, y + r, r, 0, 2 * Math.PI);
            ctx.setFillStyle(params.background || '#ffffff');
            ctx.fill();
            ctx.clip();
            // 这个地方想要的是头像，简单点就放了个矩形
            ctx.drawImage(url, x, y, w, h);
            ctx.restore();
        },
         _drawRadiusRect(params) {
            const {
                top = 0,
                left = 0,
                width = 0,
                height = 0,
                radius = 0,
             } = params;
            let x = left;
            let y = top;
            let w = width;
            let h = height;
            // let bgc = background;
            let r = radius;
            // let br = radius;
            this.ctx.beginPath();
            this.ctx.moveTo(x + r, y);    // 移动到左上角的点
            this.ctx.lineTo(x + w - r, y);
            this.ctx.arc(x + w - r, y + r, r, 2 * Math.PI * (3 / 4), 2 * Math.PI * (4 / 4));
            this.ctx.lineTo(x + w, y + h - r);
            this.ctx.arc(x + w - r, y + h - r, r, 0, 2 * Math.PI * (1 / 4));
            this.ctx.lineTo(x + r, y + h);
            this.ctx.arc(x + r, y + h - r, r, 2 * Math.PI * (1 / 4), 2 * Math.PI * (2 / 4));
            this.ctx.lineTo((x), (y + r));
            this.ctx.arc(x + r, y + r, r, 2 * Math.PI * (2 / 4), 2 * Math.PI * (3 / 4));

            // this.ctx.moveTo(x + r, y);
            // this.ctx.arcTo(x + w, y, x + w, y + h, r);
            // this.ctx.arcTo(x + w, y + h, x, y + h, r);
            // this.ctx.arcTo(x, y + h, x, y, r);
            // this.ctx.arcTo(x, y, x + w, y, r);
        },
        drawImage(params) {
            const {
                type = '',
                background,
                top = 0,
                left = 0,
                width = 0,
                height = 0,
                radius = 0,
                url = '',
                sx = 0,
                sy = 0,
                ex = 0,
                ey = 0
            } = params;
            let x = left;
            let y = top;
            let w = width;
            let h = height;
            let r = radius;
            this.ctx.save();
            if (radius) {
                this.ctx.beginPath();
                

                // if (radius === parseInt(width / 2)) {
                //     console.log('圆');
                //     this.ctx.beginPath();
                //     this.ctx.arc(left + radius, top + radius, radius, 0, 2 * Math.PI);
                //     this.ctx.setFillStyle(params.background || '#ffffff')
                //     this.ctx.fill()
                //     this.ctx.clip();
                //     this.ctx.drawImage(url, x, y, w, h);
                // }
                // else {
                // }
                this._drawRadiusRect(params);
                this.ctx.fill();
                this.ctx.clip();
            }
            if ('tailor' in params) {
                this.ctx.drawImage(url, sx, sy, ex, ey, left, top, width, height);
            }
            else {
                this.ctx.drawImage(url, left, top, width, height);
            }
            // this.drawImage(params);
            
            this.ctx.restore();
        },
        old_drawRound(params) {
            let ctx = this.ctx;
            let x = params.left;
            let y = params.top;
            let w = params.width;
            let h = params.height;
            let r = params.radius;
            ctx.save();
            ctx.beginPath();
            ctx.arc(x + r, y + r, r, 0, 2 * Math.PI);
            ctx.setFillStyle(params.background || '#ffffff');
            ctx.fill();
            ctx.clip();
            ctx.restore();
        },
        drawText(params) {
            let {
                MaxLineNumber = 2,
                breakWord = false,
                color = 'black',
                content = '',
                fontSize = 16,
                top = 0,
                left = 0,
                lineHeight = 20,
                textAlign = 'left',
                width,
                bolder = false,
                textDecoration = 'none'
            } = params;
            if (bolder) {
                top -= 0.3;
            }
            // this.ctx.save();
            // this.ctx.beginPath();
            // this.ctx.stroke();
            let _setStyle = () => {
                // this.ctx.save();
                this.ctx.closePath();
                this.ctx.beginPath();
                this.ctx.setTextBaseline('top');
                this.ctx.setFillStyle(color);
                this.ctx.setFontSize(fontSize);
                this.ctx.setTextAlign(textAlign);
            }
            _setStyle();
            if (!breakWord) {
                this.ctx.fillText(content, left, top);
                this.drawTextLine(left, top, textDecoration, color, fontSize, content);
            } else {
                let fillText = '';
                let fillTop = top;
                let lineNum = 1;
                for (let i = 0; i < content.length; i++) {
                    fillText += [content[i]];
                    // _setStyle();
                    if (this.ctx.measureText(fillText).width > width) {
                        if (lineNum === MaxLineNumber) {
                            if (i !== content.length) {
                                fillText = fillText.substring(0, fillText.length - 1) + '...';
                                // _setStyle();
                                this.ctx.fillText(fillText, left, fillTop);
                                this.drawTextLine(left, fillTop, textDecoration, color, fontSize, fillText);
                                fillText = '';
                                break;
                            }
                        }
                        // _setStyle();
                        this.ctx.fillText(fillText, left, fillTop);
                        this.drawTextLine(left, fillTop, textDecoration, color, fontSize, fillText);
                        fillText = '';
                        fillTop += lineHeight;
                        lineNum++;
                    }
                }
                // _setStyle();
                this.ctx.fillText(fillText, left, fillTop);
                this.drawTextLine(left, fillTop, textDecoration, color, fontSize, fillText);
            }
            // this.ctx.draw();
            if (bolder) {
                this.drawText({
                    ...params,
                    left: left - 0.3,
                    top: top,
                    bolder: false,
                    textDecoration: 'none'
                });
            }
        },
        drawTextLine(left, top, textDecoration, color, fontSize, content) {
            if (textDecoration === 'underline') {
                this.drawRect({
                    background: color,
                    top: top + fontSize * 1.2,
                    left: left - 1,
                    width: this.ctx.measureText(content).width + 2,
                    height: 1
                });
            } else if (textDecoration === 'line-through') {
                this.drawRect({
                    background: color,
                    top: top + fontSize * 0.6,
                    left: left - 1,
                    width: this.ctx.measureText(content).width + 2,
                    height: 1
                });
            }
        },
        drawRect(params) {
            // console.log(params)
            const { background, top = 0, left = 0, width = 0, height = 0 } = params
            this.ctx.save();
            this.ctx.setFillStyle(background);
            if (params.radius) {
                this._drawRadiusRect(params);
                this.ctx.fill();
            }
            else {
                this.ctx.setFillStyle(background);
                this.ctx.fillRect(left, top, width, height);
            }
            this.ctx.restore();
        },
        getImageInfo(url) {
            return new Promise((resolve, reject) => {
                /* 获得要在画布上绘制的图片 */
                if (this.cache[url]) {
                    resolve(this.cache[url]);
                } else {
                    const objExp = new RegExp(/^http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/);
                    if (objExp.test(url)) {
                        uni.getImageInfo({
                            src: url,
                            complete: res => {
                                if (res.errMsg === 'getImageInfo:ok') {
                                    const img = {
                                        url,
                                        originWidth: res.width,
                                        originHeight: res.height,
                                        localPath: res.path
                                    };
                                    this.cache[url] = img;
                                    // console.log(res);
                                    // resolve(res.path)
                                    resolve(img);
                                } else {
                                    reject(new Error('getImageInfo fail'));
                                }
                            }
                        });
                    } else {
                        this.cache[url] = {
                            url,
                            originWidth: 0,
                            originHeight: 0,
                            localPath: url
                        };
                        resolve(url);
                    }
                }
            })
        },
        saveImageToLocal() {
            const { width, height } = this;
            uni.canvasToTempFilePath({
                x: 0,
                y: 0,
                width,
                height,
                canvasId: 'canvasdrawer',
                success: res => {
                    if (res.errMsg === 'canvasToTempFilePath:ok') {
                        this.isPainting = false;
                        this.imageList = [];
                        this.tempFileList = [];
                        this.$emit('success', {
                            width,
                            height,
                            path: res.tempFilePath
                        });
                    }
                }
            }, this);
        }
    }
}
</script>
