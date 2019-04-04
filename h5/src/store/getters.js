export const getters = {
    shopName: state => state.config.shop_name,
    shopDesc: state => state.config.shop_desc,
    shopLogo: state => state.config.shop_logo,
    shopUploadImageMax: state => state.config.upload_image_max,
    shopStoreSwitch: state => state.config.store_switch,
    shopCateStyle: state => state.config.cate_style,
    shopCateType: state => state.config.cate_type,
    tocashMoneyLow: state => state.config.tocash_money_low,
    tocashMoneyRate: state => state.config.tocash_money_rate,
	statistics: state => state.config.statistics,
    invoice: state => state.invoice
}
