<?php


namespace addons\WelfarePro\model;


use app\common\model\Common;
use app\common\model\Coupon;
use app\common\model\User;
use think\Db;

class WelfareproCoupon extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    public function toAdd($post){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!isset($post['name'])){
            $result['msg'] = '名称必填';
            return $result;
        }else{
            $post['name'] = substr($post['name'],0,50);
        }
        if(!isset($post['coupons'])){
            $result['msg'] = '至少选择一个优惠券';
            return $result;
        }else{
            if(!isset($post['nums'])){
                $result['msg'] = '参数错误';
                return $result;
            }
            foreach ($post['coupons'] as $couponId=>$v){
                if((int)$post['nums'][$couponId] <= 0){
                    $result['msg'] = '选中的优惠券的数量不能为空';
                    return $result;
                }
            }
        }
        if(!isset($post['date'])){
            $result['msg'] = '起止时间不能为空';
            return $result;
        }
        if(!isset($post['type'])){
            $result['msg'] = '请选择范围';
            return $result;
        }

        $data = [];
        $data['id'] = $post['id'];
        $data['type'] = (int)$post['type'];
        $data['name'] = $post['name'];
        $data['sendnum'] = $post['sendnum'];
        //如果是方法，code换成小写
        $theDate = explode(' 到 ', input('param.date'));
        if (count($theDate) != 2) {
            return error_code(10000);
        }
        $data['date_start'] = strtotime($theDate[0]);
        $data['date_end'] = strtotime($theDate[1]);

        //如果选择了用户，那么就解析成一维数组
        if(isset($post['user_id'])){
            $user = explode(',',$post['user_id']);
        }else{
            $user = [0];
        }


        $hbuserModel= new WelfareproHbuser();
        $couponRelModel = new WelfareproCouponRel();
        if($data['id'] != ""){
            $result = $this->checkUser($user,$data['id']);
            if(!$result['status']){
                return $result;
            }
            try{
                Db::startTrans();
                $this->save($data,['id'=>$data['id']]);
                //组装用户数据
                $userData = [];
                foreach($user as $v){
                    $userData[] = [
                        'hb_id' => $data['id'],
                        'user_id' => $v,
                        'type'  =>  2
                    ];
                }
                //组装优惠券数据
                $relData = [];
                foreach ($post['coupons'] as $couponId=>$val){
                    $relData[] = [
                        'c_id'=>$data['id'],
                        'coupon_id'=>$couponId,
                        'num'   =>  $post['nums'][$couponId]
                    ];
                }
                $hbuserModel->where('type','=',2)->where('hb_id',$data['id'])->delete();
                $hbuserModel->saveAll($userData);
                $couponRelModel->where('c_id',$data['id'])->delete();
                $couponRelModel->saveAll($relData);
                Db::commit();
                $result['status'] = true;
                return $result;
            }catch (\Throwable $e){
                Db::rollback();
                $result['msg'] = $e->getMessage();
                return $result;
            }

        }else{
            $result = $this->checkUser($user);
            if(!$result['status']){
                return $result;
            }
            try{
                Db::startTrans();
                $this->save($data);
                //组装用户数据
                $userData = [];
                foreach($user as $v){
                    $userData[] = [
                        'hb_id' => $this->id,
                        'user_id' => $v,
                        'type'  =>  2
                    ];
                }
                //组装优惠券数据
                $relData = [];
                foreach ($post['coupons'] as $couponId=>$val){
                    $relData[] = [
                        'c_id'=>$this->id,
                        'coupon_id'=>$couponId,
                        'num'   =>  $post['nums'][$couponId]
                    ];
                }

                $hbuserModel->saveAll($userData);
                $couponRelModel->saveAll($relData);
                Db::commit();
                $result['status'] = true;
                return $result;
            }catch (\Throwable $e){
                Db::rollback();
                $result['msg'] = $e->getMessage();
                return $result;
            }
        }
    }
    //校验用户是否添加过红包活动
    private function checkUser($user_arr,$hb_id = 0){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $m = new WelfareproHbuser();
        foreach($user_arr as $v){
            $where = [];
            if($hb_id != 0){
                $where[] = ['hb_id', '<>', $hb_id];
            }
            $where[] = ['type','=',2];
            $where[] = ['user_id', '=', $v];
            $info = $m->where($where)->find();
            if($info){
                if($v == 0){
                    $result['msg'] = "只能有一个所有人都可参与分享的福利。";
                    return $result;
                }else{
                    $result['msg'] = "用户ID：".$v."已经参加活动了";
                    return $result;
                }

            }
        }
        $result['status'] = true;

        return $result;
    }

    public function toDel($id){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        try{
            Db::startTrans();
            $this->where('id',$id)->delete();
            $hbuserModel= new WelfareproHbuser();
            $hbuserModel->where('hb_id',$id)->where('type',2)->delete();
            $relModel= new WelfareproCouponRel();
            $relModel->where('c_id',$id)->delete();
            Db::commit();
        }catch (\Throwable $e){
            $result['status'] = false;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }

    public function user(){
        return $this->hasMany(WelfareproHbuser::class,'hb_id','id')->where('type',2);
    }
    public function coupon(){
        return $this->hasMany(WelfareproCouponRel::class,'c_id','id');
    }
    public function getList($post){
        $page = isset($post['page']) ?$post['page'] : 1;
        $limit = isset($post['limit']) ?$post['limit'] : 10;
        $list = $this->with(['user','coupon'])->page($page,$limit)->select()->toArray();
        $count = $this->count('id');
        if(!empty($list)){
            $type = [
                1 => '全部用户',
                2 => '新用户',
            ];
            foreach ($list as &$val){
                if ($val['type']) {
                    $val['type_name'] = $type[$val['type']];
                }
                if ($val['date_start']) {
                    $val['date_start'] = getTime($val['date_start']);
                }
                if ($val['date_end']) {
                    $val['date_end'] = getTime($val['date_end']);
                }
                if ($val['ctime']) {
                    $val['ctime'] = getTime($val['ctime']);
                }
                if ($val['utime']) {
                    $val['utime'] = getTime($val['utime']);
                }
                if(empty($val['user'])){
                    $val['user_ids'] =  '所有人';
                }else{
                    $user_ids = array_column($val['user'],'user_id');
                    if($user_ids[0] == 0){
                        $val['user_ids'] =  '所有人';
                    }else{
                        $user_ids = implode(',',$user_ids);
                        $val['user_ids'] = $user_ids;
                    }

                }
                $coupon_ids = array_column($val['coupon'],'coupon_id');
                $coupon_ids = implode(',',$coupon_ids);
                $val['coupon_ids'] = $coupon_ids;
            }
        }
        return [
            'code'=>0,
            'count'=>$count,
            'data'=>$list,
            'msg'=>error_code(10026,true)
        ];
    }

    public function sendCoupon($user_id,$shareCode){
        $result = [
            'status' => false,
            'data' => "",
            'msg' => ''
        ];

        //注册时间到现在小于10分钟的都算新用户
        //判断用户是否是新用户
        $userModel = new User();
        $userInfo = $userModel->where('id',$user_id)->find();
        if(!$userInfo){
            return error_code(10000);
        }
        if(time() - $userInfo['ctime'] > 600){
            $is_new = false;
        }else{
            $is_new = true;     //新用户
        }
        $userModel = new User();
        $tj_user_id = $userModel->getUserIdByShareCode($shareCode);

        $info = $this->userCoupon($tj_user_id);
        if(!$info){
            $result['msg'] = "没有活动或者已经结束";
            return $result;
        }
        
        //判断是否是限于新用户参加
        if($info['type'] == 2 && !$is_new){
            $result['msg'] = "没有活动或者已经结束。";         //只限于新用户参与
            return $result;
        }

        //判断该推荐人推荐数量是否已发完
        $couponLogModel = new WelfareproCouponLog();
        $is_over =  $couponLogModel->couponOver($info['id'],$tj_user_id,$info['sendnum']);
        if(!$is_over){
            $result['msg'] = "来晚一步，优惠券已经被领完了。";         //只限于新用户参与
            return $result;
        }
        //判断是否领取过，
        $coupon_ids = $info->coupon->column('coupon_id');
        $couponModel = new Coupon();
        $where = [
            ['promotion_id','in',$coupon_ids],
            ['user_id','=',$user_id]
        ];
        if($couponModel->where($where)->find()){
            $result['msg'] = '您已经领取，不能重复领取';
            return $result;
        }
        //去发券
        return $this->sendCoupon2($user_id,$info,$tj_user_id);
    }

    private function sendCoupon2($user_id,$info,$tj_user_id){
        //根据权重找到一张优惠券。
        $coupon = $this->getCoupon($info->coupon);
        $id = $coupon['c_id'];  //扫码领优惠券福利
        $coupon_id = $coupon['coupon_id'];
        $couponModel = new Coupon();
        $result = [
            'status'=>false,
            'msg'=>'领取失败',
            'data'=>''
        ];
        try{
            Db::startTrans();
            //用户领取优惠券
            $res = $couponModel->addData($user_id,$coupon_id);
            if(!$res['status']){
                //用户领取优惠券失败
                throw  new \Exception($res['msg']);
            }
            //推荐人推荐数量+1
            $couponLogModel = new WelfareproCouponLog();
            $num = $couponLogModel->where('tj_user_id',$tj_user_id)->where('c_id',$id)->count();
            $maxNums = $this->where('id',$id)->value('sendnum');
            if($num >= $maxNums){
                //用户领取优惠券失败
                throw  new \Exception('来晚一步，优惠券已经被领完了.');
            }else{
                $data = [
                    'tj_user_id'=>$tj_user_id,
                    'c_id'=>$id,
                    'user_id'=>$user_id,
                    'coupon_id'=>$coupon_id,
                    'ctime'=>time()
                ];
                $couponLogModel->save($data);
            }
            Db::commit();
        }catch (\Throwable $e){
            Db::rollback();
            dump($e);
            die;
            $result['msg'] = $e->getMessage();
            return $result;
        }
        $result['status'] = true;
        $result['msg'] = '领取成功';
        return $result;
    }

    private function  getCoupon($coupons){
        $tempArray = [];
        if(count($coupons) == 1) return $coupons[0];    //如果只有一张优惠券 就用这张优惠券
        foreach ($coupons as $coupon){
            for ($i=0;$i<(int)$coupon['num'];$i++){
                $tempArray[$i] = $coupon;
            }
        }
        $length = count($tempArray);
        $key = mt_rand(0,$length-1);    //获取随机数
        return $tempArray[$key];
    }


    /**
     * 获取当前推荐用户的优惠券列表
     * @param $tj_user_id
     */
    public function userCoupon($tj_user_id){
        $where = [];
        $where[] = ['c.date_start', '<',time()];
        $where[] = ['c.date_end', '>', time()];
        $where[] = ['hbu.user_id', 'in', [$tj_user_id,0]];
        $where[] = ['hbu.type', '=', 2];

        $info = $this->with(['coupon'])->alias('c')
                ->leftJoin(app(WelfareproHbuser::class)->getTable().' hbu','hbu.hb_id=c.id')
                ->where($where)
                ->field(['c.*'])
                ->find();
        return $info;

    }
}