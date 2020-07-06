<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Validate;
use think\Db;
use app\common\model\Logistics;


/**
 * 配送方式表
 * Class Logistics
 * @package app\common\model
 * @author keinx
 */
class Ship extends Common
{

    const HAS_COD_NO = 1;//不是货到付款
    const HAS_COD_YES = 2;//是货到付款
    const TYPE_ALL = 1;//全部地区
    const TYPE_PART = 2;//部分地区
    const DEF_AREA_FEE_YES = 1;//启用
    const DEF_AREA_FEE_NO = 2;//不启用
    const IS_DEF_YES = 1;//默认
    const IS_DEF_NO = 2;//不默认
    const STATUS_YES = 1;//正常
    const STATUS_NO = 2;//停用
    const FREE_POSTAGE_YES = 1;//包邮
    const FREE_POSTAGE_NO = 2;//不包邮

    /**
     * 配送公式
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 14:41
     */
    private function getExp($firstunit, $continueunit, $firstunit_price, $continueunit_price)
    {
        $exp = "$firstunit_price + (ceil(abs(w-$firstunit)/$continueunit) * $continueunit_price)";
        return $exp;
    }

    /**
     * 添加配送方式
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 14:41
     */
    public function add($data = [])
    {
        if ($data['free_postage'] != self::FREE_POSTAGE_YES) {
            $data['exp'] = $this->getExp($data['firstunit'], $data['continueunit'], $data['firstunit_price'], $data['continueunit_price']);
        }
        if ($data['type'] == self::TYPE_PART) {
            foreach ($data['area_fee'] as $key => $val) {
                $data['area_fee'][$key]['exp'] = $this->getExp($data['firstunit'], $data['continueunit'], $val['firstunit_area_price'], $val['continueunit_area_price']);
            }
            $data['area_fee'] = json_encode($data['area_fee']);
        }
        $logisticsModel = new Logistics();
        $logistics      = $logisticsModel->where(['logi_code' => $data['logi_code']])->find();

        if (!$logistics) {
//            $result['msg'] = '物流公司不存在';
            return error_code(13232);
        }
        $data['logi_name'] = $logistics['logi_name'];

        if ($this->insert($data)) {
            $result['status'] = true;
            $result['msg']    = '添加成功';
            $result['data'] = '';
        }else{
            return error_code(10038);
        }
        return $result;
    }


    public function toSave($data = [], $id = 0)
    {
        if ($data['free_postage'] != self::FREE_POSTAGE_YES) {
            $data['exp'] = $this->getExp($data['firstunit'], $data['continueunit'], $data['firstunit_price'], $data['continueunit_price']);
        }
        if ($data['type'] == self::TYPE_PART) {
            foreach ($data['area_fee'] as $key => $val) {
                $data['area_fee'][$key]['exp'] = $this->getExp($data['firstunit'], $data['continueunit'], $val['firstunit_area_price'], $val['continueunit_area_price']);
            }
            $data['area_fee'] = json_encode($data['area_fee']);
        }
        $logisticsModel = new Logistics();
        $logistics      = $logisticsModel->where(['logi_code' => $data['logi_code']])->find();

        if (!$logistics) {
            $result['msg'] = '';
            return error_code(13232);
        }
        $data['logi_name'] = $logistics['logi_name'];

        if ($this->save($data, ['id' => $id]) !== false) {
            $result['status'] = true;
            $result['msg']    = '保存成功';
            $result['data'] = '';
        }else{
            return error_code(10004);
        }
        return $result;
    }


    /**
     * 获取配送费用
     * @param int $area_id 地区id
     * @param int $weight 重量，单位g
     * @return string
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 15:31
     *      $shiModel = new ShipModel();
     *      $shiModel->getShipCost(1,1600);
     *      die();
     */
    /**
     * 获取配送费用
     * @param int $area_id 地区id
     * @param int $weight 重量,单位g
     * @param int $totalmoney 商品总价
     * @return string
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 15:31
     *      $shiModel = new ShipModel();
     *      $shiModel->getShipCost(1,1,1600);
     *      die();
     */
    public function getShipCost($area_id = 0, $weight = 0, $totalmoney = 0)
    {


        $postfee = '0.00';
        //先判断是否子地区满足条件
        $def = $this->where([
            ['status', '=', self::STATUS_YES], ['area_fee', 'like', '%\\\"' . $area_id . '\\\"%'], ['type', '=', self::TYPE_PART],
        ])->find();
        //没有子地区取默认
        if (!$def) {
            $def = $this->where(['is_def' => self::IS_DEF_YES, 'status' => self::STATUS_YES])->find();
        }
        //没有默认取启用状态
        if (!$def) {
            $def = $this->where(['status' => self::STATUS_YES])->find();
            if (!$def) {//没有配送方式，返回0
                return $postfee;
            }
        }

        if ($def['free_postage'] == self::FREE_POSTAGE_YES) {
            return $postfee;
        }
        if ($def['type'] == self::TYPE_PART) {
            $area_fee = json_decode($def['area_fee'], true);
            if ($area_fee) {
                $isIn = false;
                foreach ($area_fee as $key => $val) {
                    $val['goodsmoney'] = $def['goodsmoney'];
                    $area              = explode(',', $val['area']);
                    if (in_array($area_id, $area)) {
                        $isIn             = true;
                        $val['firstunit'] = $def['firstunit'];
                        $total            = self::calculate_fee($val, $weight, $totalmoney);
                        $postfee          = getMoney($total);
                        break;
                    }
                }
                if (!$isIn) {
                    $total   = self::calculate_fee($def, $weight, $totalmoney);
                    $postfee = getMoney($total);
                }
            } else {
                $total   = self::calculate_fee($def, $weight, $totalmoney);
                $postfee = getMoney($total);
            }
        } else {
            $total   = self::calculate_fee($def, $weight, $totalmoney);
            $postfee = getMoney($total);
        }

        return $postfee;

    }

    /**
     * 根据地区获取配送方式
     * todo 地区判断以后再加
     * @param int $area_id
     * @return array|bool|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShip($area_id = 0)
    {

        $def = $this->where([
            ['status', '=', self::STATUS_YES], ['area_fee', 'like', '%\\\"' . $area_id . '\\\"%'], ['type', '=', self::TYPE_PART],
        ])->find();
        //没有子地区取默认
        if (!$def) {
            $def = $this->where(['is_def' => self::IS_DEF_YES, 'status' => self::STATUS_YES])->find();
        }
        //没有默认取启用状态
        if (!$def) {
            $def = $this->where(['status' => self::STATUS_YES])->find();
            if (!$def) {//没有配送方式，返回0
                return false;
            }
        }

        return $def;
    }

    /**
     * 计算运费
     * @param $ship 配送方式内容
     * @param $weight 订单总重
     * @param int $totalmoney 商品总价
     * @return int
     */
    static function calculate_fee($ship, $weight, $totalmoney = 0)
    {
        //满多少免运费
        if (isset($ship['goodsmoney']) && $ship['goodsmoney'] > 0 && $totalmoney >= $ship['goodsmoney']) {
            return 0;
        }

        if ($weight && $weight > $ship['firstunit']) {
            $shipmoney = 0;
            $tmp_exp   = trim(str_replace('w', $weight, $ship['exp']));
            eval("\$shipmoney = $tmp_exp;");
            return $shipmoney;
        } else {
            if (isset($ship['firstunit_price'])) {
                return $ship['firstunit_price'];
            } else {
                return $ship['firstunit_area_price'];
            }
        }
    }


    /**
     * 获取配送方式详情
     * @param $filter
     * @return array
     */
    public function getInfo($filter)
    {
        $data = $this->where($filter)->find();
        if ($data) {
            $data = $data->toArray();
            if ($data['type'] == self::TYPE_PART) {
                $data['area_fee'] = json_decode($data['area_fee'], true);

                foreach ((array)$data['area_fee'] as $key => &$val) {
                    if ($val['area_value']) {
                        $area_value = json_decode($val['area_value'], true);
                        $area_html  = '';
                        foreach ($area_value as $akey => $aval) {
                            if ($aval['pid'] <= 0) {
                                $area_html .= $aval['name'] . ',';
                            }
                        }
                        $area_html                           = substr($area_html, 0, -1);
                        $data['area_fee'][$key]['area_html'] = $area_html;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 格式转换
     * @param $list
     * @return mixed
     */
    public function tableFormat($list)
    {
        if ($list) {
            foreach ($list as $key => $val) {
                if ($list[$key]['is_def'] == self::IS_DEF_YES) {
                    $list[$key]['is_def'] = "是";
                } else {
                    $list[$key]['is_def'] = "否";
                }
                if ($list[$key]['free_postage'] == self::FREE_POSTAGE_YES) {
                    $list[$key]['free_postage'] = "是";
                } else {
                    $list[$key]['free_postage'] = "否";
                }
                if ($list[$key]['has_cod'] == self::HAS_COD_YES) {
                    $list[$key]['has_cod'] = "是";
                } else {
                    $list[$key]['has_cod'] = "否";
                }

                switch ($list[$key]['status']) {
                    case self::STATUS_YES:
                        $list[$key]['status'] = "启用";
                        break;
                    case self::STATUS_NO:
                        $list[$key]['status'] = "停用";
                        break;
                    default :
                        $list[$key]['status'] = "启用";
                        break;
                }
            }
        }
        return parent::tableFormat($list); // TODO: Change the autogenerated stub
    }

    /**
     * 增加商户过滤
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where           = [];
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id' => 'desc'];
        return $result;
    }

}