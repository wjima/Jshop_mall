<?php

namespace addons\FreePackage\model;

use app\common\model\Common;
use app\common\model\Goods;
use app\common\model\Manage;
use app\common\model\Products;
use think\Db;

class FreePackage extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const STATUS_OPEN = 1;  // 开启
    const STATUS_DISABLE = 2;  // 关闭

    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('jshop.page_limit');
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

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['order_id']) && $post['order_id'] != "") {
            $where[] = ['order_id', 'eq', $post['order_id']];
        }
        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id' => 'desc']; //默认最新添加靠前，排序越小越靠前
        return $result;
    }

    protected function tableFormat($list)
    {
        foreach ($list as $k => &$v) {
            $v['ctime'] = getTime($v['ctime']);
            $v['utime'] = getTime($v['utime']);
//            $v['status_text'] = $v['status'] == self::STATUS_OPEN ? '开启' : '关闭';
        }
        return $list;
    }


    public function del($id)
    {
        $info = $this->get($id);
        if (!$info) return error_code(10002);
        if (!$info->delete()) {
            return error_code(10004);
        }
        $model = new FreePackageItems();
        $model->where('package_id', $id)->delete();
        return [
            'status' => true,
            'data' => [],
            'msg' => '成功'
        ];
    }

    // 处理套餐商品
    public function comboInfo(&$list, $user_id){
        $result = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];
        // 验证活动是否开启
        $settings = getAddonsConfig("FreePackage")['setting'];
        if($settings['combo_status'] != self::STATUS_OPEN){
            $result['msg'] = '活动暂无开启！';
            return $result;
        }

        // 验证是否满足免单数量
        $nums = 0;
        foreach($list as $v){
            if($v['is_select'] == 1){   // 计算数量
                $nums =+ $nums + $v['nums'];
            }
        }

        // 验证活动规定最少商品数量
        if($settings['combo_num'] > $nums){
            $result['msg'] = "套餐活动最少".$settings['combo_num']."件起购！";
            return $result;
        }

        $small = 0;
        $cprice = 999999999999999999999999999999999999999999999999999999999999999;
        foreach($list as $k=>$v){
            if($v['is_select'] == 1 && $cprice > $v['products']['price']){
                $small = $k;
                $cprice = $v['products']['price'];
            }
        }

        $list[$small]['products']['promotion_list'] = [
            '免单商品'
        ];
        $list[$small]['products']['amount'] = $list[$small]['products']['price'];
        $list[$small]['products']['is_free'] = 1;// 标识免单商品


        $result['status'] = true;
        $result['data'] = $list;
        return $result;
    }

    // 添加日志
    public function setLog($order){
        if($order['order_type'] != 8){
           return true;
        }
        $data = [];
        $data['user_id'] = $order['user_id'];
        $data['order_id'] = $order['order_id'];
        $data['order_amount'] = $order['order_amount'];
        $data['status'] = 1;    // 已付款
        $data['mobile'] = get_user_info($order['user_id']);
        $data['mobile'] = get_user_info($order['user_id']);
        $this->save($data);
    }

}
