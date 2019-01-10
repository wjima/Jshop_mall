import {
    SHOP_CONFIG
} from './mutation-types'

export const actions = {
    shopConfig: ({commit}, info) => commit(SHOP_CONFIG, info)
}
