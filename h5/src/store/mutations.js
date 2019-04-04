import {
    SHOP_CONFIG,
    INVOICE,
    WECHATPAYSHOW
} from './mutation-types'

export const mutations = {
    [SHOP_CONFIG](state, info) {
        state.config = info
    },
    [INVOICE](state, info) {
        state.invoice = info
    },
    [WECHATPAYSHOW](state, info) {
        state.wechatPayShow = info
    }
}