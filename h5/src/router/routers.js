import Index from '../pages/Index'
import Classify from '../pages/Classify'
import User from '../pages/User'
import Cart from '../pages/Cart'
import Login from '../pages/Login'
import GoodsList from '../pages/GoodsList'
import GoodsDetail from '../pages/GoodsDetail'
import FirmOrder from '../pages/FirmOrder'
import CashierDesk from '../pages/CashierDesk'
import AllOrder from '../pages/AllOrder'
import OrderDetail from '../pages/OrderDetail'
import AfterService from '../pages/AfterService'
import AllAfterService from '../pages/AllAfterService'
import AfterSalesDetail from '../pages/AfterSalesDetail'
import Collect from '../pages/Collect'
import History from '../pages/History'
import Notice from '../pages/Notice'
import Coupon from '../pages/Coupon'
import Evaluate from '../pages/Evaluate'
import DataSetting from '../pages/DataSetting'
import SearchPage from '../pages/SearchPage'
import Article from '../pages/Article'
import Address from '../pages/Address'
import AddressList from '../pages/AddressList'
import Balance from '../pages/Balance'
import WithdrawCash from '../pages/WithdrawCash'
import BankCard from '../pages/BankCard'
import BalanceList from '../pages/BalanceList'
import MyBankCardList from '../pages/MyBankCradList'
import RewardList from '../pages/RewardList'
import RecommendList from '../pages/RecommendList'
import Share from '../pages/Share'
import Setting from '../pages/Setting'
import CashList from '../pages/CashList'
import Register from '../pages/Register'
import ArticleList from '../pages/ArticleList'

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
        path: '/index',
        component: Index,
        name: 'index',
        meta: {
            navShow: false,
            tabShow: true,
            keepAlive: true
        }
    },
    {
        path: '/classify',
        component: Classify,
        name: 'classify',
        meta: {
            navShow: false,
            tabShow: true,
            title: '全部分类',
            keepAlive: true
        }
    },
    {
        path: '/cart',
        component: Cart,
        name: 'cart',
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
        component: User,
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
        component: Login,
        name: 'login',
        meta: {
            navShow: true,
            tabShow: false,
            title: '用户登录',
            keepAlive: false
        }
    },
    {
        path: '/goodslist',
        component: GoodsList,
        name: 'goodslist',
        meta: {
            navShow: true,
            tabShow: false,
            title: '商品列表',
            keepAlive: false
        }
    },
    {
        path: '/goodsdetail',
        component: GoodsDetail,
        name: 'goodsdetail',
        meta: {
            navShow: false,
            tabShow: false,
            title: '商品详情',
            keepAlive: false
        }
    },
    {
        path: '/firmorder',
        component: FirmOrder,
        name: 'firmorder',
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
        component: CashierDesk,
        name: 'cashierdesk',
        meta: {
            tabShow: false,
            title: '订单支付',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/allorder',
        component: AllOrder,
        name: 'allorder',
        meta: {
            tabShow: false,
            title: '订单列表',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/orderdetail',
        component: OrderDetail,
        name: 'orderdetail',
        meta: {
            tabShow: false,
            title: '订单详情',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/afterservice',
        component: AfterService,
        name: 'afterservice',
        meta: {
            tabShow: false,
            title: '申请售后',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/allafterservice',
        component: AllAfterService,
        name: 'allafterservice',
        meta: {
            tabShow: false,
            title: '售后单列表',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/collect',
        component: Collect,
        name: 'collect',
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
        component: History,
        name: 'history',
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
        component: Notice,
        name: 'notice',
        meta: {
            tabShow: false,
            title: '公告详情',
            keepAlive: false
        }
    },
    {
        path: '/article',
        component: Article,
        name: 'article',
        meta: {
            tabShow: false,
            title: '文章详情',
            keepAlive: false
        }
    },
    {
        path: '/coupon',
        component: Coupon,
        name: 'coupon',
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
        component: Evaluate,
        name: 'evaluate',
        meta: {
            tabShow: false,
            title: '订单评价',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/datasetting',
        component: DataSetting,
        name: 'datasetting',
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
        component: AfterSalesDetail,
        name: 'aftersalesdetail',
        meta: {
            tabShow: false,
            title: '售后单详情',
            keepAlive: false,
            isLogin: true
        }
    },
    {
        path: '/searchpage',
        component: SearchPage,
        name: 'searchpage',
        meta: {
            tabShow: false,
            title: '商品搜索',
            keepAlive: false
        }
    },
    {
        path: '/address',
        component: Address,
        name: 'address',
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
        component: AddressList,
        name: 'addresslist',
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
        component: Balance,
        name: 'balance',
        meta: {
            navShow: true,
            tabshow: false,
            title: '我的余额',
            keepAlive: false
        }
    },
    {
        path: '/balancelist',
        component: BalanceList,
        name: 'balancelist',
        meta: {
            navShow: true,
            tabShow: false,
            title: '余额明细',
            keepAlive: false
        }
    },
    {
        path: '/withdrawcash',
        component: WithdrawCash,
        name: 'withdrawcash',
        meta: {
            navShow: true,
            tabshow: false,
            title: '余额提现',
            keepAlive: false
        }
    },
    {
        path: '/bankcard',
        component: BankCard,
        name: 'bankcard',
        meta: {
            navShow: true,
            tabShow: false,
            title: '添加银行卡',
            keepAlive: false
        }
    },
    {
        path: '/mybankcardlist',
        component: MyBankCardList,
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
        component: RewardList,
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
        component: RecommendList,
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
        component: Share,
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
        component: CashList,
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
        component: Setting,
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
        component: Register,
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
        component: ArticleList,
        name: 'ArticleList',
        meta: {
            navShow: true,
            tabShow: false,
            title: '文章列表',
            keepAlive: false
        }
    }

]

export default routers
