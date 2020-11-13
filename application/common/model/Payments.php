<?php
namespace app\common\model;

class Payments extends Common
{

    const PAYMENT_STATUS_YES = 1;      //启用
    const PAYMENT_STATUS_NO  = 2;      //禁用

    const PAYMENT_ONLINE = 1;           //线上支付
    const PAYMENT_OFFLINE = 2;          //线下支付


    //判断支付方式是否可用
    public function getPayment($code,$status = false,$is_online = false)       //旧有的方法enable修改而成
    {
        $where[] = ['code', 'eq', $code];
        if($status){
            $where[] = ['status', 'eq', $status];
        }
        if($is_online){
            $where[] = ['is_online', 'eq', $is_online];
        }
        return $this->where($where)->find();
    }


    /**
     *  添加支付方式
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function editData($data)
    {
        $result = error_code(10004);
        $data['params'] = json_encode($data['params']);
        if(isset($data['status']) && $data['status'] == 1){
        }else{
            $data['status'] = 2;
        }
        //如果已经添加，那么就给他更新
        $where['code'] = $data['code'];

        if($this->allowField(true)->save($data,$where)){
            $result['status'] = true;
            $result['msg'] = '保存成功';
        }
        return $result;

    }
    /**
     *
     * 快捷更改状态
     * @param $id
     * @param $status
     * @return array
     */
    public function changeStatus($id,$status)
    {
        $result = ['status'=>true,'msg'=>'','data'=>''];
        $where['id'] = $id;

        switch($status)
        {
            case 'true':
                $this->save(['status'=>self::PAYMENT_STATUS_YES],$where);
                $result['msg'] = '已'.config('params.payments')['status'][self::PAYMENT_STATUS_YES];
                break;
            case 'false':
                $this->save(['status'=>self::PAYMENT_STATUS_NO],$where);
                $result['msg'] = '已'.config('params.payments')['status'][self::PAYMENT_STATUS_NO];
                break;
            default:
                $result = error_code(10022);
                break;
        }

        return $result;
    }


    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $result['where'] = [];
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


    protected function tableFormat($list)
    {
        foreach($list as $key => $val)
        {
            $list[$key]['is_online'] = config('params.payments')['is_online'][$val['is_online']];
        }
        return $list;
    }

    public function getList($enable = 1){

        $where = [];
        if($enable != 0){
            $where['status'] = $enable;
        }

        $list = $this
            ->field('code,name,sort,memo,status,is_online')
            ->where($where)->order('sort asc')->select();
        return $list;

    }


}