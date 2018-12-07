import {
    SAVE_STORE,
    SAVE_SHIP,
    SAVE_STORE_TAB
} from './mutation-types'
export const mutations = {
    [SAVE_STORE] (state, info) {
        state.storeInfo = info
    },
    [SAVE_SHIP] (state, info) {
        state.shipInfo = info
    },
    [SAVE_STORE_TAB] (state, tab) {
        state.storeTab = tab
    }
}
