<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
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
        $exp = '{{w-0}-0.4}*{{{' . $firstunit . '-w}-0.4}+1}*' . $firstunit_price . '*1.00+ {{w-' . $firstunit . '}-0.6}*[(w-' . $firstunit . ')/' . $continueunit . ']*' . $continueunit_price . '*1.00';
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
        $result = [
            'status' => false,
            'msg'    => '添加失败',
            'data'   => '',
        ];
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
            $result['msg'] = '物流公司不存在';
            return $result;
        }
        $data['logi_name'] = $logistics['logi_name'];

        if ($this->insert($data)) {
            $result['status'] = true;
            $result['msg']    = '添加成功';
        }
        return $result;
    }


    public function toSave($data = [], $id = 0)
    {
        $result = [
            'status' => false,
            'msg'    => '保存失败',
            'data'   => '',
        ];
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
            $result['msg'] = '物流公司不存在';
            return $result;
        }
        $data['logi_name'] = $logistics['logi_name'];

        if ($this->save($data, ['id' => $id])) {
            $result['status'] = true;
            $result['msg']    = '保存成功';
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
     * @return string
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 15:31
     *      $shiModel = new ShipModel();
     *      $shiModel->getShipCost(1,1,1600);
     *      die();
     */
    public function getShipCost($area_id = 0, $weight = 0)
    {
        $postfee = '0.00';
        //先判断是否包邮
        $def = $this->where(['is_def' => self::IS_DEF_YES,'status' => self::STATUS_YES])->find();
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
                    $area = explode(',', $val['area']);
                    if (in_array($area_id, $area)) {
                        $isIn    = true;
                        $total   = self::cal_fee($val['exp'], $weight, 0, $val['firstunit_area_price'], $val['continueunit_area_price']);
                        $postfee = getMoney($total);
                        break;
                    }
                }
                if (!$isIn) {
                    $total   = self::cal_fee($def['exp'], $weight, 0, $def['firstunit_price'], $def['continueunit_price']);
                    $postfee = getMoney($total);
                }
            } else {
                $total   = self::cal_fee($def['exp'], $weight, 0, $def['firstunit_price'], $def['continueunit_price']);
                $postfee = getMoney($total);
            }
        } else {
            //todo 地区 配送费以后再优化
            $total   = self::cal_fee($def['exp'], $weight, 0, $def['firstunit_price'], $def['continueunit_price']);
            $postfee = getMoney($total);
        }

        return $postfee;

    }

    /**
     * 根据地区获取配送方式
     * todo 地区判断以后再加
     * @param int $area_id
     * @return array|bool|null|\PDOStatement|string|\think\Model
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-01 15:36
     */
    public function getShip($area_id = 0)
    {
        $def = $this->where(['is_def' => self::IS_DEF_YES, 'status' => self::STATUS_YES])->find();
        if (!$def) {
            $def = $this->where(['status' => self::STATUS_YES])->find();
            if (!$def) {//没有配送方式，返回0
                return false;
            }
        }
        return $def;
    }

    //配送公式验算function
    static function cal_fee($exp, $weight, $totalmoney, $first_price, $continue_price, $defPrice = 0)
    {
        if ($str = trim($exp)) {
            $dprice         = 0;
            $weight         = $weight + 0;
            $totalmoney     = $totalmoney + 0;
            $first_price    = $first_price + 0;
            $continue_price = $continue_price + 0;
            $str            = str_replace("[", "self::_getceil(", $str);
            $str            = str_replace("]", ")", $str);
            $str            = str_replace("{", "self::_getval(", $str);
            $str            = str_replace("}", ")", $str);

            $str = str_replace("w", $weight, $str);
            $str = str_replace("W", $weight, $str);
            $str = str_replace("fp", $first_price, $str);
            $str = str_replace("cp", $continue_price, $str);
            $str = str_replace("p", $totalmoney, $str);
            $str = str_replace("P", $totalmoney, $str);
            eval("\$dprice = $str;");
            if ($dprice === 'failed') {
                return $defPrice;
            } else {
                return $dprice;
            }
        } else {
            return $defPrice;
        }
    }


    static function mydate($f, $d = null)
    {
        global $_dateCache;
        if (!$d) $d = time();
        if (!isset($_dateCache[$d][$f])) {
            $_dateCache[$d][$f] = date($f, $d);
        }
        return $_dateCache[$d][$f];
    }

    static function _getval($expval)
    {
        $expval = trim($expval);
        if ($expval !== '') {
            eval("\$expval = $expval;");
            if ($expval > 0) {
                return 1;
            } else if ($expval == 0) {
                return 1 / 2;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    static function _getceil($expval)
    {
        if ($expval = trim($expval)) {
            eval("\$expval = $expval;");
            if ($expval > 0) {
                return ceil($expval);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    static function steprange($start, $end, $step)
    {
        if ($end - $start) {
            if ($step < 2) $step = 2;
            $s = ($end - $start) / $step;
            $r = [floor($start) - 1];

            for ($i = 1; $i < $step; $i++) {
                $n     = $start + $i * $s;
                $f     = pow(10, floor(log10($n - $r[$i - 1])));
                $r[$i] = round($n / $f) * $f;
                $q[$i] = [$r[$i - 1] + 1, $r[$i]];
            }
            $q[$i] = [$r[$step - 1] + 1, ceil($end)];
            return $q;
        } else {
            if (!$end) $end = $start;
            return [[$start, $end]];
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
                foreach ($data['area_fee'] as $key => &$val) {
                    if ($val['area_value']) {
                        $area_value = json_decode($val['area_value'], true);
                        $area_html  = '';
                        foreach ($area_value as $akey => $aval) {
                            if ($aval['pid'] == '0') {
                                $area_html .= $aval['name'] . ',';
                            }
                        }
                        $area_html        = substr($area_html, 0, -1);
                        $val['area_html'] = $area_html;
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
        $where = [];
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id' => 'desc'];
        return $result;
    }

}