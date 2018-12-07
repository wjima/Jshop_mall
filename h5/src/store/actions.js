import {
    SAVE_STORE,
    SAVE_SHIP,
    SAVE_STORE_TAB
} from './mutation-types'
export const actions = {
    saveStore: ({commit}, info) => commit(SAVE_STORE, info),
    saveStoreTab: ({commit}, tab) => commit(SAVE_STORE_TAB, tab),
    saveShip: ({commit}, info) => commit(SAVE_SHIP, info)
}
