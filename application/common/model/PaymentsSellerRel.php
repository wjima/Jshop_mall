<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/16 0016
 * Time: 18:04
 */
namespace app\common\model;

use think\Validate;

class PaymentsSellerRel extends Common
{

    const PAYMENT_STATUS_YES = 1;      //启用
    const PAYMENT_STATUS_NO  = 2;      //禁用

    protected $rule     =   [

        'payment_code'  =>  'require',
        'status'       =>  'in:1,2'
    ];

    protected $msg      =   [

        'payment_code'      =>  '请选择支付方式',
        'params'            =>  '支付配置参数必须填写',
        'status.in'        =>  '支付状态错误'
    ];



    /**
     *  数据验证
     * User:sin
     * @param array $data
     * @return array
     */
    public function checkData($data = array())
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status' => true, 'msg'   => '', 'data'  => ''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
            return $result;
        }
        //这里还可以校验各个支付方式的必填项


        return $result;
    }

    public function getList($seller_id, $enable = 1){

        $where['psr.seller_id'] = $seller_id;
        if($enable != 0){
            $where['psr.status'] = $enable;
            $where['p.status'] = $enable;
        }

        $list = $this->alias('psr')
            ->field('p.*,psr.seller_id,psr.sort')
            ->leftJoin('payments p', 'psr.payment_code = p.code')
            ->where($where)->select();
        return $list;

    }

    /**
     *  添加支付方式
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function addData($data)
    {
        $res = $this->checkData($data);
        if($res['status'])
        {
            $data['params'] = json_encode($data['params']);
            if(isset($data['status']) && $data['status'] == 1){

            }else{
                $data['status'] = 2;
            }
            //验证是否已经添加了该支付方式
            if($this->duplication($data['seller_id'],$data['payment_code']))
            {
                $this->allowField(true)->save($data);
                $res['msg'] = '保存成功';
            }else{
                //如果已经添加，那么就给他更新
                $where['seller_id'] = $data['seller_id'];
                $where['payment_code'] = $data['payment_code'];
                $this->allowField(true)->save($data,$where);
                $res['msg'] = '保存成功';
            }
            return $res;
        }
        return $res;
    }


    /**
     *  修改支付方式
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function saveData($data = array())
    {
        $res = $this->checkData($data);
        if($res['status'])
        {
            $this->allowField(true)->save($data,['id'=>$data['id']]);
            $res['msg'] = '保存成功';
            return $res;
        }
        return $res;
    }


    /**
     *  删除支付方式
     * User:sin
     * @param $id
     * @return array
     *
     */
    public function del($id,$seller_id = "")
    {
        $where['id'] = $id;
        if($seller_id != ""){
            $where['seller_id'] = $seller_id;
        }
        $this->where($where)->delete();
        $result = ['status' => true,'msg' => '删除成功','data'=> ''];
        return $result;
    }


    /**
     *
     * 快捷更改状态
     * @param $id
     * @param $status
     * @return array
     */
    public function changeStatus($id,$status,$seller_id = "")
    {
        $result = ['status'=>true,'msg'=>'','data'=>''];
        $where['id'] = $id;
        if($seller_id != ""){
            $where['seller_id'] = $seller_id;
        }
        switch($status)
        {
            case 'true':
                $this->save(['status'=>self::PAYMENT_STATUS_YES],$where);
                $result['msg'] = '已'.config('params.payments_seller_rel')['status'][self::PAYMENT_STATUS_YES];
                break;
            case 'false':
                $this->save(['status'=>self::PAYMENT_STATUS_NO],$where);
                $result['msg'] = '已'.config('params.payments_seller_rel')['status'][self::PAYMENT_STATUS_NO];
                break;
            default:
                $result['status'] = false;
                $result['msg'] = '非法操作';
                break;
        }

        return $result;
    }


    /*
     * 判断是否已经添加该支付方式  勿重复添加
     * */
    public function duplication($seller_id,$code)
    {
        $where[] = ['seller_id','eq',$seller_id];
        $where[] = ['payment_code','eq',$code];
        if($this->where($where)->find())
        {
            return false;
        }
        return true;
    }

    /*
     * 获取支付配置信息
     * */
    public function getPaymentInfo($id = 0)
    {
        $payment = $this->alias('s')
            ->field('s.id,s.payment_code,s.params,s.status,p.name')
            ->join('payments p','s.payment_code = p.code')
            ->where('s.id ='.$id)
            ->find();
        return $payment;
    }

    /**
     *
     *  总后台查询条件
     * User:sin
     * @param $post
     * @return array
     *
     */
//    protected function _hasWhere($post)
//    {
//        $hasWhere = [];
//        if(isset($post['seller']) && $post['seller'])
//        {
//            if(isset($post['seller']['name']) && $post['seller']['name'])
//            {
//                $hasWhere = ['seller_name' =>$post['seller']['name']];
//            }
//        }
//        return $hasWhere;
//    }

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this::with('payments,sellerInfo')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();

        return $re;
    }


    /**
     *
     *  where 搜索条件
     * User:sin
     * @param $post
     * @return mixed
     *
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['code']) && $post['code'] != ""){
            $where[] = ['payment_code', 'eq', $post['code']];
        }
        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status','eq',$post['status']];
        }
        if(isset($post['seller_id']) && $post['seller_id'] != "")
        {
            $where[] = ['seller_id','eq',$post['seller_id']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort asc'];
        return $result;
    }

    /*
     * 关联订单支付方式表
     * */
    public function payments()
    {
        return $this->hasOne('Payments','code','payment_code')->bind([
            'name'=>'name',
            'memo',
            'is_online'
        ]);
    }
    public function sellerInfo()
    {
        return $this->hasOne('Seller','id','seller_id')->bind([
            'seller_name'
        ]);
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
//        $paymentsModel = new Payments();

        foreach($list as $key => $val)
        {
            $list[$key]['is_online'] = config('params.payments_seller_rel')['is_online'][$val['is_online']];;
//            //$list[$key]['status'] = config('params.payments_seller_rel')['status'][$val['status']];
//
//            $paymentsInfo = $paymentsModel->where(['code'=>$val['payment_code']])->find();
//            if($paymentsInfo){
//                $list[$key]['name'] =$paymentsInfo['name'];
//                $list[$key]['memo'] = $paymentsInfo['memo'];
//                $list[$key]['is_online'] = config('params.payments_seller_rel')['is_online'][$paymentsInfo['is_online']];;
//            }else{
//                unset($list[$key]);
//            }
        }
        return $list;
    }
}