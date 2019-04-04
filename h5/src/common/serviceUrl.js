let host = ''
let apiUrl = ''

if (process.env.NODE_ENV === 'development') {
    // 开发环境
    host = 'https://b2c.jihainet.com'
    host = 'http://www.b2c.com'
    apiUrl = host + '/api.html'

    // apiUrl = 'http://wjima.ngrok.jihainet.com/api.html'
    // apiUrl = 'https://b2c.jihainet.com/api.html'
} else if (process.env.NODE_ENV === 'production') {
    // 生产环境
    host = window.host
    apiUrl = host + '/api.html'
}

export {
    host,
    apiUrl
}