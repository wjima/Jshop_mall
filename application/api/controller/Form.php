<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Area;
use app\common\model\BillPayments;
use app\common\model\FormItem;
use app\common\model\FormSubmit;
use app\common\model\FormSubmitDetail;
use app\common\model\Products;
use app\common\model\UserToken;
use Request;
use app\common\model\Form as FormModel;
use think\Db;

/***
 * 表单接口
 */
class Form extends Api
{

    /**
     * 获取活动商品详情
     * @return array
     */
    public function getFormDetial()
    {
        $return_data = [
            'status' => false,
            'msg' => error_code(18001, true),
            'data' => [],
        ];
        $id          = input('id/d', 0);
        $token       = input('token', ''); //token值 会员登录后传

        if (!$id) {
            // $return_data['msg']    = error_code(10051,true);
            // $return_data['status'] = false;
            return error_code(10051);
        }
        $formModel = new FormModel();
        $info      = $formModel->getFormInfo($id);
        if (!$info['status']) {
            $return_data['msg'] = $info['msg'];
            return $return_data;
        }
        if ($info['data']['is_login'] == $formModel::NEED_LOGIN && !$token) {
            $return_data['msg']  = error_code(14006, true);
            $return_data['data'] = [
                'need_login' => true,
            ];
            return $return_data;
        }
        //检查过期时间
        if (isset($info['data']['end_date']) && $info['data']['end_date'] != 0) {
            if (time() > strtotime($info['data']['end_date'])) {
                $return_data['msg'] = error_code(18002, true);
                return $return_data;
            }
        }
        $return_data['msg']    = '获取成功';
        $return_data['data']   = $info['data'];
        $return_data['status'] = true;
        return $return_data;
    }

    /**
     * 提交表单
     * @return array
     * @throws \Exception
     */
    public function addSubmit()
    {
        $return_data = [
            'status' => false,
            'msg' => error_code(18001, true),
            'data' => [],
        ];
        $id          = input('id/d', 0);
        $token       = input('token', '');
        $data        = input('param.', [], 'remove_xss');
        $formModel = new FormModel();
        $form      = $formModel->getFormInfo($id);
        if (!$form['status']) {
            return $return_data;
        }
        if ($form['data']['is_login'] == $formModel::NEED_LOGIN && !$token) {
            $return_data['msg']  = error_code(14006, true);
            $return_data['data'] = [
                'need_login' => true,
            ];
            return $return_data;
        }
        //检查过期时间
        if (isset($form['data']['end_date']) && $form['data']['end_date'] != 0) {
            if (time() > strtotime($form['data']['end_date'])) {
                $return_data['msg'] = error_code(18002, true);
                return $return_data;
            }
        }

        $formItem     = new FormItem();
        $formSubmit   = new FormSubmit();
        $subimtDetail = new FormSubmitDetail();

        $money = 0;
        //todo 金额促销
        if ($form['data']['type'] == $formModel::FORM_TYPE_PAY) { //付款码
            $items = $formItem->where(['form_id' => $id, 'type' => 'money'])->select();
            if (!$items->isEmpty()) {
                foreach ((array)$items->toArray() as $key => $val) {
                    $money += isset($data['data'][$val['id']]) ? (float)($data['data'][$val['id']]) : 0;
                }
            }
        }
        $user_id = 0;
        if ($token) {
            $userTokenModel = new UserToken();
            $result         = $userTokenModel->checkToken($token);
            if (!$result['status']) {
                return error_code(14007);
            } else {
                $user_id = $result['data']['user_id'];
            }
        }
        //判断提交次数
        if ($form['data']['times'] && $token) {
            $count = $formSubmit->where([['user_id', '=', $user_id], ['form_id', '=', $id]])->count();
            if ($count >= $form['data']['times']) {
                // $return_data['msg'] = error_code(18003, true);
                return error_code(18003);
            }
        }
        $formData = [
            'form_id'    => $id,
            'form_name'  => $form['data']['name'],
            'user_id'    => $user_id,
            'money'      => $money,
            'pay_status' => $formSubmit::FORM_PAY_STATUS_NO,
            'status'     => $formSubmit::FORM_STATUS_UNTREATED,
            'ip'         => get_client_ip(0)
        ];

        Db::startTrans();
        if ($formSubmit->add($formData) === false) {
            $return_data['msg'] = error_code(18005, true);
            Db::rollback();
            return $return_data;
        }
        $formSubmitId = $formSubmit->getLastInsID();
        unset($data['id'], $data['token'], $data['method']);
        if (isset($data['data']) && $data['data']) {
            $item     = [];
            $tempData = [];
            foreach ($data['data'] as $key => $value) {
                if (stripos($key, '_') !== false) {
                    list($newKey, $index) = explode("_", $key);
                    $tempData[$newKey][] = $value;
                } else {
                    $tempData[$key] = $value;
                }
            }
            $productModel = new Products();
            foreach ($tempData as $key => $value) {
                $formitem = $formItem->where(['id' => $key])->find();
                if (!$formItem->validateField($formitem, $value)) {
                    // $return_data['msg'] =  error_code(18004, true, $formitem['name']);   //格式错误，请重新输入
                    Db::rollback();
                    return error_code(18004, false, $formitem['name']);
                }
                if ($formitem['required'] == $formItem::REQUIRED_YES && !$value) {
                    // $return_data['msg'] = error_code(18006, true, $formitem['name']);    //'请输入' . $formitem['name']
                    Db::rollback();
                    return error_code(18006, false, $formitem['name']);
                }
                //地区
                if ($formitem['type'] == 'area' && $value) {
                    if (!is_array($value)) {
                        $value = explode(' ', $value);
                    }
                    $county_name   = $value[2];
                    $city_name     = $value[1];
                    $province_name = $value[0];
                    $areaModel     = new Area();
                    unset($value);
                    $area_id = $areaModel->getThreeAreaId($county_name, $city_name, $province_name, '0');
                    $value   = $area_id;
                } elseif ($formitem['type'] == 'image' && $value) { //处理图片
                    $value = implode(',', (array)$value);
                } elseif ($formitem['type'] == 'checbox' && $value) { //处理复选
                    $value = implode(',', (array)$value);
                }
                if ($formitem['type'] == 'goods' && $value) {
                    foreach ($value as $k => $v) {
                        $productData = $productModel->getProductInfo($v['productId']);
                        if (!$productData['status']) {
                            // $return_data['msg'] = error_code(12501, true);
                            Db::rollback();
                            return error_code(12501);
                        }
                        $product        = $productData['data'];
                        $form_item_name = ($product['spes_desc']) ? $product['spes_desc'] . ':' . $product['sn'] : $product['sn'];

                        $item[] = [
                            'submit_id'       => $formSubmitId,
                            'form_id'         => $id,
                            'form_item_id'    => $formitem['id'],
                            'form_item_name'  => $form_item_name,
                            'form_item_value' => $v['nums'],
                        ];
                        $money += $product['price'] * $v['nums'];
                    }
                } else {
                    $item[] = [
                        'submit_id'       => $formSubmitId,
                        'form_id'         => $id,
                        'form_item_id'    => $formitem['id'],
                        'form_item_name'  => $formitem['name'],
                        'form_item_value' => $value,
                    ];
                }
            }
            if (!$subimtDetail->saveAll($item)) {
                // $return_data['msg'] = error_code(18007, true);
                Db::rollback();
                return error_code(18007);
            }
        }
        //支付类型
        if ($form['data']['type'] == $formModel::FORM_TYPE_ORDER) { //订单类型时，更新提交表单金额
            $res = $formSubmit->save(['money' => $money], ['id' => $formSubmitId]);
        }
        $return_data['data']['id']    = $formSubmitId;
        $return_data['data']['money'] = $money;

        Db::commit();
        Hook('form_submit_after', $formData);
        $return_data['msg']    = $form['data']['return_msg'] ? $form['data']['return_msg'] : $form['data']['button_name'] . '成功';
        $return_data['status'] = true;
        return $return_data;
    }

    /**
     * 获取用户提交表单
     */
    public function getUserFormSubmit()
    {
    }

    /**
     * 获取用户提交订单详情
     */
    public function getUserFormSubmitDetial()
    {
    }
}
