<?php
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\Balance;
use app\common\model\GoodsComment;
use app\common\model\Setting;
use app\common\model\UserBankcards;
use app\common\model\UserPointLog;
use app\common\model\UserShip;
use app\common\model\UserTocash;
use app\common\model\UserToken;
use app\common\model\User as UserModel;
use app\common\model\GoodsBrowsing;
use app\common\model\GoodsCollection;
use app\common\model\UserWx;
use app\common\model\BillPayments;
use org\Curl;
use think\facade\Cache;
use think\facade\Request;
use think\Container;

class User extends Api
{
    /**
     * 登陆
     * @return array
     */
    public function login()
    {
        $platform = input('param.platform',1);
        $userModel = new UserModel();
        $data = input('param.');
        return $userModel->toLogin($data, 2,$platform);
    }


    /**
     * 短信验证码登陆，手机短信验证注册账号
     * mobile       手机号码，必填
     * code         手机验证码，必填
     * invitecode   邀请码，推荐人的邀请码 选填
     * password     注册的时候，可以传密码 选填
     * user_wx_id   第三方登录，微信公众号里的登陆，微信小程序登陆等需要绑定账户的时候，要传这个参数，这是第一次的时候需要这样绑定，以后就不需要了  选填
     *
     * @return array
     */
    public function smsLogin()
    {
        $platform = input('param.platform',1);
        $userModel = new UserModel();
        $data = input('param.');
        return $userModel->smsLogin($data, 2,$platform);
    }


    /**
     * 微信小程序创建用户，不登陆，只是保存登录态
     * @return array
     */
    public function wxappLogin1()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        if(!input("?param.code")){
            $result['msg'] = 'code参数缺失';
            return $result;
        }
        $userWxModel = new UserWx();
        return $userWxModel->codeToInfo(input('param.code'));
    }


    /**
     * 微信小程序传过来了手机号码，那么取他的手机号码
     * @return array
     */
    public function wxappLogin2()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        if(!input("?param.open_id"))
        {
            $result['msg'] = 'open_id';
            return $result;
        }
        if(!input("?param.iv"))
        {
            $result['msg'] = 'iv参数缺失';
            return $result;
        }
        if(!input("?param.edata"))
        {
            //加密的encryptedData数据，这是个加密的字符串
            $result['msg'] = '加密参数缺失';
            return $result;
        }
        $pid = input('param.pid',0);
        $userWxModel = new UserWx();
        $re = $userWxModel->updateWxInfo(input('param.open_id'),input('param.edata'),input('param.iv'),$pid);

        if(!$re['status'])
        {
            return $re;
        }
        if($re['data']['user_id'] == 0){
            //未绑定用户，需要先绑定手机号码
            $result['status'] = true;
            $result['data'] = ['user_wx_id' => $re['data']['id']];
            return $result;
        }else{
            //绑定好手机号码了，去登陆,去取user_token
            $userTokenModel = new UserToken();
            $re = $userTokenModel->setToken($re['data']['user_id'],2);
            if($re['status']){
                $re['data'] = ['token'=>$re['data']];
            }
            return $re;
        }
    }


    /**
     * 发送登陆注册短信，type为1注册，为2登陆
     * @return array|mixed
     */
    public function sms()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '成功'
        ];
        $userModel = new UserModel();
        if(!input("?param.mobile"))
        {
            $result['msg'] = '请输入手机号码';
            return $result;
        }
        if(!input("?param.code"))
        {
            $result['msg'] = '缺少核心参数';
            return $result;
        }
        $code = input('param.code');
        $type = input('param.type');
        if($type == 'bind'){ //绑定会员
            $code = 'login';
        }
        return $userModel->sms(input('param.mobile'),$code);
    }


    /**
     * 退出
     * @return array
     */
    public function logout()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(!input("?param.token"))
        {
            $result['msg'] = '请输入token';
            return $result;
        }
        $userTokenModel = new UserToken();
        return $userTokenModel->delToken(input("param.token"));
    }


    /**
     * 注册，此接口迟早要废弃，建议直接使用smsLogin接口
     * @return array
     */
    public function reg()
    {
        $userModel = new UserModel();
        $data = input('post.');
        return $userModel->smsLogin($data,2);
    }


    /**
     * 用户信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $userModel = new UserModel();
        $userInfo = $userModel
            ->field('id,username,mobile,sex,birthday,avatar,nickname,balance,point,status')
            ->where(array('id'=>$this->userId))
            ->find();
        if($userInfo)
        {
            $result['data'] = $userInfo;
            $result['status'] = true;
        }
        else
        {
            $result['msg'] = '未找到此用户';
        }
        return $result;
    }


    /**
     * 更换头像
     * @return array|mixed
     */
    public function changeAvatar()
    {
        $result = [
            'status' => false,
            'data' => input('param.'),
            'msg' => '保存失败'
        ];
        if(!input("?param.avatar"))
        {
            return error_code(11003);
        }
        $userModel = new UserModel();
        if($userModel->changeAvatar($this->userId,input('param.avatar')))
        {
            $result['status'] = true;
            $result['data']['avatar'] = input('param.avatar');
            $result['msg'] = '保存成功';
        }
        return $result;
    }


    /**
     * 编辑用户信息
     * @return array|mixed
     */
    public function editInfo()
    {
        $sex = input('param.sex','');
        $birthday = input('param.birthday','');
        $nickname = input('param.nickname','');
        $userModel = new UserModel();
        return $userModel->editInfo($this->userId,$sex,$birthday,$nickname);
    }


    /**
     * 添加商品浏览足迹
     * @return array
     */
    public function addGoodsBrowsing()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(!input("?param.goods_id"))
        {
            $result['msg'] = '请输入goods_id';
            return $result;
        }
        $goodsBrowsingModel = new GoodsBrowsing();
        return $goodsBrowsingModel->toAdd($this->userId, input("param.goods_id"));
    }


    /**
     * 删除商品浏览足迹
     * @return array
     */
    public function delGoodsBrowsing()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(!input("?param.goods_ids"))
        {
            $result['msg'] = '请输入ids';
            return $result;
        }
        $goodsBrowsingModel = new GoodsBrowsing();
        return $goodsBrowsingModel->toDel($this->userId,input("param.goods_ids"));
    }


    /**
     * 取得商品浏览足迹
     * @return array
     */
    public function goodsBrowsing()
    {

        if(input("?param.limit"))
        {
            $limit = input("param.limit");
        }
        else
        {
            $limit = config('jshop.page_limit');
        }
        if(input("?param.page"))
        {
            $page = input("param.page");
        }
        else
        {
            $page = 1;
        }
        $goodsBrowsingModel = new GoodsBrowsing();
        return $goodsBrowsingModel->getList($this->userId, $page , $limit);
    }


    /**
     * 添加商品收藏（关注）
     * @return array
     */
    public function goodsCollection()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(!input("?param.goods_id"))
        {
            $result['msg'] = '请输入goods_id';
            return $result;
        }
        $goodsCollectionModel = new GoodsCollection();
        return $goodsCollectionModel->toDo($this->userId, input("param.goods_id"));
    }


    /**
     * 取得商品收藏记录（关注）
     * @return array
     */
    public function goodsCollectionList()
    {
        if(input("?param.limit"))
        {
            $limit = input("param.limit");
        }
        else
        {
            $limit = config('jshop.page_limit');
        }
        if(input("?param.page"))
        {
            $page = input("param.page");
        }
        else
        {
            $page = 1;
        }
        $goodsCollectionModel = new GoodsCollection();
        return $goodsCollectionModel->getList($this->userId,$page , $limit);
    }


    /**
     * 存储用户收货地址接口
     * @return array
     */
    public function saveUserShip()
    {
        //传入进来的数据
        $area_id = input('area_id');
        $user_name = input('user_name');
        $detail_info = input('detail_info');
        $tel_number = input('tel_number');
        $is_def = input('is_def');
        $user_id = $this->userId;

        $data['user_id'] = $user_id;
        $data['area_id'] = $area_id;
        $data['address'] = $detail_info;
        $data['name'] = $user_name;
        $data['mobile'] = $tel_number;
        $data['is_def'] = $is_def;

        //存储收货地址
        $result = model('common/UserShip')->saveShip($data);
        if($result)
        {
            $return_data = array(
                'status' => true,
                'msg' => '存储收货地址成功',
                'data' => $result
            );
        }
        else
        {
            $return_data = array(
                'status' => false,
                'msg' => '存储收货地址失败',
                'data' => $result
            );
        }
        return $return_data;
    }


    /**
     * H5 添加收货地址
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function vueSaveUserShip()
    {
        $data['user_id'] = $this->userId;
        $data['area_id'] = input('param.area_id');
        $data['address'] = input('param.address');
        $data['name'] = input('param.name');
        $data['mobile'] = input('param.mobile');
        $data['is_def'] = input('param.is_def');
        $model = new UserShip();
        $result = $model->vueSaveShip($data);
        if($result)
        {
            $return_data = [
                'status' => true,
                'msg' => '存储收货地址成功',
                'data' => $result
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '存储收货地址失败',
                'data' => $result
            ];
        }
        return $return_data;
    }


    /**
     * 获取收货地址详情
     * @return array
     */
    public function getShipDetail()
    {
        $id = input('param.id');
        $result = model('common/UserShip')->getShipById($id,$this->userId);
        if($result)
        {
            $result['area_name'] = get_area($result['area_id']);
            $res = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $result
            ];
        }
        else
        {
            $res = [
                'status' => false,
                'msg' => '该收货地址不存在',
                'data' => ''
            ];
        }
        return $res;
    }


    /**
     * 收货地址编辑
     * @return array
     */
    public function editShip()
    {
        $data['name'] = input('param.name');
        $data['area_id'] = input('param.area_id');
        $data['address'] = input('param.address');
        $data['mobile'] = input('param.mobile');
        $data['is_def'] = input('param.is_def');
        $data['id'] = input('param.id');

        $result = model('common/UserShip')->editShip($data, $this->userId);
        if($result)
        {
            $res = [
                'status' => true,
                'msg' => '保存成功',
                'data' => ''
            ];
        }
        else
        {
            $res = [
                'status' => false,
                'msg' => '保存失败',
                'data' => ''
            ];
        }
        return $res;
    }


    /**
     * 删除收货地址
     * @return mixed
     */
    public function removeShip()
    {
        if(!input('param.id'))
        {
            return error_code(10051);
        }
        return model('common/UserShip')->removeShip(input('param.id'), $this->userId);
    }


    /**
     * 设置默认地址
     * @return array|mixed
     */
    public function setDefShip()
    {
        if(!input('param.id'))
        {
            return error_code(10051);
        }
        return model('common/UserShip')->setDefaultShip(input('param.id'),$this->userId);
    }


    /**
     * 获取用户收货地址列表
     * @return array
     */
    public function getUserShip()
    {
        $user_id = $this->userId;
        $list = model('common/UserShip')->getUserShip($user_id);
        if($list)
        {
            $return_data = array(
                'status' => true,
                'msg' => '获取用户收货地址成功',
                'data' => $list
            );
        }
        else
        {
            $return_data = array(
                'status' => true,
                'msg' => '用户暂无收货地址',
                'data' => $list
            );
        }
        return $return_data;
    }


    /**
     * 获取收货地址全部名称
     * @return string
     */
    public function getAllName()
    {
        $id = input('id');
        return get_area($id);
    }


    /**
     * 获取最终地区ID
     * @return array
     */
    public function getAreaId()
    {
        $province_name = input('province_name');
        $city_name = input('city_name');
        $county_name = input('county_name');
        $postal_code = input('postal_code');
        $area_id = model('common/Area')->getThreeAreaId($county_name, $city_name, $province_name, $postal_code);
        if($area_id)
        {
            $res = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $area_id
            ];
        }
        else
        {
            $res = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $area_id
            ];
        }
        return $res;
    }


    /**
     * 支付
     * @return array|mixed
     */
    public function pay()
    {
        if(!input("?param.ids"))
        {
            return error_code(13100);
        }
        if(!input("?param.payment_code"))
        {
            return error_code(10055);
        }
        if(!input("?param.payment_type"))
        {
            return error_code(10051);
        }

        //支付的时候，有一些特殊的参数需要传递到支付里面，这里就是干这个事情的,key=>value格式的一维数组
        $data = input('param.');
        if(!isset($data['params']))
        {
            $params = [];
        }
        else
        {
            $params = $data['params'];
        }

        $billPaymentsModel = new BillPayments();
        //生成支付单,并发起支付
        return $billPaymentsModel->pay(input('param.ids'),input('param.payment_code'),$this->userId,input('param.payment_type'),$params);
    }


    /**
     * 订单评价接口
     * @return array|mixed
     */
    public function orderEvaluate()
    {
        if(!input('items/a'))
        {
            //缺少评价商品信息
            return error_code(13400);
        }
        if(!input('order_id'))
        {
            //没有order_id
            return error_code(13401);
        }

        $order_id = input('order_id');
        $items = input('items/a');

        //添加评价
        $model = new GoodsComment();
        $result = $model->addComment($order_id, $items, $this->userId);
        return $result;
    }


    /**
     * 获取用户默认收货地址
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDefaultShip()
    {
        $user_id = $this->userId;
        $model = new UserShip();
        $res = $model->getUserDefaultShip($user_id);
        return $res;
    }


    /**
     * 判断是否签到
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isSign()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        $res = $userPointLog->isSign($user_id);
        return $res;
    }


    /**
     * 签到操作
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        $res = $userPointLog->sign($user_id);
        return $res;
    }


    /**
     * 积分记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pointLog()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        $res = $userPointLog->pointLogList($user_id);
        return $res;
    }


    /**
     * 获取用户积分
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserPoint()
    {
        $user_id = $this->userId;
        $order_money = Request::param('order_money', 0);
        $userModel = new UserModel();
        return $userModel->getUserPoint($user_id, $order_money);
    }


    /**
     * 获取店铺设置
     * @return array|mixed
     */
    public function getSetting()
    {
        $result = [
            'status' => true,
            'msg' => '',
            'data' => ''
        ];

        $key = input('param.key/s');
        if(!$key) return error_code(10003);
        $result['data'] = getSetting($key);

        switch ($key)
        {
            case 'shop_logo':
                $result['data'] = _sImage($result['data']);
                break;
            default:
                break;
        }
        return $result;
    }


    /**
     * 获取我的银行卡列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankCardList()
    {
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->getMyBankcardsList($this->userId);
    }


    /**
     * 获取默认的银行卡
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultBankCard()
    {
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->defaultBankCard($this->userId);
    }


    /**
     * 添加银行卡
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addBankCard()
    {
        $bankCardsModel = new UserBankcards();
        $data = [
            'bank_name' => input('param.bankName'), //银行名
            'bank_code' => input('param.bankCode'), //银行编码
            'bank_area_id' => input('param.areaId/d'), //开户行地区
            'account_bank' => input('param.accountBank'), //开户行名称
            'account_name' => input('param.accountName'), //持卡人
            'card_number' => input('param.cardNumber'), //银行卡号
            'card_type' => input('param.cardType/d'), //银行卡类型
            'is_default' => input('param.isDefault', 2) //是否默认
        ];
        return $bankCardsModel->addBankcards($this->userId, $data);
    }


    /**
     * 删除银行卡
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function removeBankCard()
    {
        $card_id = input('param.id/d');
        if (!$card_id) return error_code(10051);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->delBankcards($this->userId, $card_id);
    }


    /**
     * 设置默认银行卡
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setDefaultBankCard()
    {
        $card_id = input('param.id/d');
        if (!$card_id) return error_code(10051);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->setDefault($this->userId, $card_id);
    }


    /**
     * 获取银行卡信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankCardInfo()
    {
        $card_id = input('param.id/d');
        if (!$card_id) return error_code(10051);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->getBankcardInfo($this->userId, $card_id);
    }


    /**
     * 获取银行卡组织信息
     * @return array|mixed
     */
    public function getBankCardOrganization()
    {
        $card_code = input('param.card_code');
        if (!$card_code) return error_code(11017);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->bankCardsOrganization($card_code);
    }


    /**
     * 用户修改密码
     * @return array|mixed
     */
    public function editPwd()
    {
        if (!input("?param.pwd")) return error_code(11012);
        if (!input('param.newpwd')) return error_code(11013);
        if (!input('param.repwd')) return error_code(11014);
        $data = [
            'password' => input('param.pwd'),
            'newPwd' => input('param.newpwd'),
            'rePwd' => input('param.repwd'),
            'user_id' => $this->userId
        ];
        $userModel = new userModel();
        return $userModel->checkCode($data);
    }


    /**
     * 用户找回密码
     * @return array|mixed
     */
    public function forgotPwd()
    {
        if (!input('param.mobile')) return error_code(11051);
        if (!input('param.code')) return error_code(10013);
        if (!input('param.newpwd')) return error_code(11013);
        if (!input('param.repwd')) return error_code(11014);
        $data = [
            'mobile' => input('param.mobile'),
            'code' => input('param.code'),
            'newPwd' => input('param.newpwd'),
            'rePwd' => input('param.repwd'),
            'user_id' => $this->userId
        ];
        $userModel = new userModel();
        return $userModel->checkCode($data);
    }


    /**
     * 获取我的余额明细
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userBalance()
    {
        $page = input('param.page', 1);
        $limit = input('param.limit', config('jshop.page_limit'));
        $order = input('param.order','ctime desc');
        $balanceModel = new Balance();
        return $balanceModel->getBalanceList($this->userId, $order, $page, $limit);
    }


    /**
     * 获取用户推荐列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recommend()
    {
        $page = input('param.page', 1);
        $limit = input('param.limit', config('jshop.page_limit'));
        $userModel = new UserModel();
        return $userModel->recommendList($this->userId, $page, $limit);
    }


    /**
     * 邀请码
     * @return array
     */
    public function sharecode()
    {
        $userModel = new UserModel();
        return $result = [
            'status' => true,
            'data' => $userModel->getShareCodeByUserId($this->userId),
            'msg' => ''
        ];
    }


    /**
     * 用户提现申请
     * @return array|mixed
     */
    public function cash()
    {
        $money = input('param.money');
        $bankcard_id = input('param.cardId');
        if (!$money) return error_code(11018);
        if (!$bankcard_id) return error_code(11017);
        $userToCashModel = new UserTocash();
        return $userToCashModel->tocash($this->userId, $money, $bankcard_id);
    }


    /**
     * 获取用户提现记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cashList()
    {
        $page = input('param.page', 1);
        $limit = input('param.limit', config('jshop.page_limit'));
        $type = input('param.type', '');
        $userToCashModel = new UserTocash();
        return $userToCashModel->userToCashList($this->userId, $page, $limit, $type);
    }


    /**
     * 获取信任登录内容，标题，图标，名称，跳转地址
     * @return array
     */
    public function getTrustLogin(){
        $data = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        #$jshopHost = Container::get('request')->domain();
        #$jshopHost = 'http://b2c.jihainet.com';
        $url = input('url/s','');//前台地址
        $uuid = input('uuid/s','');//前台用户信息，记录是同一个用户
        if(!$url){
            $url = Container::get('request')->domain();
        }
        $params['url'] = $url.'/wap/index.html#/author';
        $params['uuid'] = $uuid;
        if(!$params['url']||!$uuid)
        {
            $data['status'] = false;
            $data['msg'] = '获取失败';
            return $data;
        }
        if(checkAddons('trustlogin'))
        {
            $data['data'] = Hook('trustlogin',$params);
        }
        return $data;
    }


    /**
     * 根据code 获取用户信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function trustCallBack()
    {
        $returnData = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        $params['code'] = input('code');
        $params['type'] = input('type');
        $params['state'] = input('state');
        $params['uuid'] = input('uuid/s','');
        if(!$params['code'] || !$params['type'] ||! $params['state'])
        {
            $returnData['msg'] = '关键参数丢失';
            return $returnData;
        }
        $data = [];
        //此处钩子只能取第一个插件的返回值
        if(checkAddons('trustcallback'))
        {
            $data = Hook('trustcallback', $params);
        }

        if(isset($data[0]['status']) && !$data[0]['status'])
        {
            return $returnData;
        }

        $user = $data[0]['data'];
        $userWxModel = new UserWx();
        if(isset($user['unionId']) && $user['unionId'])
        {
            //有这个unionid的时候，用这个判断
            $where['unionid'] = $user['unionId'];
        }
        elseif(isset($user['openid']) && $user['openid'])
        {
            //有这个openid的时候，先用unionid，再用这个判断
            $where['openid'] = $user['openid'];
        }
        $where['type'] = $userWxModel::TYPE_OFFICIAL;
        $wxInfo = $userWxModel->where($where)->find();
        if($wxInfo)
        {
            //存在第三方账号，检查是否存在会员，存在的话，直接登录，不存在则绑定手机号
            if($wxInfo['user_id'])
            {
                //直接登录
                $userModel = new UserModel();
                $userInfo = $userModel->where(array('id'=>$wxInfo['user_id']))->find();
                if(!$userInfo)
                {
                    $result['msg'] = '没有找到此账号';
                    return $result;
                }
                return $userModel->setSession($userInfo,2,1);
            }
            else
            {
                Cache::set('user_wx_'.$params['uuid'],json_encode($wxInfo));
                $returnData['msg'] = '请绑定手机号';
                $returnData['status'] = true;
                $returnData['data'] = [
                    'is_new' => true
                ];
                return $returnData;
            }
        }
        else
        {
            //不存在第三方账号,先插入第三方账号，然后跳转绑定手机号页面
            $res = $userWxModel->toAddWx($user);
            if(!$res['status'])
            {
                $returnData['msg'] = $res['msg'];
                return $returnData;
            }
            Cache::set('user_wx_'.$params['uuid'],json_encode($res['data']));

            $returnData['msg'] = '保存成功，请绑定手机号';
            $returnData['status'] = true;
            $returnData['data'] = [
                'is_new' => true
            ];
            return $returnData;
        }
        return $returnData;
    }


    /**
     * 用户手机号绑定,然后调用登录，返回登录信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function trustBind()
    {
        $returnData = [
            'status' => false,
            'msg' => '绑定失败',
            'data' => []
        ];
        $data = input('param.');
        if (!$data['uuid']) {
            return $returnData;
        }
        $wxinfo = Cache::get('user_wx_' . $data['uuid']);
        $wxinfo = json_decode($wxinfo, true);
        $data['user_wx_id'] = $wxinfo['id'];
        $userModel = new UserModel();
        $userWxModel = new UserWx();
        $wxInfo = $userWxModel->where(['id' => $data['user_wx_id']])->find();
        if (isset($wxInfo['user_id']) && $wxInfo['user_id']) {
            $returnData['msg'] = '请勿重复绑定';
            return $returnData;
        }
        return $userModel->smsLogin($data, 2);
    }


    /**
     * 是否开启积分
     * @return array
     */
    public function isPoint()
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => 2
        ];
        $settingModel = new Setting();
        $return['data'] = $settingModel->getValue('point_switch');
        return $return;
    }


    /**
     * 获取我的要求相关信息
     * @return array
     * @throws \think\exception\DbException
     */
    public function myInvite()
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        //我的邀请码
        $code = $this->sharecode();
        $return['data']['code'] = $code['data'];
        //我邀请的人数
        $userModel = new UserModel();
        $where[] = ['pid', 'eq', $this->userId];
        $return['data']['number'] = $userModel->where($where)->count();
        //邀请赚的佣金 todo::暂时没有
        $return['data']['money'] = 0;
        //是否有上级
        $userInfo = $userModel->get($this->userId);
        $is_superior = false;
        if($userInfo['pid'] && $userInfo['pid'] != 0)
        {
            $is_superior = true;
        }
        $return['data']['is_superior'] = $is_superior;

        return $return;
    }


    /**
     * 设置我的上级邀请人
     * @return array
     * @throws \think\exception\DbException
     */
    public function activationInvite()
    {
        $code = Request::param('code');
        $userModel = new UserModel();
        return $userModel->setMyInvite($this->userId, $userModel->getUserIdByShareCode($code));
    }
}