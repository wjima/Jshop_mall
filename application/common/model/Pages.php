<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class Pages extends Common
{

    const MAIN_YES = 1;//首页
    const MAIN_NO = 2;//主页

    /**
     * @param $post
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }

        $tableWhere = $this->tableWhere($post);
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        return $re;
    }


    //where搜索条件
    protected function tableWhere($post)
    {
        $where           = [];
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id desc'];
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as &$val) {
            $val['layout'] = config('params.pages.layout')[$val['layout']];
            $val['type']   = config('params.pages.type')[$val['type']];
            $val['main']   = config('params.pages.is_main')[$val['is_main']];
        }
        return $list;
    }

    /**
     * 获取保存的页面配置信息
     * 后台获取配置项方法
     * @param $page_code
     */
    public function getInfo($page_code = '')
    {
        $result            = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        $pageModel         = new Pages();
        $pagesItemsModel   = new PagesItems();
        $pageinfo          = $pageModel->where([['code', '=', $page_code]])->find();
        $pageinfo['items'] = $pagesItemsModel->where([['page_code', '=', $page_code]])->order('sort asc')->select();
        if ($pageinfo['items']->isEmpty()) {
            return error_code(34800);
        }
        $result['data'] = $pageinfo;
        return $result;
    }

    /**
     * 获取页面配置详情
     * 调整为只前台获取用
     * @param $page_code 页面编码
     * @param string $token
     * @param bool|false $api 是否接口访问，接口为前台
     * @return array
     */
    public function getHomeDetails($token = '')
    {
        $result          = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        $pageModel       = new Pages();
        $pagesItemsModel = new PagesItems();
        $result['data']  = $pageModel->where([['is_main', '=', 1]])->cache(86400)->find();
        if(!$result['data']){
            return error_code(34800);
        }
        $data            = $pagesItemsModel->where([['page_code', '=', $result['data']['code']]])->order('sort asc')->cache(86400)->select();

        if ($data->isEmpty()) {
            return error_code(34800);
        }
        $contentRes = $this->getContent($data,$token);
        if (!$contentRes['status']) {
            return $contentRes;
        }
        $result['data']['items'] = $contentRes['data'];
        return $result;
    }

    public function getContent($data,$token = ''){
        $result            = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        try {
            $data = $data->toArray();
            $i    = 0;
            foreach ($data as $key => $value) {
                $data[$i]['params'] = json_decode($value['params'], true);
                if ($value['widget_code'] == 'notice') {
                    if ($data[$i]['params']['type'] == 'auto') {
                        $noticeModel                = new Notice();
                        $list                       = $noticeModel->getNoticeList();
                        $data[$i]['params']['list'] = $list;
                    }
                } elseif ($value['widget_code'] == 'coupon') {
                    $promotionModel             = new Promotion();
                    $list                       = $promotionModel->receiveCouponList($data[$i]['params']['limit']);
                    $data[$i]['params']['list'] = $list;
                } elseif ($value['widget_code'] == 'goods') {
                    $list       = $where = [];
                    $whereRaw   = ' 1=1 ';
                    $goodsModel = new Goods();
                    if ($data[$i]['params']['type'] == 'auto') {
                        //商品分类,同时取所有子分类 todo 无限极分类时要注意
                        if (isset($data[$i]['params']['classifyId']) && trim($data[$i]['params']['classifyId'])) {
                            $goodsCatModel = new GoodsCat();
                            $catIds        = [];
                            $childCats     = $goodsCatModel->getCatByParentId($data[$i]['params']['classifyId']);
                            $catIds        = array_column($childCats->toArray(), 'id');
                            $catIds[]      = $data[$i]['params']['classifyId'];
                            //$where[]       = ['g.goods_cat_id', 'in', $catIds];
                            //扩展分类
                            $goodsExtendCat = new GoodsExtendCat();
                            $goods_ids     = $goodsExtendCat->getGoodsIdByCat($catIds, true);
                            if ($goods_ids) {
                                $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $catIds) . ') or g.id in (' . implode(',', $goods_ids) . ') ) ';
                            } else {
                                $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $catIds) . ') ) ';
                            }
                        }
                        //品牌筛选
                        if (isset($data[$i]['params']['brandId']) && $data[$i]['params']['brandId']) {
                            $where[] = ['g.brand_id', 'in', $data[$key]['params']['brandId']];
                        }
                        $where[]                    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];
                        $limit                      = isset($data[$i]['params']['limit']) ? $data[$i]['params']['limit'] : config('jshop.page_limit');
                        $returnGoods                = $goodsModel->getList('id,name,bn,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,is_nomal_virtual,marketable,stock,weight,unit,spes_desc,params,comments_count,view_count,buy_count,sort,is_recommend,is_hot,label_ids', $where, 'sort asc', 1, $limit, $whereRaw);
                        $data[$i]['params']['list'] = $returnGoods['data'];
                    } else {
                        foreach ((array)$data[$i]['params']['list'] as $gk => $gv) {
                            $goods                           = $goodsModel->getGoodsDetial($gv['id'], 'id,name,bn,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,is_nomal_virtual,marketable,stock,weight,unit,spes_desc,params,comments_count,view_count,buy_count,sort,is_recommend,is_hot,label_ids', $token);
                            $data[$i]['params']['list'][$gk] = $goods['data'];
                        }
                    }
                } elseif ($value['widget_code'] == 'articleClassify') {
                    $article                    = new Article();
                    $type_id                    = $data[$i]['params']['articleClassifyId'];
                    $limit                      = $data[$i]['params']['limit'];
                    $res                        = $article->articleList($type_id, 1, $limit);
                    $data[$i]['params']['list'] = $res['data']['list'];
                } elseif ($value['widget_code'] == 'groupPurchase') {
                    $promotion = new Promotion();
                    if (isset($data[$i]['params']['list']) && $data[$i]['params']['list']) {
                        foreach ((array)$data[$i]['params']['list'] as $k => $v) {
                            if (isset($v['goods_id']) && $v['goods_id']) {
                                $goods = $promotion->getGroupDetial($v['goods_id'], $token, 'id,name,bn,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,is_nomal_virtual,marketable,stock,weight,unit,spes_desc,params,comments_count,view_count,buy_count,sort,is_recommend,is_hot,label_ids', $v['id']);
                                if ($goods['status']) {
                                    $data[$i]['params']['list'][$k] = $goods['data'];
                                } else {
                                    $data[$i]['params']['list'][$k] = [];
                                }
                            }
                        }
                    }
                    $data[$i]['params']['list'] = array_values(array_filter((array)$data[$i]['params']['list']));
                } elseif ($value['widget_code'] == 'pintuan') {
                    $pintuanModel = new PintuanRule();
                    $pi           = 0;
                    $pintuan      = [];
                    foreach ((array)$data[$i]['params']['list'] as $k => $v) {
                        if ($v['goods_id']) {
                            $goodsinfo = $pintuanModel->getPintuanInfo($v['goods_id']);
                            if ($goodsinfo) {
                                $pintuan[$pi]                         = $v;
                                $pintuan[$pi]['pintuan_start_status'] = 1;
                                //判断拼团状态
                                $nowtime = time();
                                if ($goodsinfo['stime'] > $nowtime) {
                                    $pintuan[$pi]['pintuan_start_status'] = 2;//未开始
                                    $pintuan[$pi]['lasttime']             = secondConversionArray($goodsinfo['stime'] - time());
                                } elseif ($goodsinfo['stime'] <= $nowtime && $goodsinfo['etime'] > $nowtime) {
                                    $pintuan[$pi]['lasttime']             = secondConversionArray($goodsinfo['etime'] - time());
                                    $pintuan[$pi]['pintuan_start_status'] = 1;//已开始
                                } else {
                                    $pintuan[$pi]['pintuan_start_status'] = 3;//已过期
                                }
                                $pintuan[$pi]['pintuan_price'] = $goodsinfo['pintuan_price'];
                            } else {
                                $pintuan[$pi] = [];
                            }
                            $pi++;
                        }
                    }
                    $data[$i]['params']['list'] = array_values(array_filter((array)$pintuan));
                } elseif ($value['widget_code'] == 'textarea') {
                    $data[$i]['params'] = clearHtml($data[$i]['params'], ['width', 'height']);//清除文章中宽高
                    $data[$i]['params'] = str_replace("<img", "<img style='max-width: 100%'", $data[$i]['params']);
                }
                $i++;
            }
        } catch (Exception $e) {
            $result['status'] = false;
            $result['msg']    = $e->getMessage();
            return $result;
        }
        $result['data'] = $data;
        return $result;
    }
    /**
     * 获取页面配置详情
     * 调整为只前台获取用
     * @param $page_code 页面编码
     * @param string $token
     * @param bool|false $api 是否接口访问，接口为前台
     * @return array
     */
    public function getDetails($page_code, $token = '')
    {
        $result          = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        $pageModel       = new Pages();
        $pagesItemsModel = new PagesItems();
        $result['data']  = $pageModel->where([['code', '=', $page_code]])->cache(86400)->find();

        $data            = $pagesItemsModel->where([['page_code', '=', $page_code]])->order('sort asc')->cache(86400)->select();

        if ($data->isEmpty()) {
            return error_code(34800);
        }
        $contentRes = $this->getContent($data,$token);
        if (!$contentRes['status']) {
            return $contentRes;
        }
        $result['data']['items'] = $contentRes['data'];
        return $result;
    }



    public function addData($data = [])
    {
        $result = error_code(10004);

        if (!$data['name']) {
//            $result['msg'] = '请输入名称';
            return error_code(10900);
        }


        // 判断该编码是否已经添加
        if ($this->where('code', $data['code'])->find()) {
//            $result['msg'] = '该编码已存在,勿重复添加!';
            return error_code(10901);
        }

        if ($this->allowField(true)->save($data)) {
            $result['status'] = true;
            $result['msg']    = '保存成功';
        }

        return $result;

    }


    public function items()
    {
        return $this->hasMany('PagesItems', 'page_code', 'code');
    }
}