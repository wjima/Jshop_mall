<?php
/**
 * Created by PhpStorm.
 * Date: 2018/1/16 0016
 * Time: 11:16
 */

namespace app\common\model;

use app\common\model\Promotion;

class Coupon extends Common
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';

    const USED_NO = 1;      //未使用
    const USED_YES = 2;     //已使用

    /**
     * 返回表格数据类型
     * @param $post
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);

        $list = $this->with('user,promotion')->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }

    /**
     * 用户领取优惠券 插入数据
     * @param $seller_id
     * @param $user_id
     * @param $promotion_id
     * @return array
     */
    public function addData($user_id,$promotion_id)
    {
        $result = ['status'=>false,'msg'=>'领取失败','data'=>'' ];

        $data = [
            'coupon_code' => $this->generate_promotion_code()[0],
            'promotion_id' => $promotion_id,
            'user_id' => $user_id
        ];
        if($this->allowField(true)->save($data))
        {
            $result['status'] = true;
            $result['msg'] = '领取成功';
        }

        return $result;
    }

    /**
     * 删除用户领取的优惠券
     * @param $coupon_code
     * @return array
     */
    public function del($coupon_code)
    {
        $result = ['status'=>false,'msg'=>'删除失败','data'=>''];
        if ($this->where('coupon_code',$coupon_code)->delete())
        {
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }
        return $result;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['promotion_id']) && $post['promotion_id'] != ""){
            $where[] = ['promotion_id', 'eq', $post['promotion_id']];
        }
        if(isset($post['is_used']) && $post['is_used'] != ""){
            $where[] = ['is_used','eq',$post['is_used']];
        }
        if(isset($post['date']) && $post['date'] != ""){
            $theDate = explode(' 到 ',$post['date']);
            if(count($theDate) == 2){
                $where[] = ['utime', ['EGT',strtotime($theDate[0])],['ELT',strtotime($theDate[1])],'and'];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['utime DESC'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $key => $val) {
            $list[$key]['utime'] = date('Y-m-d H:i:s',$val['utime']);
            if ($val['is_used'] == self::USED_YES)
            {
                $list[$key]['used_name'] = model('User')->where('id',$val['used_id'])->value('nickname');
            }
            $list[$key]['is_used'] = config('params.coupon')['is_used'][$val['is_used']];
        }
        return $list;
    }

    /**
     * 关联user表
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne('User','id','user_id')->bind('nickname');
    }

    /**
     * 关联 促销表
     * @return \think\model\relation\HasOne
     */
    public function promotion()
    {
        return $this->hasOne('Promotion','id','promotion_id')->bind('name,stime,etime,status');
    }

    /**
     * 获取 我的优惠券
     * @param $user_id
     * @param string $promotion_id
     * @param string $display //all显示全部 no_used没有使用过的 yes_used使用过的
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMyCoupon($user_id, $promotion_id = '', $display = 'all')
    {
        $where[] = ['user_id','eq',$user_id];
        if($display == 'no_used')
        {
            $where[] = ['is_used', 'eq', self::USED_NO];
        }

        if($promotion_id)
        {
            $where[] = ['promotion_id','eq',$promotion_id];
        }
        $list = $this::with('promotion')->where($where)->select();
        if(!$list->isEmpty()){
            foreach($list as $k =>$v){
                if($v['etime']<time()){
                    $list[$k]['overdue'] = true;
                }else{
                    $list[$k]['overdue'] = false;
                }
                $list[$k]['stime'] = date('Y-m-d',$v['stime']);
                $list[$k]['etime'] = date('Y-m-d',$v['etime']);
            }
        }
        return $list;
    }

    /**
     * 根据优惠券编码取优惠券的信息,并判断是否可用
     * @param $code //优惠券号码,多个优惠券的话，用个英文逗号分割
     * @param bool $check 校验是否是可用的
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function codeToInfo($code,$check=false)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];

        $code_arr = explode(',',$code);

        foreach($code_arr as $v){
            $where['coupon_code'] = $v;
            $info = $this::with('promotion')->where($where)->find();
            if($info){
                if($check){
                    //判断规则是否开启
                    $promotionModel = new Promotion();
                    if($info['status'] != $promotionModel::STATUS_OPEN){
                        return error_code(15012);       //优惠券规则不是可用状态
                    }
                    //判断优惠券规则是否到达开始时间
                    if($info['stime']>time()){
                        return error_code(15010);
                    }
                    //判断优惠券规则是否已经到结束时间了，也就是是否过期了
                    if($info['etime']<time()){
                        return error_code(15011);
                    }
                    //判断是否已经使用过了
                    if($info['is_used'] != self::USED_NO){
                        return error_code(15013);
                    }
                    //判断此类优惠券是否已经使用过,防止一类优惠券使用多张
                    foreach($result['data'] as $j){
                        if($j['promotion_id'] == $info['promotion_id']){
                            return error_code(15015);
                        }
                    }

                }
                $result['data'][$v] = $info;
            }else{
                return error_code(15009);
            }
        }
        $result['status'] = true;
        return $result;
    }

    /**
     * 生成优惠券code 方法
     * @param int $no_of_codes //定义一个int类型的参数 用来确定生成多少个优惠码
     * @param string $exclude_codes_array //定义一个exclude_codes_array类型的数组
     * @param int $code_length //定义一个code_length的参数来确定优惠码的长度
     * @return array //返回数组
     */
    private function generate_promotion_code($no_of_codes=1,$exclude_codes_array='',$code_length =10)
    {
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $promotion_codes = [];  //这个数组用来接收生成的优惠码
        for($j = 0 ; $j < $no_of_codes; $j++)
        {
            $code = "";
            for ($i = 0; $i < $code_length; $i++)
            {
                $code .= $characters[mt_rand(0, strlen($characters)-1)];
            }
            //如果生成的6位随机数不再我们定义的$promotion_codes函数里面
            if(!in_array($code,$promotion_codes)) {
                if(is_array($exclude_codes_array)){
                    if(!in_array($code,$exclude_codes_array)){       //排除已经使用的优惠码
                        $promotion_codes[$j] = $code;       //将生成的新优惠码赋值给promotion_codes数组
                    }
                    else {
                        $j--;
                    }
                } else {
                    $promotion_codes[$j] = $code;//将优惠码赋值给数组
                }
            }else{
                $j--;
            }
        }
        return $promotion_codes;
    }

    /**
     * 删除核销多个优惠券
     * @param $coupon_code
     * @param $seller_id
     * @param $user_id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function usedMultipleCoupon($coupon_code, $user_id)
    {
        $res = $this->codeToInfo($coupon_code, true);
        if(!$res['status'])
        {
            return $res;
        }

        $where[] = ['coupon_code', 'in', $coupon_code];
        $where[] = ['is_used', 'eq', 1];

        $data['is_used'] = 2;
        $data['used_id'] = $user_id;

        $result = $this->save($data, $where);
        if($result)
        {
            $return_data = [
                'status' => true,
                'msg' => '核销使用优惠券成功',
                'data' => $coupon_code
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '核销使用优惠券失败',
                'data' => $coupon_code
            ];
        }
        return $return_data;
    }
}