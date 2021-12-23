<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use app\common\model\Promotion;
use think\Db;
use think\facade\Cache;

class Coupon extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
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
     * @param $user_id
     * @param $promotion_id
     * @return array
     */
    public function addData($user_id,$promotion_id)
    {
        $result = error_code(15021);
        $lock_key = 'coupon_'.$user_id.'_'.$promotion_id;

        if(!Cache::has($lock_key)){//防止高并发重复领取问题
            Cache::set($lock_key,'1',3);
            Db::startTrans();
            //取优惠券的结束时间
            $promotionModel = new Promotion();
            $where[] = ['status', '=', 1];
            $where[] = ['type', '=',2];
            $where[] = ['id','=', $promotion_id];
            $info = $promotionModel->where($where)->find();
            if(!$info){
                Db::rollback();
                return $result;
            }
            $params = json_decode($info['params'],true);
            if(isset($params['term_day']) && $params['term_day'] > 0){
                $endtime = time();
                $endtime = $endtime + $params['term_day'] * 60*60*24;
                //如果计算之后的时间大于优惠券结束的时间了，那就以优惠券的时间为准
                if ($info['etime'] < $endtime) {
                    $endtime = 0;           //就写0吧。
                }
            }else{
                $endtime = 0;
            }
            $data = [
                'coupon_code' => $this->generate_promotion_code()[0],
                'promotion_id' => $promotion_id,
                'user_id' => $user_id,
                'endtime' => $endtime
            ];
            if($this->allowField(true)->save($data))
            {
                Cache::rm($lock_key);
                $result['status'] = true;
                $result['msg'] = '领取成功';
            }else{
                Db::rollback();
                return $result;
            }
            Db::commit();
        }else{
            $result['msg'] = '请勿重复领取';
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
        $result = error_code(10023);
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
        if (isset($post['code']) && $post['code'] != "")
        {
            $where[] = ['coupon_code', 'like', '%'.$post['code'].'%'];
        }
        if(isset($post['date']) && $post['date'] != ""){
            $theDate = explode(' 到 ',$post['date']);
            if(count($theDate) == 2){
                $where[] = ['ctime', ['EGT',strtotime($theDate[0])],['ELT',strtotime($theDate[1])],'and'];
            }
        }
        if(isset($post['promotion']) && $post['promotion'] != ""){
            foreach ($where as $k => $v){
                foreach ($v as $kk => $vv){
                    if($vv == "promotion_id"){
                        $where["$k"][2] = $post['promotion'];
                    }
                }
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
            $list[$key]['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
            if($val['user_id']){
                $list[$key]['nickname'] =get_user_info($val['user_id'],'showname');
            }
            if ($val['is_used'] == self::USED_YES)
            {
                $list[$key]['used_name'] = get_user_info($val['used_id'],'showname');
            }
            if($val['endtime'] > 0){
                $list[$key]['endtime'] = date('Y-m-d H:i:s', $val['endtime']);
            }else{
                $list[$key]['endtime'] = date('Y-m-d H:i:s', $val['etime']);
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
     * @param $user_id
     */
    public function getMyCouponCount($user_id,$display = 'all'){
        $where = [];
        $where[] = ['p.type', 'eq', 2]; //优惠券
        $where[] = ['p.status','eq',Promotion::STATUS_OPEN];
        $where[] = ['c.user_id', 'eq', $user_id];   //领取者是$user_id
        if($display == 'no_used')
        {
            $where[] = ['c.is_used', 'eq', self::USED_NO];
            // $where[] = ['p.etime', '>=', time()];
        }
        if($display == 'yes_used')
        {
            $where[] = ['c.is_used', 'eq', self::USED_YES];
        }
        if($display == 'invalid')
        {
            $where[] = ['c.is_used', 'eq', self::USED_NO];
            // $where[] = ['p.etime', '<', time()];
        }
        $m = $this->alias('c')
            ->join('promotion p', 'p.id = c.promotion_id')
            ->where($where);

        if ($display == 'no_used') {
            $m->where('(c.endtime = 0 and p.etime >= '.time().') or (c.endtime >='.time().')');
        }
        if ($display == 'invalid') {
            $m->where('(c.endtime = 0 and p.etime < '.time().') or (c.endtime <'.time().')');
        }

        return $m->count();
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
    public function getMyCoupon($user_id, $promotion_id = '', $display = 'all', $page = 1, $limit = 10)
    {
        $return = [
            'status' => true,
            'msg' => '',
            'data' => [
                'list' => [],
                'count' => 0,
                'page' => $page,
                'limit' => $limit,
                'q_type' => $display
            ]
        ];
        $where[] = ['c.user_id', 'eq', $user_id];
        $where[] = ['p.type', 'eq', 2];
        if($display == 'no_used')
        {
            $where[] = ['c.is_used', 'eq', self::USED_NO];
            // $where[] = ['p.etime', '>=', time()];
        }
        if($display == 'yes_used')
        {
            $where[] = ['c.is_used', 'eq', self::USED_YES];
        }
        if($display == 'invalid')
        {
            $where[] = ['c.is_used', 'eq', self::USED_NO];
            // $where[] = ['p.etime', '<', time()];
        }
        if($promotion_id)
        {
            $where[] = ['c.promotion_id','eq',$promotion_id];
        }
        else
        {
            $where[] = ['p.status', 'eq', 1];
        }

        $m1 = $this->alias('c')
            ->join('promotion p', 'p.id = c.promotion_id')
            ->where($where);
        if ($display == 'no_used') {
            $m1->where('(c.endtime = 0 and p.etime >= '.time().') or (c.endtime >='.time().')');
        }
        if ($display == 'invalid') {
            $m1->where('(c.endtime = 0 and p.etime < '.time().') or (c.endtime <'.time().')');
        }

        $return['data']['list'] = $m1->order('c.ctime asc')
            ->page($page, $limit)
            ->select();
        

        $m2 = $this->alias('c')
            ->join('promotion p', 'p.id = c.promotion_id')
            ->where($where);
        if ($display == 'no_used') {
            $m2->where('(c.endtime = 0 and p.etime >= '.time().') or (c.endtime >='.time().')');
        }
        if ($display == 'invalid') {
            $m2->where('(c.endtime = 0 and p.etime < '.time().') or (c.endtime <'.time().')');
        }

        $return['data']['count'] = $m2->count();
        if($return['data']['list'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '获取成功';
            if(count($return['data']['list']) > 0)
            {
                $conditionModel = new PromotionCondition();
                $resultModel = new PromotionResult();
                foreach($return['data']['list'] as $k => $v)
                {
                    $pcondition = $conditionModel->getConditionList($v['id']);
                    $presult = $resultModel->getResultList($v['id']);
                    $expression1 = '';
                    $expression2 = '';
                    foreach($pcondition as $kk => $vv)
                    {
                        $expression1 .= $conditionModel->getConditionMsg($vv['code'], $vv['params']);
                    }
                    foreach($presult as $kk => $vv)
                    {
                        $expression2 .= $resultModel->getResultMsg($vv['code'], $vv['params']);
                    }
                    $return['data']['list'][$k]['expression1'] = $expression1;
                    $return['data']['list'][$k]['expression2'] = $expression2;
                    $return['data']['list'][$k]['is_expire'] = $v['etime'] > time() ? 1 : 2;
                    $return['data']['list'][$k]['stime'] = date('Y-m-d H:i', $v['stime']);
                    if($v['endtime'] > 0 ){
                        $return['data']['list'][$k]['etime'] = date('Y-m-d H:i', $v['endtime']);
                    }else{
                        $return['data']['list'][$k]['etime'] = date('Y-m-d H:i', $v['etime']);
                    }
                }

            }
        }
        return $return;
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
        $promotionModel = new Promotion();

        $code_arr = explode(',',$code);
        $where1[] = ['c.coupon_code', 'in', $code_arr];
        $where1[] = ['p.type', '=', $promotionModel::TYPE_COUPON];
        $list = $this->alias('c')
            ->field('c.*,name,stime,etime,status,exclusive')
            ->join('promotion p', 'c.promotion_id = p.id')
            ->where($where1)
            ->order('p.sort asc')
            ->select();
        //如果取出来的条数和优惠券的条数不一致，就说明有些优惠券不能用
        if(count($code_arr) != count($list)){
            return error_code(15009);
        }
        $exclusive_code = "";     //默认是不排他的，如果排他，第一个排他的优惠券码
        $exclusive_name = "";       //第一个排他的名称
        $exclusive_arr = [];    //根据权重，从前到后，第一个排他之后的优惠券列表
        foreach($list as $info){
            //判断规则是否开启
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
            foreach($list as $j){
                if($j['coupon_code'] != $info['coupon_code']){
                    if($j['promotion_id'] == $info['promotion_id']){
                        return error_code(15015);
                    }
                }
            }
            //如果前面是排他，那么这里就放到排他列表里
            if($exclusive_code != ""){
                $exclusive_arr[] = [
                    'code' => $info['coupon_code'],
                    'name' => $info['name']
                ];
            }else{
                if($info['exclusive'] == $promotionModel::EXCLUSIVE_YES){
                    $exclusive_code = $info['coupon_code'];
                    $exclusive_name = $info['name'];
                }
            }
            $result['data'][$info['coupon_code']] = $info;
        }
        //如果有排他记录，就返回报错，
        if($exclusive_arr){
            $result = error_code(15030);
            $result['exclusive_arr'] = $exclusive_arr;
            $result['exclusive_code'] = $exclusive_code;
            $result['exclusive_name'] = $exclusive_name;
            return $result;
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
        } else {
            return error_code(15022);
        }
        return $return_data;
    }

    /**
     * 批量生成优惠券
     * @param $promotion_id
     * @param $nums
     * @return array
     * @throws \Exception
     */
    public function createCoupon($promotion_id,$nums){
        $result = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];
        //判断promotin_id是否是合法的
        $promotionModel = new Promotion();
        $where[] = ['status', '=', 1];
        $where[] = ['type', '=',2];
        $where[] = ['id','=', $promotion_id];
        $info = $promotionModel->where($where)->find();
        if(!$info){
            $result['msg'] = '没有此优惠券活动';
            return $result;
        }
        //算开始时间
//        if($info['stime'] < time()){
//            $starttime = time();
//        }else{
//            $starttime = $info['stime'];
//        }
        $starttime = $info['stime'];
        $params = json_decode($info['params'],true);
        if(isset($params['term_day']) && $params['term_day'] > 0){
            $endtime = $starttime;          //起始时间就是开始时间
            $endtime = $endtime + $params['term_day'] * 60*60*24;
            //如果结算之后的时间大于优惠券结束的时间了，那就以优惠券的时间为准
            if($info['etime'] < $endtime){
                $endtime = 0;           //就写0吧。
            }
        }else{
            $endtime = 0;
        }


        if($nums > 5000){
//            $result['msg'] = '一次最多可以生生5000张';
            return error_code(15023);
        }


        //优惠券最大数量判断
        $data = [];
        for($i = 0 ;$i<$nums;$i++){
            $data[] = [
                'coupon_code' => $this->getCouponNumber($promotion_id),
                'promotion_id' => $promotion_id,
                'endtime' => $endtime,
                'ctime' => $starttime
            ];
        }
        $re = $this->saveAll($data);

        if(!$re->isEmpty()){
            $result['status'] = true;
            $result['data'] = $re->hidden(['promotion_id','is_used','user_id','used_id','ctime','utime'])->toArray();//array_column($re->toArray(),'coupon_code');
        }else{
//            $result['msg'] = '一张都没生成';
            return error_code(15024);
        }
        return $result;
    }

    /**
     * 获取优惠券的券码
     * @param $id       优惠券id
     * @return string
     */
    private function getCouponNumber($id){
        return strtoupper(substr_replace(dechex($id),substr(md5(uniqid(rand(), true)),4,16),2,0));
    }

    /**
     * 通过优惠券号领取优惠券
     * @param $user_id
     * @param $coupon_code
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function receiveCoupon($user_id, $coupon_code)
    {
        $return = error_code(15021);

        $where[] = ['coupon_code', 'eq', $coupon_code];
        $flag = $this->where($where)->find();

        if($flag['used_id'])
        {
//            $return['msg'] = '该优惠券已被使用';
            return error_code(15025);
        }
        if($flag['user_id'])
        {
//            $return['msg'] = '该优惠券已被其他人领取';
            return error_code(15026);
        }

        $data['user_id'] = $user_id;
        $result = $this->save($data, $where);

        if($result !== false)
        {
            $return['data'] = $this->where($where)->find();
            if($return['data']['promotion_id'])
            {
                $promotionModel = new \app\common\model\Promotion();
                $w[] = ['id', 'eq', $return['data']['promotion_id']];
                $promotion = $promotionModel->field('name')->where($w)->find();
                $return['data']['coupon_name'] = $promotion['name'];
            }
            $return['status'] = true;
            $return['msg'] = '领取成功';
        }
        return $return;
    }

    /**
     * 用户绑定优惠卷
     * @param $user_id
     * @param $coupon_code
     * @return array
     */
    public function bindUser($user_id, $coupon_code)
    {
        $result = error_code(15027);
        if ($this->where('coupon_code', $coupon_code)->update(['user_id' => $user_id]) !== false) {
            $result['status'] = true;
            $result['msg']    = '绑定成功';
        }
        return $result;
    }

    //导出优惠券验证
    public function exportValidate($where = [])
    {
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '验证成功',
        ];
        $filter[]       = ['id', 'eq', $where['id']];
        $promotionModel = new Promotion();
        $info           = $promotionModel->field('id,params')->where($filter)->find();

        $receive_count = $this->where([['promotion_id', '=', $where['id']]])->count();
        if (!$info) {
//            $result['msg'] = '优惠券不存在';
            return error_code(15007);
        }
        $info['params'] = json_decode($info['params'], true);
        if ($info['params']) {
            //判断最大领取数量
            if (isset($info['params']['max_nums']) && $info['params']['max_nums'] != 0) {
                if ($info['params']['max_nums'] < ($where['nums'] + $receive_count)) {
//                    $result['msg'] = '优惠券超过最大领取数量';
                    return error_code(15028);
                }
            }
        }
        $result['status'] = true;
        $result['msg']    = '检查通过';
        return $result;
    }
}