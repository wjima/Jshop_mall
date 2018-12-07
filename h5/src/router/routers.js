/**
 *      path         页面路由地址
 *      navShow      是否显示页面头部
 *      tabShow      是否显示页面底部导航
 *      title        页面标题
 *      keepAlive    是否缓存页面
 *      @type {*[]}
 */
const routers = [
    {
        path: '/',
        redirect: '/index'
    },
    {
        path: '/authbind',
        component: resolve => require(['../pages/AuthBind'], resolve),
        name: 'AuthBind',
        meta: {
            navShow: false,
            tabShow: true,
            title: '用户绑定',
            keepAlive: false
        }
    },
    {
        path: '/author',
        component: resolve => require(['../pages/Author'], resolve),
        name: 'author',
        meta: {
            navShow: false,
            tabShow: false,
            keepAlive: false
        }
    },
    {
        path: '/index',
        component: resolve => require(['../pages/Index'], resolve),
        name: 'Index',
        meta: {
            navShow: false,
            tabShow: true,
            keepAlive: true
        }
    },
    {
        path: '/classify',
        component: resolve => require(['../pages/Classify'], resolve),
        name: 'Classify',
        meta: {
            navShow: true,
            tabShow: true,
            title: '全部分类',
            keepAlive: true
        }
    },
    {
        path: '/cart',
        component: resolve => require(['../pages/cart/Cart'], resolve),
        name: 'Cart',
        meta: {
            navShow: true,
            tabShow: false,
            title: '购物车',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/user',
        component: resolve => require(['../pages/User'], resolve),
        name: 'user',
        meta: {
            navShow: false,
            tabShow: true,
            title: '个人中心',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/login',
        component: resolve => require(['../pages/login/Login'], resolve),
        name: 'Login',
        meta: {
            navShow: true,
            tabShow: false,
            title: '用户登录',
            keepAlive: false
        }
    },
    {
        path: '/goodslist',
        component: resolve => require(['../pages/goods/GoodsList'], resolve),
        name: 'GoodsList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '商品列表',
            keepAlive: false
        }
    },
    {
        path: '/goodsdetail',
        component: resolve => require(['../pages/goods/GoodsDetail'], resolve),
        name: 'GoodsDetail',
        meta: {
            navShow: false,
            tabShow: false,
            title: '商品详情',
            keepAlive: false
        }
    },
    {
        path: '/firmorder',
        component: resolve => require(['../pages/order/FirmOrder'], resolve),
        name: 'FirmOrder',
        meta: {
            tabShow: false,
            navShow: true,
            title: '订单确认',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/cashierdesk',
        component: resolve => require(['../pages/order/CashierDesk'], resolve),
        name: 'CashierDesk',
        meta: {
            tabShow: false,
            title: '订单支付',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/allorder',
        component: resolve => require(['../pages/order/AllOrder'], resolve),
        name: 'AllOrder',
        meta: {
            navShow: true,
            tabShow: false,
            title: '订单列表',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/orderdetail',
        component: resolve => require(['../pages/order/OrderDetail'], resolve),
        name: 'OrderDetail',
        meta: {
            tabShow: false,
            title: '订单详情',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/afterservice',
        component: resolve => require(['../pages/order/AfterService'], resolve),
        name: 'AfterService',
        meta: {
            tabShow: false,
            title: '申请售后',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/allafterservice',
        component: resolve => require(['../pages/order/AllAfterService'], resolve),
        name: 'AllAfterService',
        meta: {
            tabShow: false,
            title: '售后单列表',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/collect',
        component: resolve => require(['../pages/other/Collect'], resolve),
        name: 'Collect',
        meta: {
            navShow: true,
            tabShow: false,
            title: '我的关注',
            keepAlive: false,
            isLogin: true

        }
    },
    {
        path: '/history',
        component: resolve => require(['../pages/other/History'], resolve),
        name: 'History',
        meta: {
            navShow: true,
            tabShow: false,
            title: '我的足迹',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/notice',
        component: resolve => require(['../pages/other/Notice'], resolve),
        name: 'Notice',
        meta: {
            tabShow: false,
            title: '公告详情',
            keepAlive: false
        }
    },
    {
        path: '/article',
        component: resolve => require(['../pages/article/Article'], resolve),
        name: 'Article',
        meta: {
            tabShow: false,
            title: '文章详情',
            keepAlive: false
        }
    },
    {
        path: '/coupon',
        component: resolve => require(['../pages/Coupon'], resolve),
        name: 'Coupon',
        meta: {
            navShow: true,
            tabShow: false,
            title: '我的优惠券',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/evaluate',
        component: resolve => require(['../pages/order/Evaluate'], resolve),
        name: 'Evaluate',
        meta: {
            tabShow: false,
            title: '订单评价',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/datasetting',
        component: resolve => require(['../pages/setting/DataSetting'], resolve),
        name: 'DataSetting',
        meta: {
            navShow: true,
            tabShow: false,
            title: '资料设置',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/aftersalesdetail',
        component: resolve => require(['../pages/order/AfterSalesDetail'], resolve),
        name: 'AfterSalesDetail',
        meta: {
            tabShow: false,
            title: '售后单详情',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/searchpage',
        component: resolve => require(['../pages/search/SearchPage'], resolve),
        name: 'SearchPage',
        meta: {
            tabShow: false,
            title: '商品搜索',
            keepAlive: false
        }
    },
    {
        path: '/address',
        component: resolve => require(['../pages/address/Address'], resolve),
        name: 'Address',
        meta: {
            navShow: true,
            tabshow: false,
            title: '地址详情',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/addresslist',
        component: resolve => require(['../pages/address/AddressList'], resolve),
        name: 'AddressList',
        meta: {
            navShow: true,
            tabshow: false,
            title: '地址管理',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/balance',
        component: resolve => require(['../pages/balance/Balance'], resolve),
        name: 'Balance',
        meta: {
            navShow: true,
            tabshow: false,
            title: '我的余额',
            keepAlive: false
        }
    },
    {
        path: '/balancelist',
        component: resolve => require(['../pages/balance/BalanceList'], resolve),
        name: 'BalanceList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '余额明细',
            keepAlive: false
        }
    },
    {
        path: '/withdrawcash',
        component: resolve => require(['../pages/balance/WithDrawCash'], resolve),
        name: 'WithDrawCash',
        meta: {
            navShow: true,
            tabshow: false,
            title: '余额提现',
            keepAlive: false
        }
    },
    {
        path: '/bankcard',
        component: resolve => require(['../pages/balance/BankCard'], resolve),
        name: 'BankCard',
        meta: {
            navShow: true,
            tabShow: false,
            title: '添加银行卡',
            keepAlive: false
        }
    },
    {
        path: '/mybankcardlist',
        component: resolve => require(['../pages/balance/MyBankCardList'], resolve),
        name: 'MyBankCardList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '我的银行卡',
            keepAlive: false
        }
    },
    {
        path: '/rewardlist',
        component: resolve => require(['../pages/RewardList'], resolve),
        name: 'RewardList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '我的奖励金',
            keepAlive: false
        }
    },
    {
        path: '/recommendlist',
        component: resolve => require(['../pages/share/RecommendList'], resolve),
        name: 'RecommendList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '推荐记录',
            keepAlive: false
        }
    },
    {
        path: '/share',
        component: resolve => require(['../pages/share/Share'], resolve),
        name: 'Share',
        meta: {
            navShow: true,
            tabShow: false,
            title: '推荐好友',
            keepAlive: false
        }
    },
    {
        path: '/cashlist',
        component: resolve => require(['../pages/balance/CashList'], resolve),
        name: 'CashList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '提现记录',
            keepAlive: false
        }
    },
    {
        path: '/setting',
        component: resolve => require(['../pages/setting/Setting'], resolve),
        name: 'Setting',
        meta: {
            navShow: true,
            tabShow: false,
            title: '更多设置',
            keepAlive: false
        }
    },
    {
        path: '/register',
        component: resolve => require(['../pages/register/Register'], resolve),
        name: 'Register',
        meta: {
            navShow: true,
            tabShow: false,
            title: '用户注册',
            keepAlive: false
        }
    },
    {
        path: '/articlelist',
        component: resolve => require(['../pages/article/ArticleList'], resolve),
        name: 'ArticleList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '文章列表',
            keepAlive: false
        }
    },
    {
        path: '/storeorder',
        component: resolve => require(['../pages/store/Order'], resolve),
        name: 'StoreOrder',
        meta: {
            navShow: true,
            tabShow: false,
            title: '店铺提货订单',
            keepAlive: false
        },
    },
    {
        path: '/orderverification',
        component: resolve => require(['../pages/store/OrderVerification'], resolve),
        name: 'OrderVerification',
        meta: {
            navShow: true,
            tabShow: false,
            title: '提货单核销',
            keepAlive: false
        }
    },
    {
        path: '/seckilldetail',
        component: resolve => require(['../pages/activity/SecKilldetail'], resolve),
        name: 'SecKillDetail',
        meta: {
            navShow: false,
            tabShow: false,
            title: '秒杀活动',
            keepAlive: false
        }
    },
    {
        path: '/groupbuying',
        component: resolve => require(['../pages/activity/GroupBuying'], resolve),
        name: 'GroupBuying',
        meta: {
            navShow: false,
            tabShow: false,
            title: '团购活动',
            keepAlive: false
        }
    }
]

export default routers
