<?php
namespace app\common\model;
use org\Curl;
use think\model\concern\SoftDelete;
use think\Validate;

/**
 * 用户银行卡信息表
 * Class UserBankcards
 * @package app\common\model
 */
class UserBankcards extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const DEFAULT_YES = 1; //默认
    const DEFAULT_NO = 2; //不默认

    const BANK_TYPE_DC = 1;  // 储蓄卡
    const BANK_TYPE_CC = 2; // 信用卡

    //验证规则
    protected $rule = [
        'card_number' => 'require|number|length:16,19',
        'account_name' => 'require',
        'bank_name' => 'require',
        'bank_area_id' => 'require',
        'account_bank' => 'require',
        'card_type' => 'require'
    ];

    protected $msg = [
        'card_number.require' => '请输入银行卡号',
        'card_number.number' => '银行卡号必须是数字',
        'card_number.length' => '请输入16-19位银行卡号',
        'account_name.require' => '请输入开户账户名',
        'bank_name.require' => '请输入银行名称',
        'bank_area_id.require' => '请选择开户行所在地区',
        'account_bank.require' => '请输入开户行名称',
        'card_type.require' => '无效的卡类型'
    ];

    /**
     * 验证数据
     * @param $data
     * @return array
     */
    protected function checkData($data)
    {
        $validate = new Validate($this->rule, $this->msg);
        $result = [
            'status' => true,
            'msg' => '',
            'data' => ''
        ];

        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
            return $result;
        }
        return $result;
    }

    /**
     * 我的银行卡列表
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMyBankcardsList($user_id)
    {
        $return = [
            'status' => true,
            'msg' => '',
            'data' => []
        ];
        $where[] = ['user_id', 'eq', $user_id];
        $res = $this->field('id, bank_name, bank_area_id, account_bank, account_name, card_number, bank_code, card_type, is_default, ctime, utime')
            ->where($where)
            ->select();
        $return['data'] = $res;
        foreach($return['data'] as $k => $v)
        {
            $return['data'][$k]['bank_area_name'] = get_area($v['bank_area_id']);
            $return['data'][$k]['card_number'] = bankCardNoFormat($v['card_number']);
            $return['data'][$k]['card_type'] = config('params.bank_card')['type'][$v['card_type']];
            $return['data'][$k]['bank_logo'] = config('params.bank_card')['bank_logo_url'].$v['bank_code'];
        }

        return $return;
    }

    /**
     *
     *  添加银行卡
     * @param $user_id
     * @param $data
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addBankcards($user_id, $data)
    {
        $return = error_code(10038);

        //数据验证
        $flag = $this->checkData($data);
        if(!$flag['status'])
        {
            return $flag;
        }

        // 判断该卡是否已经添加
        $card = $this->where([
            'user_id' => $user_id,
            'card_number' => $data['card_number']
        ])->find();

        if ($card) {
            return error_code(11060);
        }
        $new_data = [
            'user_id' => $user_id,
            'bank_name' => $data['bank_name'],
            'bank_area_id' => $data['bank_area_id'],
            'account_bank' => htmlentities($data['account_bank']),
            'account_name' => htmlentities($data['account_name']),
            'bank_code' => $data['bank_code'],
            'card_number' => $data['card_number'],
            'card_type' => $data['card_type'],
            'is_default' =>( isset($data['is_default']) && $data['is_default']==self::DEFAULT_YES )? self::DEFAULT_YES : self::DEFAULT_NO
        ];

        if ($new_data['is_default'] == self::DEFAULT_YES)
        {
            // 如果要添加默认 先判断是否有默认卡
            $def = $this->where(['user_id' => $user_id, 'is_default' => self::DEFAULT_YES])->find();
            if ($def) {
                $this->startTrans();
                try {
                    $this->where(['user_id'=>$def['user_id'],'id'=>$def['id']])->update(['is_default' => self::DEFAULT_NO]);
                    $this->save($new_data);
                    $this->commit();
                    $return['status'] = true;
                    $return['msg'] = '保存成功';
                } catch (\Exception $e) {
                    $this->rollback();
                    $return['msg'] = error_code(10004,true);
                }
            } else {
                // 不是默认的直接添加
                if ($this->allowField(true)->save($new_data)) {
                    $return[ 'status' ] = true;
                    $return[ 'msg' ] = '保存成功';
                }
            }
        } else {
            if ($this->allowField(true)->save($new_data)) {
                $return[ 'status' ] = true;
                $return[ 'msg' ] = '保存成功';
            }
        }

        return $return;
    }


    /**
     *
     *  删除银行卡
     * @param $user_id
     * @param $id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delBankcards($user_id, $id)
    {
        $return = error_code(10023);
        $where[] = ['id', 'eq', $id];
        $where[] = ['user_id', 'eq', $user_id];
        // 先判断该银行卡是否存在
        $data = $this->where($where)->find();
        if ($data) {
            // 如果要删除的是默认的卡
            if ($data['is_default'] === self::DEFAULT_YES) {
                // 查询是否有其他银行卡
                $_where[] = ['id','neq',$id];
                $_where[] = ['user_id','eq',$user_id];
                $list = $this->where($_where)->order('ctime desc')->find();
                if ($list) {
                    $this->startTrans();
                    try {
                        $this->where([ 'id' => $list[ 'id' ], 'user_id' => $user_id ])->update(['is_default' => self::DEFAULT_YES]);
                        $this->where([ 'id' => $id, 'user_id' => $user_id ])->delete();
                        $this->commit();
                        $return[ 'status' ] = true;
                        $return[ 'msg' ] = '删除成功';
                    } catch ( \Exception $e ) {
                        $this->rollback();
                        $return[ 'msg' ] = error_code(10023,true);
                    }
                } else {
                    if ($this->where([ 'id' => $id, 'user_id' => $user_id ])->delete()) {
                        $return[ 'status' ] = true;
                        $return[ 'msg' ] = '删除成功';
                    }
                }
            } else {
                if ($this->where([ 'id' => $id, 'user_id' => $user_id ])->delete()) {
                    $return[ 'status' ] = true;
                    $return[ 'msg' ] = '删除成功';
                }
            }
        } else {
            return error_code(11060);
        }

        return $return;
    }


    /**
     *
     *  获取默认的银行卡
     * @param $user_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function defaultBankCard($user_id)
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        $where[] = ['user_id','eq',$user_id];
        $where[] = ['is_default','eq',self::DEFAULT_YES];
        $def_card = $this->where($where)->find();
        if ($def_card)
        {
            $def_card['bank_area_name'] = get_area($def_card['bank_area_id']);
            $def_card['card_number'] = bankCardNoFormat($def_card['card_number']);
            $def_card['card_type'] = config('params.bank_card')['type'][$def_card['card_type']];
            $def_card['bank_logo'] = config('params.bank_card')['bank_logo_url'].$def_card['bank_code'];
            $return['data'] = $def_card;
        } else {
            $card = $this->where('user_id', $user_id)->order('ctime desc')->find();
            if ($card) {
                $card['bank_area_name'] = get_area($card['bank_area_id']);
                $card['card_number'] = bankCardNoFormat($card['card_number']);
                $card['card_type'] = config('params.bank_card')['type'][$card['card_type']];
                $card['bank_logo'] = config('params.bank_card')['bank_logo_url'].$card['bank_code'];
                $return['data'] = $card;
            }
        }

        return $return;
    }
    /**
     * 修改银行卡
     * @param $user_id
     * @param $id
     * @param $data
     * @return array
     */
    public function editBankcards($user_id, $id, $data)
    {
        $return = error_code(10024);

        //数据验证
        $flag = $this->checkData($data);
        if(!$flag['status'])
        {
            return $flag;
        }

        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['id', 'eq', $id];

        $new_data = [
            'bank_name' => $data['bank_name'],
            'bank_area_id' => $data['bank_area_id'],
            'account_bank' => $data['account_bank'],
            'account_name' => $data['account_name'],
            'card_number' => $data['card_number'],
            'card_type' => $data['card_type'],
            'is_default' => $data['is_default']
        ];

        $res = $this->save($new_data, $where);
        $return['data'] = $res;

        if($res)
        {
            $return['status'] = true;
            $return['msg'] = '修改成功';
        }

        return $return;
    }


    /**
     *
     *  设置默认的银行卡
     * @param $user_id
     * @param $id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setDefault($user_id, $id)
    {
        $return = error_code(10081);

        $data = $this->where(['id'=>$id,'user_id'=>$user_id])->find();
        if ($data) {
            // 是否有默认
            $def = $this->where(['user_id' => $user_id,'is_default' => self::DEFAULT_YES])->find();
            $this->startTrans();
            try {
                $this->save(['is_default'=>self::DEFAULT_NO],['id'=>$def['id'],'user_id'=>$user_id]);
                $this->save(['is_default'=>self::DEFAULT_YES],['id'=>$data['id'],'user_id'=>$user_id]);
                $this->commit();
                $return['status'] = true;
                $return['msg'] = '保存成功';
            } catch (\Exception $e){
                $this->rollback();
                $return['msg'] = error_code(10004,true);
            }
        } else {
            return error_code(11061);
        }
        return $return;
    }

    /**
     * 获取银行卡信息
     * @param $user_id
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankcardInfo($user_id, $id)
    {
        $return = error_code(10025);

        $where[] = ['id', 'eq', $id];
        $where[] = ['user_id', 'eq', $user_id];

        $res = $this->field('id, bank_name, bank_area_id, account_bank, account_name, card_number, card_type, bank_code, is_default')
            ->where($where)
            ->find();
        if($res)
        {
            $res['bank_logo'] = config('params.bank_card')['bank_logo_url'].$res['bank_code'];
            $res['card_number_i'] = bankCardNoFormat($res['card_number']);
            $res['card_type_i'] = config('params.bank_card')['type'][$res['card_type']];
        }

        $return['data'] = $res;

        if($res)
        {
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }
        return $return;
    }


    /**
     *
     *  获取银行卡组织信息
     * @param $card_code
     *
     * @return array
     */
    public function bankCardsOrganization ($card_code)
    {
        $result = ['status' => true, 'msg' => '获取成功', 'data' => ''];
        $curl = new Curl();
        $url = 'https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo='.$card_code.'&cardBinCheck=true';
        $res = $curl->get($url);
        $res = json_decode($res, true);
        if (!$res['validated']) {
            return error_code(11021);
        } else {
            $card = [];
            $card['name'] = config('bank.bank_list')[$res['bank']];
            switch ($res['cardType']) {
                case 'DC':
                    $card['type'] = self::BANK_TYPE_DC;
                    $card['type_name'] = config('params.bank_card')['type'][self::BANK_TYPE_DC];
                    break;
                case 'CC':
                    $card['type'] = self::BANK_TYPE_CC;
                    $card['type_name'] = config('params.bank_card')['type'][self::BANK_TYPE_CC];
                    break;
                default:
                    break;
            }
            $card['bank_code'] = $res['bank'];
            $result['data'] = $card;
        }
        return $result;
    }


}