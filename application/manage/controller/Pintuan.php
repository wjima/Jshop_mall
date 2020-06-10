<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\PintuanGoods;
use app\common\model\PintuanRecord;
use app\common\model\PintuanRule;
use think\facade\Request;


class Pintuan extends Manage
{


    /**
     *
     * @return mixed
     */
    public function index()
    {
        if(Request::isAjax()) {
            $pintuanRuleModel = new PintuanRule();
            $request = input('param.');
            return $pintuanRuleModel->tableData($request);
        }
        return $this->fetch();

    }



    public function del()
    {

        if (!input('?param.id')) {
            return error_code(10051);
        }
        $pintuanRuleModel = new PintuanRule();
        $pintuanRuleModel->where(['id'=>input('param.id')])->delete();

        $pintuanGoodsModel = new PintuanGoods();
        $pintuanGoodsModel->where(['rule_id'=>input('param.id')])->delete();


        return [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
    }

    //添加&编辑拼团
    public function edit(){
        $this->view->engine->layout(false);
        $result = [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];

        $pintuanRuleModel = new PintuanRule();


        if (Request::isPost()) {
            $data = input('param.');

            return $pintuanRuleModel->toAdd($data);
        }

        //如果是编辑，取数据
        if(input('?param.id')){
            $info = $pintuanRuleModel->where('id',input('param.id'))->find();
            if(!$info){
                return error_code(10001);
            }

            $info['date'] = date('Y-m-d H:i:s',$info['stime'])." 到 ".date('Y-m-d H:i:s',$info['etime']);

            $this->assign('info',$info);
            $pintuanGoodsModel = new PintuanGoods();
            $list = $pintuanGoodsModel->where('rule_id',input('param.id'))->select();
            $ids = "";
            if(!$list->isEmpty()){
                $list = $list->toArray();
                $ids = implode(',',array_column($list,'goods_id'));
            }
            $this->assign('goods',$ids);
        }


        $result['data'] = $this->fetch();
        return $result;
    }
    //排序更改
    public function updateSort()
    {
        $result =  error_code(10051);
        $field  = input('post.field/s');
        $value  = input('post.value/d');
        $id     = input('post.id/d', '0');
        if (!$field || !$value || !$id) {
            return $result;
        }

        $pintuanRuleModel = new PintuanRule();
        $rel = $pintuanRuleModel->where('id','eq',$id)->update([$field => $value]);
        if ($rel) {
            $result['msg']    = '更新成功';
            $result['status'] = true;
        } else {
            return error_code(10021);
        }
        return $result;
    }

    /**
     *  更改设置状态
     *
     */
    public function changeState()
    {
        $result =  error_code(10051);
        $pintuanRuleModel = new PintuanRule();
        $id = input('param.id/d', 0);

        $state = input('param.state/s', 'true');

        if (!$id) return $result;
        if($state == 'true'){
            $status = $pintuanRuleModel::STATUS_ON;
        }else{
            $status = $pintuanRuleModel::STATUS_OFF;
        }
        if ($pintuanRuleModel->save(['status'=>$status], ['id' => $id]))
        {
            $result['status'] = true;
            $result['msg'] = '设置成功';
        } else {
            return error_code(10021);
        }
        return $result;
    }

    public function record()
    {
        if(Request::isAjax()) {
            $pintuanRecordModel = new PintuanRecord();
            $request = input('param.');
            return $pintuanRecordModel->tableData($request);
        }
        return $this->fetch();
    }
}
