## 配置
### 服务器地址
config/config.js中
```
export const apiBaseUrl = 'http://www.b2c.com/';//注意最后斜杠,填写你的域名地址
```

### 海报H5中保存图片跨域
nginx中添加以下配置
```
    location ~ .*\.(gif|jpg|jpeg|png)$ {  
      add_header Access-Control-Allow-Origin *;
      add_header Access-Control-Allow-Headers X-Requested-With;
      add_header Access-Control-Allow-Methods GET,POST,OPTIONS;
    }
```

### 函数差异说明
switchTab
微信小程序有success事件，支付宝小程序暂无此事件

### APP升级说明
manifest.json 文件源码中的 versionName（应用版本名称） 设置的就是当前APP的版本号
后台APP更新插件设置的版本号大于 versionName（应用版本名称）时，APP打开时会提示更新APP
