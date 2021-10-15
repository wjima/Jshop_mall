<?php

namespace addons\DistributionCenter\model;
use app\common\model\UserGrade;
use think\Model;
use think\Validate;

class DistributionResult extends Model{

    public $code = [
        'COMMISSION_1' => [
            'name' => '一级佣金',
            'type' => 'user'
        ],
        'COMMISSION_2' => [
            'name' => '二级佣金',
            'type' => 'user',
        ],
        /*'COMMISSION_3' => [
            'name' => '三级佣金',
            'type' => 'user',
        ],*/
    ];

    /**
     * @param $code
     * @param array $params
     * @return string
     */
    public function getResultMsg($code, $params = [])
    {
        switch($code)
        {
            case 'COMMISSION_1':
                $msg = '返'.$params['money'].'元 ';
                break;
            case 'COMMISSION_2':
                $msg = '返'.$params['money'].'折 ';
                break;
            case 'COMMISSION_3':
                $msg = '返'.$params['money'].'元 ';
                break;
        }
        return $msg;
    }

    /**
     *  添加促销的条件
     * User:wht
     * @param array $data
     * @return array
     */
    public function addData($data)
    {
        //校验结果
        $result = $this->addCheck($data);
        if(!$result['status']){
            return $result;
        }
        $result['status'] = false;          //重新置成false

        //判断如果是商品促销结果，在params里必须要有condition_id信息
        if(!isset($this->code[$data['code']]['type'])){
            $result['msg'] = '没有此促销结果代码';
            return $result;
        }


        $data['params'] = json_encode($data['params']);
        if($data['id'] != ''){
            //更新
            $info = $this->getInfo($data['id']);
            if($info){
                if($this->allowField(true)->save($data,['id'=>$data['id']])){
                    $result['status'] = true;
                }else{
                    $result['msg'] = "保存失败";
                }
                return $result;

            }
        }else{
            //添加
            $userGradeModel = new UserGrade();
            $where['id'] = $data['grade_id'];
            $info = $userGradeModel->where($where)->find();
            if($info){
                $check = $this->where([['code', '=', $data['code']], ['grade_id', '=', $data['grade_id']]])->count();
                if ($check >= 1) {
                    $result['msg'] = "同一类型的返佣只能添加一次";
                } else {
                    if ($this->allowField(true)->save($data)) {
                        $result['status'] = true;
                    } else {
                        $result['msg'] = "保存失败";
                    }
                }
                return $result;
            }else{
                $result['msg'] = '没有找到此等级记录';
                return $result;
            }
        }
    }

    private function addCheck($data)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!isset($data['code']) || !isset($data['grade_id']) || !isset($data['params'])){
            return error_code(10003);
        }
        if(!isset($this->code[$data['code']])){
            return error_code(15005);
        }
        switch ($data['code'])
        {
            case 'COMMISSION_1':
                if($data['params']['commission_type']=='1' && floatval($data['params']['discount'])>100 && floatval($data['params']['discount'])<0){
                    $result['msg'] = "请正确输入比例";
                    return $result;
                }elseif(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['discount'])){
                        $result['msg'] = "请正确输入金额，最多2位小数";
                        return $result;
                }
                break;
            case 'COMMISSION_2':
                if($data['params']['commission_type']=='1' && floatval($data['params']['discount'])>100 && floatval($data['params']['discount'])<0){
                    $result['msg'] = "请正确输入比例";
                    return $result;
                }elseif(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['discount'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                break;
            case 'COMMISSION_3':
                if($data['params']['commission_type']=='1' && floatval($data['params']['discount'])>100 && floatval($data['params']['discount'])<0){
                    $result['msg'] = "请正确输入比例";
                    return $result;
                }elseif(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['discount'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                break;
        }
        $result['status'] = true;


        return $result;
    }

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {

        $tableWhere = $this->tableWhere($post);
        $list = $this
            ->field($tableWhere['field'])
            ->alias('dr')
            ->join(config('database.prefix').'user_grade ug','ug.id = dr.grade_id')
            ->where($tableWhere['where'])
            ->select();

        $data = $this->tableFormat($list);
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = count($list);
        $re['data'] = $data;

        return $re;
    }
    protected function tableWhere($post)
    {
        $result['where'] = [];

        if(isset($post['grade_id'])){
            $result['where']['dr.grade_id'] = $post['grade_id'];
        }

        if(isset($post['field'])){
            $result['field'] = $post['field'];
        }else{
            $result['field'] = "dr.*";
        }
        if(isset($post['order'])){
            $result['order'] = $post['order'];
        }else{
            $result['order'] = [];
        }
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
            if($v['code']) {
                $list[$k]['name'] = $this->code[$v['code']]['name'];
            }
            if($v['params']) {
                $list[$k]['params'] = $v['params'];
            }
        }
        return $list;
    }


    public function toDel($id)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $info = $this->getInfo($id);
        if($info){
            $this->where(['id'=>$info['id'],'grade_id'=>$info['grade_id']])->delete();
            $result['status'] = true;
            return $result;
        }else{
            $result['msg'] = '没有找到此促销记录';
            return $result;
        }
    }

    //取信息
    public function getInfo($id){
        $where['dr.id'] = $id;
        $info = $this
            ->field('dr.*')
            ->alias('dr')
            ->join(config('database.prefix').'user_grade ug','ug.id = dr.grade_id')
            ->where($where)->find();
        if($info){
            $info['params'] = json_decode($info['params'],true);
        }
        return $info;
    }

}
