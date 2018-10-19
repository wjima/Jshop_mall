<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class BillReship extends Common
{

    protected $pk = 'reship_id';

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';

    const STATUS_WAIT_SHIP = 1;       //状态，等待发货
    const STATUS_SHIPPED = 2;        //状态，已发货
    const STATUS_SUCCESS = 3;       //状态，已收货

    public function toAdd($user_id,$order_id,$aftersales_id,$aftersalesItems)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(count($aftersalesItems) == 0){
            return error_code(13209);
        }

        $data['reship_id'] = get_sn(7);
        $data['order_id'] = $order_id;
        $data['aftersales_id'] = $aftersales_id;
        $data['user_id'] = $user_id;
        $re = $this->save($data);
        if($re){
            //保存退货单明细
            foreach($aftersalesItems as $v){
                $row['reship_id'] = $data['reship_id'];
                $row['order_items_id'] = $v['order_items_id'];
                $row['goods_id'] = $v['goods_id'];
                $row['product_id'] = $v['product_id'];
                $row['sn'] = $v['sn'];
                $row['bn'] = $v['bn'];
                $row['name'] = $v['name'];
                $row['image_url'] = $v['image_url'];
                $row['nums'] = $v['nums'];
                $row['addon'] = $v['addon'];
            }
            $itemsData[] = $row;
            $billReshipItemsModel = new BillReshipItems();
            if(!$billReshipItemsModel->saveAll($itemsData)){
                return error_code(10000);
            }
        }else{
            return error_code(10000);
        }
        $result['status'] = true;
        $result['data'] = $data;
        return $result;
    }

    /**
     * 用户发送退货包裹
     * @param $user_id              用户id
     * @param $reship_id            退货单id
     * @param $logi_code            退货物流公司
     * @param $logi_no              退货订单号
     * @return array
     */
    public function sendReship($user_id,$reship_id,$logi_code,$logi_no)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where = [
            'reship_id' => $reship_id,
            'user_id' => $user_id,
            'status' => self::STATUS_WAIT_SHIP
        ];
        $info = $this->where($where)->find();
        if(!$info){
            return error_code(13211);
        }
        $data['logi_code'] = $logi_code;
        $data['logi_no'] = $logi_no;
        $data['status'] = self::STATUS_SHIPPED;

        $this->where($where)->data($data)->update();
        $result['status'] = true;
        $result['msg'] = '保存成功';
        return $result;

    }

    /**i
     * 平台客服收到退款包裹，确认收货，这时候，在业务上可以让退款员去退款了
     * @param $seller_id
     * @param $reship_id
     * @return array|\think\Config
     */
    public function confirmReship($reship_id)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where = [
            'reship_id' => $reship_id,
            'status' => self::STATUS_SHIPPED
        ];
        $info = $this->where($where)->find();
        if(!$info){
            return error_code(13211);
        }
        $data['status'] = self::STATUS_SUCCESS;

        $this->where($where)->data($data)->update();
        $result['status'] = true;
        $result['msg'] = '收货成功';
        return $result;
    }

    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $re['sql'] = $this->getLastSql();
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;


        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['reship_id']) && $post['reship_id'] != ""){
            $where[] = ['reship_id', 'like', '%'.$post['reship_id'].'%'];
        }
        if(isset($post['order_id']) && $post['order_id'] != ""){
            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
        }
        if(isset($post['logi_no']) && $post['logi_no'] != ""){
            $where[] = ['logi_no', 'like', '%'.$post['logi_no'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }

        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "status asc,utime desc";
        return $result;
    }
    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {

        foreach($list as $k => $v) {
            if($v['status']) {
                $list[$k]['status_name'] = config('params.bill_reship')['status'][$v['status']];
            }
            if($v['user_id']) {
                $list[$k]['user_id'] = format_mobile(get_user_info($v['user_id']));
            }

            if($v['ctime']) {
                $list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
            }
            if($v['ctime']) {
                $list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
            }


            if($v['logi_code']) {
                $list[$k]['logi_code'] = get_logi_info($v['logi_code']);

            }
        }
        return $list;
    }
    public function items()
    {
        return $this->hasMany('BillReshipItems','reship_id','reship_id');
    }
}
