<?php


namespace addons\WelfarePro\model;


use app\common\model\Common;
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
            foreach ($post['coupons'] as $val){
                if((int)$post['nums'][$val] <= 0){
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
                $result['msg'] = "用户ID：".$v."已经参加活动了";
                return $result;
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
        $page = $post['page'] ?? 1;
        $limit = $post['limit'] ?? 10;
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
            'msg'=>'查询成功'
        ];
    }
}