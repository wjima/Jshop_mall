<template>
    <div class="afterserviceimg">
        <div class="uploadimg-list"  v-if="imgs.length">
            <div v-for="(item, index) in imgs" :key="index">
                <yd-badge @click.native="remove(index)">X</yd-badge>
                <img class="thumbnail-list" :src="item.url">
            </div>
        </div>
        <div class="uploadimg">
            <input name="file" type="file" accept="image/png,image/gif,image/jpeg" @change="uploadImg"/>
            <img slot="icon" src="../../static/image/addimg.png" v-show="upload">
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            imgs: [],
            upload: true,
            imgData: {
                accept: 'image/gif, image/jpeg, image/png, image/jpg'
            }
        }
    },
    methods: {
        uploadImg (e) {
            let file = e.target.files[0]
            let param = new FormData() // 创建form对象
            param.append('upfile', file, file.name) // 通过append向form对象添加数据
            this.$api.uploadFile('image', param, res => {
                if (res.status) {
                    this.imgs.push(res.data)
                }
            })
        },
        remove (index) {
            this.imgs.splice(index, 1)
        }
    },
    watch: {
        imgs () {
            this.$emit('images', this.imgs)
            if (this.imgs.length >= 4) {
                this.upload = false
            } else {
                this.upload = true
            }
        }
    }
}
</script>
