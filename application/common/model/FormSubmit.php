<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Db;

class FormSubmit extends common
{

    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';
    protected $createTime = 'ctime';


    //表单支付状态
    const FORM_PAY_STATUS_YES = 1;//已支付
    const FORM_PAY_STATUS_NO = 2;//未支付
    //表单状态
    const FORM_STATUS_UNTREATED = 2;//未处理
    const FORM_STATUS_PROCESSED = 1;//未处理


    public function add($data)
    {
        return $this->save($data);
    }

    public function tableFormat($list)
    {
        $list = $list->toArray();
        foreach ((array)$list as $key => $value) {
            $list[$key]['status']     = config('params.form')['submit_status'][$value['status']];
            $list[$key]['pay_status'] = config('params.form')['submit_pay_status'][$value['pay_status']];
            $list[$key]['ctime']      = ($value['ctime'] > 0) ? date('Y-m-d H:i:s', $value['ctime']) : '';
            $list[$key]['utime']      = ($value['utime'] > 0) ? date('Y-m-d H:i:s', $value['utime']) : '';
            if ($value['user_id']) {
                $list[$key]['user_name'] = get_user_info($value['user_id'], 'showname');
            } else {
                $list[$key]['user_name'] = '';
            }
        }
        return parent::tableFormat($list);
    }

    /***
     * 表单支付
     * @param $id
     * @param string $payment_code
     * @return array
     */
    public function pay($id, $payment_code = '')
    {
        $return_data = [
            'status' => true,
            'msg'    => '',
            'data'   => [],
        ];
        if (!$id) {
            return error_code(18012);
        }
        $this->update(['pay_status' => self::FORM_PAY_STATUS_YES], ['id' => $id, 'pay_status' => self::FORM_PAY_STATUS_NO]);
        $return_data['status'] = true;
        $return_data['msg']    = '支付成功';
        return $return_data;
    }


    /**
     * 表单提交统计
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function statisticsByFormid($id = 0)
    {
        $num = 7;
        $day = date('Y-m-d', strtotime('-' . $num . ' day'));

        $where[] = ['form_id', 'eq', $id];

        $res = $this->fieldRaw('DATE_FORMAT(FROM_UNIXTIME(ctime),"%Y-%m-%d") as day, count(*) as nums')
            ->where($where)
            ->whereRaw('FROM_UNIXTIME(ctime) >=' . $day)
            ->group('day')
            ->select();

        $data = get_lately_days($num, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }

    /**
     * 表单提交信息删除
     * @param $id
     * @return array|bool
     */
    public function deleteFormSubmit($id)
    {
        $result = error_code(10023);
        if (!$id) {
//            $result['msg'] = '关键参数丢失';
            return error_code(10051);
        }
        //先删除明细
        $formSubmitDetail = new FormSubmitDetail();
        Db::startTrans();
        $res = $formSubmitDetail->where(['submit_id' => $id])->delete();
        if (!$res) {
            Db::rollback();
            return $result;
        }
        $res = $this->where(['id' => $id])->delete();
        if (!$res) {
            Db::rollback();
            return $result;
        }
        Db::commit();
        $result['msg']    = '删除成功';
        $result['status'] = true;
        return $result;
    }


    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['form_id']) && $post['form_id']) {
            $where[] = ['form_id', '=', $post['form_id']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['ctime desc'];
        return $result;
    }

    /**
     * 根据form提交id 获取表单提交详情
     * @param int $id
     * @return array
     */
    public function getDetail($id = 0)
    {
        $result = error_code(10025);
        if (!$id) {
//            $result['msg']    = '关键参数丢失';
//            $result['status'] = false;
            return error_code(10051);
        }
        $formSubmitInfo = $this->get($id);
        if (!$formSubmitInfo) {
//            $result['status'] = false;
//            $result['msg']    = '无此提交';
            return error_code(18011);
        }
        $formModel = new Form();
        $formInfo  = $formModel->getFormInfo($formSubmitInfo['form_id']);
        if (!$formInfo['status']) {
            $result['status'] = false;
            $result['msg']    = $formInfo['msg'];
            return $result;
        }
        $formInfo['data']['user_id']    = $formSubmitInfo['user_id'];
        $formInfo['data']['pay_status'] = $formSubmitInfo['pay_status'];
        $formInfo['data']['money']      = $formSubmitInfo['money'];
        $formInfo['data']['status']     = $formSubmitInfo['status'];
        $formInfo['data']['feedback']   = $formSubmitInfo['feedback'];
        $formInfo['data']['ctime']      = $formSubmitInfo['ctime'];
        $formInfo['data']['utime']      = $formSubmitInfo['utime'];
        $submitDetail                   = new FormSubmitDetail();

        if (isset($formInfo['data']['items']) && $formInfo['data']['items']) {
            foreach ($formInfo['data']['items'] as $key => $val) {
                $where   = [];
                $where[] = ['submit_id', '=', $id];
                $where[] = ['form_id', '=', $formSubmitInfo['form_id']];
                $where[] = ['form_item_id', '=', $val['id']];
                $detail  = $submitDetail->where($where)->find();

                if ($detail) {
                    if ($val['type'] == 'area') {
                        $formInfo['data']['items'][$key]['svalue'] = get_area($detail['form_item_value']);
                    } elseif ($val['type'] == 'image') {
                        $image = [];
                        $image = explode(',', $detail['form_item_value']);
                        foreach ($image as $k => $v) {
                            $formInfo['data']['items'][$key]['svalue'][$k] = _sImage($v);
                        }
                    } elseif ($val['type'] == 'goods') {
                        $details = $submitDetail->where($where)->select();
                        $svalue  = [];
                        if (!$details->isEmpty()) {
                            foreach ((array)$details->toArray() as $k => $v) {
                                $svalue[] = $v['form_item_name'] . 'x' . $v['form_item_value'];
                            }
                        }
                        $formInfo['data']['items'][$key]['svalue'] = $svalue;//todo 字段是商品时处理
                    } else {
                        $formInfo['data']['items'][$key]['svalue'] = $detail['form_item_value'];
                    }
                } else {
                    $formInfo['data']['items'][$key]['svalue'] = '';
                }
            }
        }
        $result['data']   = $formInfo['data'];
        $result['msg']    = '获取成功';
        $result['status'] = true;
        return $result;
    }

}