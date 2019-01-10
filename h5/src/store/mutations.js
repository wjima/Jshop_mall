import {
    SHOP_CONFIG
} from './mutation-types'

export const mutations = {
    [SHOP_CONFIG] (state, info) {
        state.config = info
    }
}
