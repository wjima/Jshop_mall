<?php

namespace app\common\model;

use think\facade\Cache;

/**
 * 用户收货地址
 * Class UserShip
 * @package app\common\model
 */
class UserShip extends Common
{
    const SHIP_DEFAULT = 1;
    const SHIP_DEFAULT_NO = 2;

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
    protected $updateTime = 'utime';

    // /**
    //  * 存储收货地址
    //  * @param $data
    //  * @return int|mixed|string
    //  * @throws \think\Exception
    //  * @throws \think\db\exception\DataNotFoundException
    //  * @throws \think\db\exception\ModelNotFoundException
    //  * @throws \think\exception\DbException
    //  * @throws \think\exception\PDOException
    //  */
    // public function saveShip($data)
    // {
    //     $character = ["\r\n", "\n", "\r"];
    //     $data['address'] = str_replace($character, '', $data['address']);

    //     $where[] = ['user_id', 'eq', $data['user_id']];
    //     $where[] = ['area_id', 'eq', $data['area_id']];
    //     $where[] = ['address', 'eq', $data['address']];
    //     $where[] = ['name', 'eq', $data['name']];
    //     $where[] = ['mobile', 'eq', $data['mobile']];

    //     $res = $this->where($where)
    //         ->find();
    //     if($res)
    //     {
    //         $data['utime'] = time();
    //         $this->where($where)->update($data);
    //         $ship_id = $res['id'];
    //     }
    //     else
    //     {
    //         $awhere[] = ['user_id', 'eq', $data['user_id']];
    //         $awhere[] = ['is_def', 'eq', self::SHIP_DEFAULT];
    //         $flag = $this->where($awhere)->find();
    //         if(!$flag)
    //         {
    //             //没有默认
    //             $ship_data['is_def'] = self::SHIP_DEFAULT;
    //         }
    //         else
    //         {
    //             //有默认
    //             $ship_data['is_def'] = $data['is_def']?$data['is_def']:self::SHIP_DEFAULT_NO;
    //         }

    //         $ship_data['user_id'] = $data['user_id'];
    //         $ship_data['area_id'] = $data['area_id'];
    //         $ship_data['address'] = $data['address'];
    //         $ship_data['name'] = $data['name'];
    //         $ship_data['mobile'] = $data['mobile'];
    //         $ship_data['utime'] = time();
    //         $ship_id = $this->insertGetId($ship_data);
    //     }
    //     return $ship_id;
    // }


    // /**
    //  *
    //  *
    //  *  添加收货地址
    //  * @param $data
    //  * @return array
    //  * @throws \think\Exception
    //  * @throws \think\db\exception\DataNotFoundException
    //  * @throws \think\db\exception\ModelNotFoundException
    //  * @throws \think\exception\DbException
    //  * @throws \think\exception\PDOException
    //  */
    // public function vueSaveShip($data)
    // {

    //     $result = error_code(10004);

    //     $checkStatus = $this->checkData($data);

    //     if (!$checkStatus['status'])
    //     {
    //         return $checkStatus;
    //     }

    //     $character = ["\r\n", "\n", "\r"];
    //     $data['address'] = str_replace($character, '', $data['address']);

    //     $where = [];
    //     $where[] = ['user_id', 'eq', $data['user_id']];
    //     $where[] = ['area_id', 'eq', $data['area_id']];
    //     $where[] = ['address', 'eq', $data['address']];
    //     $where[] = ['name', 'eq', $data['name']];
    //     $where[] = ['mobile', 'eq', $data['mobile']];

    //     $res_data = $this->where($where)->find();
    //     if($res_data)
    //     {
    //         if($data['is_def'] === self::SHIP_DEFAULT)
    //         {
    //             //查找该用户是否有默认的地址
    //             $defData = $this->where(['user_id' => $data['user_id'], 'is_def' => self::SHIP_DEFAULT])->select();
    //             if(count($defData)>0)
    //             {
    //                 foreach($defData as $k => $v)
    //                 {
    //                     $this->where('id',$v['id'])->update(['is_def' => self::SHIP_DEFAULT_NO]);
    //                 }
    //             }
    //         }
    //         $setData['is_def'] = $data['is_def'] ? $data['is_def'] : self::SHIP_DEFAULT_NO;
    //         $setData['utime'] = time();
    //         $this->where($where)->update($setData);

    //         if ($this->allowField(true)->save($setData)) {
    //             $result['status'] = true;
    //             $result['msg'] = '保存成功';
    //         }
    //     }
    //     else
    //     {
    //         //如果设置的地址是默认的
    //         if($data['is_def'] == self::SHIP_DEFAULT)
    //         {
    //             //查找该用户是否有默认的地址
    //             $defData = $this->where(['user_id' => $data['user_id'], 'is_def' => self::SHIP_DEFAULT])->select();
    //             if(count($defData)>0)
    //             {
    //                 foreach($defData as $k => $v)
    //                 {
    //                     $this->where('id',$v['id'])->update(['is_def' => self::SHIP_DEFAULT_NO]);
    //                 }
    //             }
    //         }
    //         $ship_data = [
    //             'user_id' => $data['user_id'],
    //             'area_id' => $data['area_id'],
    //             'address' => htmlentities($data['address']),
    //             'name' => htmlentities($data['name']),
    //             'mobile' => $data['mobile'],
    //             'utime' => time(),
    //             'is_def' => $data['is_def'] ? $data['is_def'] : self::SHIP_DEFAULT_NO
    //         ];

    //         if ($this->allowField(true)->save($ship_data)) {
    //             $result['status'] = true;
    //             $result['msg'] = '保存成功';
    //         }
    //     }

    //     return $result;
    // }


    /**
     *
     *  验证信息
     * @param $data
     * @return array
     */
    protected function checkData($data)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];

        $validate = new \app\common\validate\UserShip();

        if (!$validate->check($data)) {
            $result['msg'] = $validate->getError();
            return $result;
        }

        $result['status'] = true;
        return $result;
    }


    /**
     * 新增&编辑收货地址
     * @param $data
     * @param $user_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editShip($data, $user_id)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];

        $lock_key = 'user_edit_ship_' . $user_id;//防止高并发重复签到问题
        if (!Cache::has($lock_key)) {
            Cache::set($lock_key, '1', 3);

            // 收货地址验证
            $checkStatus = $this->checkData($data);

            if (!$checkStatus['status']) {
                return $checkStatus;
            }
            if ($data['address']) {
                $character       = ["\r\n", "\n", "\r", "\t", "+", "\\"];
                $data['address'] = str_replace($character, '', $data['address']);
            }

            $ship_data = [
                'user_id' => $user_id,
                'area_id' => $data['area_id'],
                'address' => htmlentities($data['address']),
                'name'    => htmlentities($data['name']),
                'mobile'  => $data['mobile'],
                'utime'   => time(),
                'is_def'  => $data['is_def'] ? $data['is_def'] : self::SHIP_DEFAULT_NO
            ];

            if (isset($data['id'])) {
                //编辑
                $where[] = ['id', 'eq', $data['id']];
                $where[] = ['user_id', 'eq', $user_id];
                $oldData = $this->where($where)->find();
                if ($oldData) {
                    if ($data['is_def'] == self::SHIP_DEFAULT) {
                        $where1[] = ['user_id', 'eq', $user_id];
                        $where1[] = ['is_def', 'eq', self::SHIP_DEFAULT];
                        $defData  = $this->where($where1)->select();
                        foreach ($defData as $k => $v) {
                            $this->where('id', $v['id'])->update(['is_def' => self::SHIP_DEFAULT_NO]);
                        }
                    }
                    if ($this->allowField(true)->save($ship_data, ['id' => $data['id'], 'user_id' => $user_id])) {
                        $result['status'] = true;
                        $result['msg']    = '成功';
                        Cache::rm($lock_key);
                    } else {
                        Cache::rm($lock_key);
                        return error_code(10004);
                    }
                } else {
                    Cache::rm($lock_key);
                    return error_code(11062);
                }
            } else {
                //新增
                //如果设置的地址是默认的
                if ($data['is_def'] == self::SHIP_DEFAULT) {
                    //查找该用户是否有默认的地址
                    $defData = $this->where(['user_id' => $user_id, 'is_def' => self::SHIP_DEFAULT])->select();
                    if (count($defData) > 0) {
                        foreach ($defData as $k => $v) {
                            $this->where('id', $v['id'])->update(['is_def' => self::SHIP_DEFAULT_NO]);
                        }
                    }
                }

                if ($this->allowField(true)->save($ship_data)) {
                    $result['status'] = true;
                    $result['msg']    = '成功';
                    $result['data']   = $this->id;
                    Cache::rm($lock_key);
                } else {
                    Cache::rm($lock_key);
                    return error_code(10004);
                }
            }
        } else {
            $result['msg'] = '请勿重复提交';
        }
        return $result;
    }


    /**
     * 收货地址删除
     * @param $id
     * @param $user_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function removeShip($id, $user_id)
    {
        $res = error_code(10023);
        $data = $this->where(['id' => $id, 'user_id' => $user_id])->find();
        //判断收货地址是否存在
        if ($data) {
            //如果要删除的是默认地址
            if ($data['is_def'] === self::SHIP_DEFAULT) {
                //查询是否有其他地址
                $where[] = ['id', 'neq', $id];
                $where[] = ['user_id', 'eq', $user_id];
                $list = $this->where($where)->order('utime desc')->find();
                if ($list) {
                    $this->startTrans();
                    try {
                        $this->save(['is_def' => self::SHIP_DEFAULT], ['id' => $list['id'], 'user_id' => $user_id]);
                        $this->where(['id' => $id, 'user_id' => $user_id])->delete();
                        $this->commit();
                        $res['status'] = true;
                        $res['msg'] = '删除成功';
                    } catch (\Exception $e) {
                        $this->rollback();
                        $res['msg'] = $e->getMessage();
                    }
                } else {
                    $this->where(['id' => $id, 'user_id' => $user_id])->delete();
                    $res['status'] = true;
                    $res['msg'] = '删除成功';
                }
            } else {
                $this->where(['id' => $id, 'user_id' => $user_id])->delete();
                $res['status'] = true;
                $res['msg'] = '删除成功';
            }
        } else {
            return error_code(11062);
        }
        return $res;
    }


    /**
     * 设置为默认地址
     * @param $id
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setDefaultShip($id, $user_id)
    {
        $res = error_code(10004);
        $data = $this->where(['id' => $id, 'user_id' => $user_id])->find();
        if ($data) {
            //是否有默认
            $def = $this->where(['user_id' => $user_id, 'is_def' => self::SHIP_DEFAULT])->find();
            if ($def) {
                $this->startTrans();
                try {
                    $this->save(['is_def' => self::SHIP_DEFAULT_NO], ['id' => $def['id'], 'user_id' => $user_id]);
                    $this->save(['is_def' => self::SHIP_DEFAULT], ['id' => $data['id'], 'user_id' => $user_id]);
                    $this->commit();
                    $res['status'] = true;
                    $res['msg'] = '保存成功';
                } catch (\Exception $e) {
                    $this->rollback();
                    $res['msg'] = $e->getMessage();
                }
            } else {
                //没有默认的直接设置为默认
                if ($this->save(['is_def' => self::SHIP_DEFAULT], ['id' => $data['id'], 'user_id' => $user_id])) {
                    $res['status'] = true;
                    $res['msg'] = '保存成功';
                }
            }
        } else {
            return error_code(11062);
        }
        return $res;
    }


    /**
     * 获取收货地址详情
     * @param $id
     * @param $user_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShipById($id, $user_id)
    {
        $ship_data = $this->where('id', 'eq', $id)->where('user_id', 'eq', $user_id)
            ->find();
        return $ship_data;
    }


    /**
     * 获取用户收货地址列表
     * @param $user_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserShip($user_id)
    {
        $info = $this->where('user_id', 'eq', $user_id)
            ->select();
        if ($info && count($info) > 0) {
            foreach ($info as $k => &$v) {
                $v['area_name'] = get_area($v['area_id']);
            }
        }

        return $info;
    }

    /**
     * 获取默认地址
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDefaultShip($user_id)
    {
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['is_def', 'eq', 1];
        $res = $this->where($where)
            ->order('utime desc')
            ->find();
        if ($res !== false) {
            if (isset($res['area_id']) && $res['area_id']) {
                $res['area_name'] = get_area($res['area_id']);
            } else {
                $res = [];
            }
            $return = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        } else {
            return error_code(10025);
        }
        return $return;
    }
}
