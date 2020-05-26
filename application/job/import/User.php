<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\job\import;

use think\exception\ErrorException;
use think\facade\Validate;
use think\queue\Job;
use app\common\model\User as UserModel;

use app\common\model\Ietask;
use think\facade\Log;

class User
{
    protected $rule = [
        'username' => 'require|max:40',
        'password' => 'require|alphaNum',
        'mobile' => ['regex' => '^1[3|4|5|7|8][0-9]\d{4,8}$'],
        'sex' => 'in:1,2,3',
        'nickname' => 'length:2,50',
        'balance' => 'float',
        'point' => 'number',
        'birthday'=>'date'

    ];
    protected $msg = [
        'username.require' => '用户名必填',
        'username.max' => '用户名最长40个字符',
        'password.require' => '密码必须',
        'password.alphaNum' => '密码格式错误',
        'mobile' => '请输入一个合法的手机号码',
        'sex' => '请选择合法的性别',
        'nickname' => '昵称长度为2-50个字符',
        'balance' => '请输入正确的余额',
        'point' => '请输入正确的积分',
        'birthday' => '生日格式不正确'
    ];
    //执行导入任务
    public function exec(Job $job, $params)
    {
        $ietaskModle = new Ietask();
        $userModel = new UserModel();
        $message = [];
        Log::record($params);
        try{
            $file = json_decode($params['params'], true);
            $csv = new \org\Csv();
            $resCsv = $csv->import($file['file_path']);

            if($resCsv['status']){
                $header = $resCsv['data'][0];
                unset($resCsv['data'][0]);
                $title=$userModel->csvHeader();
                $fields=[];
                foreach($title as $key=>$val){
                    $index = array_search($val['desc'],$header);
                    if($index>=0){
                        $fields[] = [
                            'index'=>$index,
                            'value'=>$val['id']
                        ];
                    }
                }
                $iData = [];
                if($fields){
                    $i = 0;
                    foreach ($resCsv['data'] as $key=>$val)
                    {
                        foreach($fields as $fkey=>$fval){
                            $iData[$i][$fval['value']]=$val[$fval['index']];
                        }
                        $i++;
                    }
                }
                foreach($iData as $key=>$val){
                    $time = time();
                    $user['username'] = $val['username'];
                    $user['password'] = md5(md5('000000'.$time));
                    $user['mobile'] = $val['mobile'];
                    $user['avatar'] = $val['avatar'];
                    if($val['sex'] == '男'){
                        $user['sex'] = $userModel::SEX_BOY;
                    }elseif ($val['sex'] == '女'){
                        $user['sex'] = $userModel::SEX_GIRL;
                    }else{
                        $user['sex'] = $userModel::SEX_OTHER;
                    }
                    $user['status'] = trim($val['status']) == '正常' ? $userModel::STATUS_NORMAL : $userModel::STATUS_DISABLE;
                    $user['birthday'] = str_replace('/','-',$val['birthday']);
                    $user['nickname'] = $val['nickname'];
                    $user['balance'] = $val['balance'];
                    $user['point'] = $val['point'];
                    $user['ctime'] = $time;
                    $user['utime'] = $time;


                    Log::record($user);
                    //校验数据
                    $validate = new \think\Validate($this->rule, $this->msg);
                    if(!$validate->check($user))
                    {
                        $message[] = $validate->getError();
                        Log::record($user['username'].implode(',',$message));
                        continue;
                    }else{
                        $userModel->startTrans();
                        //判断用户是否存在，存在跳过
                        $userData=$userModel->field('id')->where(['mobile'=>$user['mobile']])->find();
                        if(isset($userData['id'])&&$userData['id']!=''){
                            Log::record($user['username'].'已存在，导入失败');
                            $user_id = $userData['id'];
                        }else{
                            $user_id = $userModel->doAdd($user);
                        }
                        if(!$user_id) {
                            $userModel->rollback();
                            $message[] = error_code(11029,true);
                            Log::record($user['username'].'用户数据保存失败');
                            continue;
                        }else{
                            Log::record("用户id".$user_id);
                            $userModel->commit();
                        }
                    }
                }

                $uData['status'] = $ietaskModle::IMPORT_SUCCESS_STATUS;
                $uData['message'] = '导入成功';
                if($message){
                    $uData['message'] .= json_encode($message);
                }
                $uData['utime'] = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }else{
                $uData['status'] = $ietaskModle::IMPORT_FAIL_STATUS;
                $uData['message'] = $resCsv['msg'];
                $uData['utime'] = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }
        }catch (Exception $e){
            $message[] = $e->getMessage();
        }
        if ($job->attempts() > 3) {
            $uData['status'] = $ietaskModle::IMPORT_FAIL_STATUS;
            $uData['message'] = error_code(11041,true);
            $uData['utime'] = time();
            $ietaskModle->update($uData, ['id' => $params['task_id']]);
            $job->delete();
        }
    }

    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }
}