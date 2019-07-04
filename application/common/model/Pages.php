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
        }
        return $list;
    }

    /**
     * 获取页面配置详情
     * @param $page_code
     * @return array
     */
    public function getDetails($page_code)
    {
        $result          = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => []
        ];
        $pagesItemsModel = new PagesItems();
        $data            = $pagesItemsModel->where([['page_code', '=', $page_code]])->order('sort asc')->select();
        if ($data->isEmpty()) {
            $result['msg'] = '请先配置该页面';
            return $result;
        }
        $data = $data->toArray();
        foreach ($data as $key => $value) {
            $where = [];
            $data[$key]['params'] = json_decode($value['params'], true);
            if ($value['widget_code'] == 'notice') {
                if ($data[$key]['params']['type'] == 'auto') {
                    $noticeModel                  = new Notice();
                    $list                         = $noticeModel->getNoticeList();
                    $data[$key]['params']['list'] = $list;
                }
            } elseif ($value['widget_code'] == 'coupon') {
                $promotionModel               = new Promotion();
                $list                         = $promotionModel->receiveCouponList($data[$key]['params']['limit']);
                $data[$key]['params']['list'] = $list;
            } elseif ($value['widget_code'] == 'goods') {
                $list = [];
                if ($data[$key]['params']['type'] == 'auto') {
                    $goodsModel = new Goods();
                    //商品分类,同时取所有子分类 todo 无限极分类时要注意
                    if (isset($data[$key]['params']['classifyId']) && $data[$key]['params']['classifyId']) {
                        $goodsCatModel = new GoodsCat();
                        $catIds        = [];
                        $childCats     = $goodsCatModel->getCatByParentId($data[$key]['params']['classifyId']);
                        if (!$childCats->isEmpty()) {
                            $filter['child_cats'] = $childCats;
                        }
                        $catIds   = array_column($childCats->toArray(), 'id');
                        $catIds[] = $data[$key]['params']['classifyId'];
                        $where[]  = ['g.goods_cat_id', 'in', $catIds];
                    }
                    //品牌筛选
                    if (isset($data[$key]['params']['brandId']) && $data[$key]['params']['brandId']) {
                        $where[] = ['g.brand_id', 'in', $data[$key]['params']['brandId']];
                    }
                    $where[]                      = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];
                    $limit                        = isset($data[$key]['params']['limit']) ? $data[$key]['params']['limit'] : config('jshop.page_limit');
                    $returnGoods                  = $goodsModel->getList('id,name,bn,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,is_nomal_virtual,marketable,stock,weight,unit,spes_desc,params,comments_count,view_count,buy_count,sort,is_recommend,is_hot,label_ids', $where, 'sort asc', 1, $limit);
                    $data[$key]['params']['list'] = $returnGoods['data'];
                }
            } elseif ($value['widget_code'] == 'articleClassify') {
                $article                      = new Article();
                $type_id                      = $data[$key]['params']['articleClassifyId'];
                $limit                        = $data[$key]['params']['limit'];
                $res                          = $article->articleList($type_id, 1, $limit);
                $data[$key]['params']['list'] = $res['data']['list'];
            } elseif ($value['widget_code'] == 'groupPurchase') {
                $promotion      = new Promotion();
                $conditionModel = new PromotionCondition();
                $token          = '';//todo 会员登录后
                foreach ((array)$data[$key]['params']['list'] as $k => $v) {
                    if (!isset($v['id'])) {
                        unset($data[$key]['params']['list'][$k]);
                    }
                    $filter['promotion_id'] = $v['id'];
                    $condition              = $conditionModel->field('*')->where($filter)->find();
                    $condition['params']    = json_decode($condition['params'], true);
                    if (isset($condition['params']['goods_id']) && $condition['params']['goods_id']) {
                        $goods = $promotion->getGroupDetial($condition['params']['goods_id'], $token);
                        if ($goods['status']) {
                            $data[$key]['params']['list'][$k]['goods'] = $goods['data'];
                        }
                    }
                }
            } elseif ($value['widget_code'] == 'textarea') {
                $data[$key]['params'] = clearHtml($data[$key]['params'], ['width', 'height']);//清除文章中宽高
                $data[$key]['params'] = str_replace("<img", "<img style='max-width: 100%'", $data[$key]['params']);
            }
        }
        $result['data'] = $data;
        return $result;
    }

}