<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\GroupGoods;
use app\common\model\Promotion as PromotionModel;
use app\common\model\PromotionCondition;
use app\common\model\PromotionResult;
use app\common\model\UserGrade;
use Request;
use app\common\model\GoodsCat;

class Promotion extends Manage
{

    /**
     *
     * User:sin
     *
     * @return mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $promotionModel  = new PromotionModel();
            $request         = input('param.');
            $request['type'] = $promotionModel::TYPE_PROMOTION;
            return $promotionModel->tableData($request);
        }
        return $this->fetch();

    }

    /**
     * @return mixed
     */
    public function coupon()
    {
        if (Request::isAjax()) {
            $promotionModel  = new PromotionModel();
            $request         = input('param.');
            $request['type'] = $promotionModel::TYPE_COUPON;

            return $promotionModel->tableData($request);
        }
        return $this->fetch();

    }

    //添加促销
    public function add()
    {

        if (Request::isPost()) {
            if (!input('?param.name') || input('param.name') == "") {
                return error_code(15001);
            }
            if (!input('?param.date') || !input('param.date')) {
                return error_code(15002);
            } else {
                $theDate = explode(' 到 ', input('param.date'));
                if (count($theDate) != 2) {
                    return error_code(15002);
                }
            }
            $data['name']      = input('param.name');
            $data['stime']     = strtotime($theDate[0]);
            $data['etime']     = strtotime($theDate[1]);
            $data['status']    = input('param.status/d', 1);
            $data['sort']      = input('param.sort/d', 100);
            $data['exclusive'] = input('param.exclusive/d', 1);
            $promotionModel    = new PromotionModel();
            $id                = $promotionModel->insertGetId($data);
            return [
                'status' => true,
                'data'   => url('promotion/edit', ['id' => $id]),
                'msg'    => ''
            ];
        }
        return $this->fetch();
    }

    //添加优惠券
    public function couponAdd()
    {
        if (Request::isPost()) {
            if (!input('?param.name')) {
                return error_code(15001);
            }
            if (!input('?param.date') || !input('param.date')) {
                return error_code(15002);
            } else {
                $theDate = explode(' 到 ', input('param.date'));
                if (count($theDate) != 2) {
                    return error_code(15002);
                }
            }
            $promotionModel       = new PromotionModel();
            $data['name']         = input('param.name');
            $data['stime']        = strtotime($theDate[0]);
            $data['etime']        = strtotime($theDate[1]);
            $data['status']       = input('param.status/d', 1);
            $data['auto_receive'] = input('param.auto_receive/d', 2);
            $data['sort']         = input('param.sort/d', 100);
            $data['type']         = $promotionModel::TYPE_COUPON;
            $data['exclusive'] = input('param.exclusive/d', 1);


            //优惠券领取条件
            $parmas['max_nums'] = input('param.max_nums', 0);
            $parmas['term_day'] = input('param.term_day/d', 0);

            $data['params']     = json_encode($parmas);

            $id = $promotionModel->insertGetId($data);
            return [
                'status' => true,
                'data'   => url('promotion/couponEdit', ['id' => $id]),
                'msg'    => ''
            ];
        }
        return $this->fetch('couponAdd');
    }

    //编辑促销
    public function edit()
    {
        $promotionModel = new PromotionModel();
        $where['id']    = input('param.id');
        $where['type']  = $promotionModel::TYPE_PROMOTION;
        $info           = $promotionModel->where($where)->find();
        if (!$info) {
            $this->error(error_code(15019,true));
        }
        $info['params'] = json_decode($info['params']);

        if (Request::isPost()) {
            if (!input('?param.name') || input('param.name') == "") {
                return error_code(15001);
            }
            if (!input('?param.date') || !input('param.date')) {
                return error_code(15002);
            } else {
                $theDate = explode(' 到 ', input('param.date'));
                if (count($theDate) != 2) {
                    return error_code(15002);
                }
            }
            $data['name']      = input('param.name');
            $data['stime']     = strtotime($theDate[0]);
            $data['etime']     = strtotime($theDate[1]);
            $data['status']    = input('param.status/d', 2);
            $data['sort']      = input('param.sort/d', 100);
            $data['exclusive'] = input('param.exclusive/d', 1);
            $promotionModel    = new PromotionModel();
            $id                = $promotionModel->where($where)->update($data);
            return [
                'status' => true,
                'data'   => url('promotion/edit', ['id' => $id]),
                'msg'    => ''
            ];
        }

        $this->assign('info', $info);

        return $this->fetch();
    }

    //编辑优惠券
    public function couponEdit()
    {
        $promotionModel = new PromotionModel();
        $where['id']    = input('param.id');
        $where['type']  = $promotionModel::TYPE_COUPON;
        $info           = $promotionModel->where($where)->find();
        if (!$info) {
            $this->error(error_code(15020,true));
        }
        $info = $info->toArray();
        //优惠券条件
        $info['params'] = json_decode($info['params'], true);
        if (!isset($info['params']['max_nums'])) {
            $info['params']['max_nums'] = 0;
        }
        if (!isset($info['params']['term_day'])) {
            $info['params']['term_day'] = 0;
        }

        if (Request::isPost()) {
            if (!input('?param.name')) {
                return error_code(15001);
            }
            if (!input('?param.date') || !input('param.date')) {
                return error_code(15002);
            } else {
                $theDate = explode(' 到 ', input('param.date'));
                if (count($theDate) != 2) {
                    return error_code(15002);
                }
            }
            $data['name']         = input('param.name');
            $data['stime']        = strtotime($theDate[0]);
            $data['etime']        = strtotime($theDate[1]);
            $data['status']       = input('param.status/d', 2);
            $data['sort']         = input('param.sort/d', 100);
            $data['exclusive']    = input('param.exclusive/d', 1);
            $data['auto_receive'] = input('param.auto_receive/d', 2);
            //优惠券领取条件
            $parmas['max_nums'] = input('param.max_nums', 0);
            $parmas['term_day'] = input('param.term_day/d', 0);
            $data['params']     = json_encode($parmas);

            $promotionModel = new PromotionModel();
            $id             = $promotionModel->where($where)->update($data);
            return [
                'status' => true,
                'data'   => url('promotion/edit', ['id' => $id]),
                'msg'    => ''
            ];
        }

        $this->assign('info', $info);

        return $this->fetch('couponEdit');
    }

    public function del()
    {
        $promotionModel = new PromotionModel();
        $where['id']    = input('param.id');
        $info           = $promotionModel->where($where)->find();
        if (!$info) {
            return error_code(10002);
        }
        if ($promotionModel::destroy($info['id'])) {
            //删除促销条件和促销结果
            $condition = new PromotionCondition();
            $condition->where('promotion_id', input('param.id'))->delete();
            $result = new PromotionResult();
            $result->where('promotion_id', input('param.id'))->delete();
            return [
                'status' => true,
                'data'   => '',
                'msg'    => ''
            ];
        } else {
            return error_code(10023);
        }
    }

    public function couponDel()
    {
        $promotionModel = new PromotionModel();
        $where['id']    = input('param.id');
        $info           = $promotionModel->where($where)->find();
        if (!$info) {
            return error_code(10002);
        }
        $couponModel = new \app\common\model\Coupon();
        //只删除未使用过的优惠券 已使用过的需要保存记录
        $where = [
            'promotion_id' => $info['id'],
            'is_used'      => 1
        ];
        $couponModel->where($where)->delete();
        if (!$promotionModel::destroy($info['id'])) {
            return error_code(10037);
        }
        $return['status'] = true;
        $return['msg']    = '成功';
        return $return;
    }


    //条件列表
    public function conditionList()
    {
        $conditionModel = new PromotionCondition();
        if (!input('?param.id')) {
            return error_code(10003);
        }

        //校验是否有此权限
        $promotionModel = new PromotionModel();
        $pwhere['id']   = input('param.id');
        $info           = $promotionModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        //$where['id'] = input('param.id');
        $where['promotion_id'] = input('param.id');
        return $conditionModel->tableData($where);
    }

    //单纯的选择促销条件
    public function conditionAdd()
    {
        $this->view->engine->layout(false);
        $conditionModel = new PromotionCondition();
        $this->assign('code', $conditionModel->code);
        return [
            'status' => true,
            'data'   => $this->fetch('conditionAdd'),
            'msg'    => ''
        ];
    }

    //添加促销条件
    public function conditionEdit()
    {
        $this->view->engine->layout(false);


        if (!(input('?param.condition_code') && input('?param.promotion_id')) && !input('?param.id')) {
            return error_code(15003);
        }

        //校验是否有此权限
        $promotionModel = new PromotionModel();
        $pwhere['id']   = input('param.promotion_id');
        $pinfo          = $promotionModel->where($pwhere)->find();
        if (!$pinfo) {
            return error_code(10002);
        }

        $conditionModel = new PromotionCondition();

        if (Request::isPOST()) {
            $data = input('param.');
            return $conditionModel->addData($data);
        }

        //如果是修改，就取数据，否则就是新增，直接渲染模板
        if (input('?param.id')) {
            $info = $conditionModel->getInfo(input('param.id'));

            if (!$info) {
                return error_code(15004);
            }
            $code = $info['code'];
            if ($code == 'GOODS_CATS') {
                if (isset($info['params']['cat_id']) && $info['params']['cat_id']) {
                    $goodsCatModel = new GoodsCat();
                    $catids        = $goodsCatModel->getCatIdsByLastId($info['params']['cat_id']);
                    $this->assign('catids', $catids);
                }
            }
            $this->assign($info->toArray());
        } else {
            $code = input('param.condition_code');
            $this->assign('promotion_id', input('param.promotion_id/d'));
            $this->assign('code', $code);
        }

        //初始化数据
        switch ($code) {
            case 'GOODS_CATS':
                $goodsCatModel = new GoodsCat();
                $catList       = $goodsCatModel->getCatByParentId(0);
                $this->assign('catList', $catList);

                break;
            case 'USER_GRADE':
                $userGradeModel = new UserGrade();
                $gradeList      = $userGradeModel->select();
                $this->assign('gradeList', $gradeList);
                break;
        }

        return [
            'status' => true,
            'data'   => $this->fetch("../extend/org/promotion/condition/tpl/".$conditionModel->code[$code]['class'].".html"),
            'msg'    => ''
        ];
    }

    //促销条件删除
    public function conditionDel()
    {
        //校验是否有此权限
        $promotionModel = new PromotionModel();
        $pwhere['id']   = input('param.promotion_id');
        $info           = $promotionModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        $conditionModel = new PromotionCondition();
        return $conditionModel->toDel(input('param.id'));
    }

    //促销结果列表
    public function resultList()
    {
        $resultModel = new PromotionResult();
        if (!input('?param.id')) {
            return error_code(10003);
        }

        //校验是否有此权限
        $promotionModel = new PromotionModel();
        $pwhere['id']   = input('param.id');
        $info           = $promotionModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }


        //$where['id'] = input('param.id');
        $where['promotion_id'] = input('param.id');
        return $resultModel->tableData($where);
    }

    //单纯的选择促销结果
    public function resultAdd()
    {
        $this->view->engine->layout(false);
        $resultModel = new PromotionResult();
        $type        = input('type', 'promotion');

        if ($type && $type == 'group') {//团购时不要订单促销
            foreach ($resultModel->code as $key => $value) {
                if ($key != 'GOODS_REDUCE' && $key != 'GOODS_DISCOUNT' && $key != 'GOODS_ONE_PRICE') {
                    unset($resultModel->code[$key]);
                }
            }
        }
        $this->assign('code', $resultModel->code);
        return [
            'status' => true,
            'data'   => $this->fetch('resultAdd'),
            'msg'    => ''
        ];
    }

    //添加促销条件
    public function resultEdit()
    {
        $this->view->engine->layout(false);

        if (!(input('?param.result_code') && input('?param.promotion_id')) && !input('?param.id')) {
            return error_code(15003);
        }

        //校验是否有此权限
        $promotionModel = new PromotionModel();
        $pwhere['id']   = input('param.promotion_id');
        $info           = $promotionModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        $resultModel = new PromotionResult();

        if (Request::isPOST()) {
            $data = input('param.');
            //团购和秒杀时，限制一个促销结果
            if ($info['type'] == $promotionModel::TYPE_GROUP || $info['type'] == $promotionModel::TYPE_SKILL) {
                $result = $resultModel->where(['promotion_id' => $pwhere['id']])->find();
                if ($result && $result['id'] != input('param.id')) {
                    return error_code(15016);
                }
            }
            return $resultModel->addData($data);
        }

        //如果是修改，就取数据，否则就是新增，直接渲染模板
        if (input('?param.id')) {
            $info = $resultModel->getInfo(input('param.id'));
            if (!$info) {
                return error_code(15004);
            }
            $code = $info['code'];
            $this->assign($info->toArray());
        } else {
            $code = input('param.result_code');
            $this->assign('promotion_id', input('param.promotion_id/d'));
            $this->assign('code', $code);
        }


        return [
            'status' => true,
            'data'   => $this->fetch("../extend/org/promotion/result/tpl/".$resultModel->code[$code]['class'].".html"),
            'msg'    => ''
        ];
    }

    //促销条件删除
    public function resultDel()
    {
        //校验是否有此权限
        $promotionModel = new PromotionModel();
        $pwhere['id']   = input('param.promotion_id');
        $info           = $promotionModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        $resultModel = new PromotionResult();
        return $resultModel->toDel(input('param.id'));
    }


    /**
     * @return mixed
     */
    public function group()
    {
        if (Request::isAjax()) {
            $promotionModel  = new PromotionModel();
            $request         = input('param.');
            $request['type'] = [$promotionModel::TYPE_GROUP, $promotionModel::TYPE_SKILL];

            return $promotionModel->tableData($request);
        }
        return $this->fetch();
    }

    //添加团购秒杀
    public function groupAdd()
    {
        if (Request::isPost()) {
            if (!input('?param.name')) {
                return error_code(15001);
            }
            if (!input('?param.date') || !input('param.date')) {
                return error_code(15002);
            } else {
                $theDate = explode(' 到 ', input('param.date'));
                if (count($theDate) != 2) {
                    return error_code(15002);
                }
            }

            $promotionModel    = new PromotionModel();
            $data['name']      = input('param.name');
            $data['stime']     = strtotime($theDate[0]);
            $data['etime']     = strtotime($theDate[1]);
            $data['status']    = input('param.status/d', 1);
            $data['sort']      = input('param.sort/d', 100);
            $data['type']      = input('param.type/d', 3);
            $data['exclusive'] = input('param.exclusive/d', 2);

            $params                   = input('param.params/a', []);
            $params['max_nums']       = input('param.max_nums/d', 0);//每人限购
            $params['max_goods_nums'] = input('param.max_goods_nums/d', 0);//总量

            if (isset($params['salesnum']) && !$params['salesnum']) {
                $params['salesnum'] = rand(1, 10);
            }
            //判断是否已经加入团购
            if (isInGroup($params['goods_id'])) {
                return error_code(15017);
            }
            $data['params'] = json_encode($params);
            $id             = $promotionModel->insertGetId($data);
            return [
                'status' => true,
                'data'   => url('promotion/groupEdit', ['id' => $id]),
                'msg'    => '',
            ];
        }
        return $this->fetch('groupAdd');
    }

    //编辑团购（秒杀）
    public function groupEdit()
    {
        $promotionModel = new PromotionModel();
        $id             = input('param.id/d', '0');
        $where[]        = ['id', '=', $id];

        $where[] = ['type', 'in', [$promotionModel::TYPE_GROUP, $promotionModel::TYPE_SKILL]];
        $info    = $promotionModel->where($where)->find();
        if (!$info) {
            $this->error(error_code(15019,true));
        }
        //取促销信息
        $conditionModel    = new PromotionCondition();
        $promotion         = $conditionModel->where(['promotion_id' => $id])->find();
        $goods['goods_id'] = '';
        if ($promotion) {
            $params            = json_decode($promotion['params'], true);
            $goods['goods_id'] = $params['goods_id'];
        }
        $this->assign('goods', $goods);

        if (Request::isPost()) {
            if (!input('?param.name')) {
                return error_code(15001);
            }
            if (!input('?param.date') || !input('param.date')) {
                return error_code(15002);
            } else {
                $theDate = explode(' 到 ', input('param.date'));
                if (count($theDate) != 2) {
                    return error_code(15002);
                }
            }
            $data['name']      = input('param.name');
            $data['stime']     = strtotime($theDate[0]);
            $data['etime']     = strtotime($theDate[1]);
            $data['status']    = input('param.status/d', 2);
            $data['sort']      = input('param.sort/d', 100);
            $data['exclusive'] = input('param.exclusive/d', 2);
            $data['type']      = input('param.type/d', 3);
            $params            = input('param.params/a', []);

            $params['max_nums']       = input('param.max_nums/d', 0);//每人限购
            $params['max_goods_nums'] = input('param.max_goods_nums/d', 0);//总量

            if (isset($params['salesnum']) && !$params['salesnum']) {
                $params['salesnum'] = rand(1, 10);
            }
            $data['params']  = json_encode($params);
            $promotionModel  = new PromotionModel();
            $groupGoodsModel = new GroupGoods();
            //保存或更新促销条件商品


            $goods_id   = input('post.goods_id');
            if (!$goods_id) {
                $result =  error_code(12009);
                return $result;
            }
            $goods_ids  = explode(',', $goods_id);
            $groupGoods = [];

            foreach ($goods_ids as $gid) {
                //判断商品是否在促销中，如果存在不允许保存
                $goods = $groupGoodsModel->checkInActivity($gid, $id);
                if ($goods) {
                    $result = [
                        'status' => false,
                        'data'   => 0,
                        'msg'    => error_code(12014,true,$goods['goods_name'],$goods['name']),
                    ];
                    return $result;
                }
                $groupGoods[] = [
                    'goods_id' => $gid,
                    'rule_id'  => $id
                ];
            }
            $conditionData = [
                'promotion_id' => $id,
                'code'         => 'GOODS_IDS',
                'params'       => ['goods_id' => $goods_id, 'nums' => '1'],
            ];
            $conditionModel->where(['promotion_id' => $id])->delete();

            $conditionRes  = $conditionModel->addData($conditionData);
            if (!$conditionRes['status']) {
                return $conditionRes;
            }
            //保存到关系表中

            $groupGoodsModel->where([['rule_id', '=', $info['id']]])->delete();

            $groupGoodsModel->saveAll($groupGoods);
            $id = $promotionModel->where($where)->update($data);
            return [
                'status' => true,
                'data'   => url('promotion/edit', ['id' => $id]),
                'msg'    => '',
            ];
        }
        $info['params'] = json_decode($info['params'], true);
        $this->assign('info', $info);

        return $this->fetch('groupEdit');
    }

    /**
     * 先删除促销信息，促销条件和结果可能跟订单有关，暂时不删除
     *
     * @return array|mixed
     */
    public function groupdel()
    {
        $promotionModel = new PromotionModel();
        $where['id']    = input('param.id');
        $info           = $promotionModel->where($where)->find();
        if (!$info) {
            return error_code(10002);
        }
        if ($promotionModel::destroy($info['id'])) {
            $groupGoodsModel = new GroupGoods();
            $groupGoodsModel->where([['rule_id', '=', $info['id']]])->delete();
            return [
                'status' => true,
                'data'   => '',
                'msg'    => ''
            ];
        } else {
            return error_code(10023);
        }
    }


    /**
     *  更改设置状态
     *
     */
    public function changeState()
    {
        $result         = [
            'status' => false,
            'msg'    => '',
            'data'   => []
        ];
        $promotionModel = new \app\common\model\Promotion();
        $id             = input('param.id/d', 0);
        $elem           = input('param.elem/s', '');
        $state          = input('param.state/s', 'true');

        if (!$id && !$elem) return error_code(10051);
        if ($elem === 'status') {
            $change = $state === 'true'
                ? $promotionModel::STATUS_OPEN
                : $promotionModel::STATUS_CLOSE;
        } else if ($elem === 'exclusive') {
            $change = $state === 'true'
                ? $promotionModel::EXCLUSIVE_YES
                : $promotionModel::EXCLUSIVE_NO;
        }
        switch ($elem) {
            case 'status':
                $iData['status'] = $change;
                break;
            case 'exclusive':
                $iData['exclusive'] = $change;
                break;
            default:
                $iData = '';
                break;
        }

        if ($promotionModel->save($iData, ['id' => $id])) {
            $result['status'] = true;
            $result['msg']    = '设置成功';
        } else {
            return error_code(10021);
        }

        return $result;
    }
}