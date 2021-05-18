<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;
use app\common\model\BillReship as BillReshipModel;
use app\common\model\Logistics;

class BillReship extends Manage
{

    public function index()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $billReshipModel = new BillReshipModel;
            return $billReshipModel->tableData($data);
        }
        return $this->fetch('index');
    }
    public function reship()
    {
        $this->view->engine->layout(false);
        if(!input('?param.reship_id')){
            return error_code(13220);
        }
        $reship_id = input('reship_id');
        $billReshipModel = new BillReshipModel();
        $reship = $billReshipModel->with('items')->where('reship_id',$reship_id)->find();
        if(!$reship) return \error_code(10002);
        if($reship['status'] != $billReshipModel::STATUS_WAIT_SHIP) return \error_code(10000);
        if(request()->isPost()){
            $logi_code = input('post.logi_code','');
            $logi_no = input('post.logi_no','');
            // $status = input('post.status');
            if(!$logi_code || !$logi_no ) return \error_code(10003);
            $data = [];
            $data['logi_code'] = $logi_code;
            $data['logi_no'] = $logi_no;
             $data['status'] = $billReshipModel::STATUS_SHIPPED;
            if($billReshipModel->where('reship_id',$reship_id)->update($data)){
                return [
                    'status'=>true,
                    'msg'=>'',
                    'data'=>[]
                ];
            }else{
                return \error_code(10004);
            }
            
        }
        // 获取退货单信息
        $this->assign('reship',$reship);
        // 获取物流公司
        $logisticsModel = new Logistics();
        $logi_info = $logisticsModel->getAll();
        $this->assign('logi', $logi_info);
        return  [
            'data'=>$this->fetch(),
            'status'=>true
        ];
    }

    public function view()
    {
        $this->view->engine->layout(false);
        if(!input('?param.reship_id')){
            return error_code(13220);
        }
        $billReshipModel = new BillReshipModel();
        $where['reship_id'] = input('param.reship_id');
        $info = $billReshipModel->where($where)->find();
        if(!$info){
            return error_code(13221);
        }
        if($info->items){
            $info['items_json'] = json_encode($info->items);
        }

        $this->assign('info',$info);
        return [
            'status' => true,
            'data' => $this->fetch('view'),
            'msg' => ''
        ];
    }

    //退货 ，待确认
    public function confirmReship()
    {
        $this->view->engine->layout(false);
        if(!input('?param.reship_id')){
            return error_code(13220);
        }
        $billReshipModel = new BillReshipModel();
        return $billReshipModel->confirmReship(input('param.reship_id'));
    }
}
