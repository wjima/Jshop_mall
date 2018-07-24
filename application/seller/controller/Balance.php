<?php

/**
 * 商户的资金管理（余额管理，取店铺创始人的余额）
 */

namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Balance as BalanceModel;


use app\common\model\User;
use app\common\model\UserBankcards;
use app\common\model\UserTocash;
use Request;


class Balance extends Seller
{
    protected $userId = 0;
    protected function initialize()
    {
        parent::initialize();
        //取店铺的管理员id
        $user_id = getSellerInfoById($this->sellerId,'user_id');
        if($user_id == ''){
            $this->error('找不到店铺的创始人');
        }else{
            $this->userId = $user_id;
        }
    }

    public function index()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $data['user_id'] = $this->userId;
            $balanceModel = new BalanceModel();
            return $balanceModel->tableData($data);
        }else{
            $userModel = new User();
            $info = $userModel->getUserInfo($this->userId);
            $this->assign('info',$info);
            return $this->fetch('index');
        }
    }

    //提现
    public function tocash()
    {
        $this->view->engine->layout(false);

        if(Request::isPost()){
            if(!input('?param.id')){
                return error_code(10000);
            }
            if(!input('?param.money')){
                return error_code(10000);
            }
            $userTocashModel = new UserTocash();
            return $userTocashModel->tocash($this->userId,input('param.money'),input('param.id'));
        }
        $userBankcardsModel = new UserBankcards();
        $list = $userBankcardsModel->getMyBankcardsList($this->userId);
        $this->assign('list',$list['data']);
        return [
            'status' => true,
            'data' => $this->fetch('tocash'),
            'msg' => ''
        ];
    }
    public function tocashlist()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $data['user_id'] = $this->userId;
            $tocashModel = new UserTocash();
            return $tocashModel->tableData($data);
        }else{
            return $this->fetch('tocashlist');
        }
    }

    //我的银行卡列表
    public function bankcards()
    {
        $bankcardsModel = new UserBankcards();
        $list = $bankcardsModel->getMyBankcardsList($this->userId);
        $list['data'] = $list['data']->toArray();
        $this->assign('list',$list['data']);
        return $this->fetch('bankcards');
    }
    //添加银行卡
    public function addcard(){
        $this->view->engine->layout(false);
        $bankcardsModel = new UserBankcards();
        if(Request::isPost()){
            return $bankcardsModel->addBankcards($this->userId,input('param.'));
        }else{
            return $this->fetch('addcard');
        }
    }


    /**
     *
     *  设置默认的银行卡
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setDefaultCard()
    {
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->setDefault($this->userId, input('param.id/d'));
    }


    /**
     *
     *  删除银行卡
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delCard()
    {
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->delBankcards($this->userId, input('param.id/d'));
    }


    // 根据卡号获取银行信息
    public function bankCardOrganization()
    {
        $bankCardsModel = new UserBankcards();
        $card_number = input('param.card_number');
        return $bankCardsModel->bankCardsOrganization($card_number);
    }
}