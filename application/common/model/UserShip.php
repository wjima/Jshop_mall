<?php
namespace app\common\model;

/**
 * 用户收货地址
 * Class UserShip
 * @package app\common\model
 */
class UserShip extends Common
{
    const SHIP_DEFAULT = 1;
    const SHIP_DEFAULT_NO = 2;


    /**
     * 存储收货地址
     * @param $data
     * @return int|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveShip($data)
    {
        $where[] = ['user_id', 'eq', $data['user_id']];
        $where[] = ['area_id', 'eq', $data['area_id']];
        $where[] = ['address', 'eq', $data['address']];
        $where[] = ['name', 'eq', $data['name']];
        $where[] = ['mobile', 'eq', $data['mobile']];

        $res = $this->where($where)
            ->find();
        if($res)
        {
            $data['utime'] = time();
            $this->where($where)->update($data);
            $ship_id = $res['id'];
        }
        else
        {
            $ship_data['user_id'] = $data['user_id'];
            $ship_data['area_id'] = $data['area_id'];
            $ship_data['address'] = $data['address'];
            $ship_data['name'] = $data['name'];
            $ship_data['mobile'] = $data['mobile'];
            $ship_data['utime'] = time();
            $ship_data['is_def'] = $data['is_def']?$data['is_def']:self::SHIP_DEFAULT_NO;
            $ship_id = $this->insertGetId($ship_data);
        }
        return $ship_id;
    }


    /**
     * @param $data
     * @return int|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function vueSaveShip ($data)
    {
        $where = [];
        $where[] = ['user_id', 'eq', $data['user_id']];
        $where[] = ['area_id', 'eq', $data['area_id']];
        $where[] = ['address', 'eq', $data['address']];
        $where[] = ['name', 'eq', $data['name']];
        $where[] = ['mobile', 'eq', $data['mobile']];

        $res_data = $this->where($where)->find();
        if ($res_data) {
            if ($data['is_def'] === self::SHIP_DEFAULT) {
                $setData['is_def'] = self::SHIP_DEFAULT;
            }
            $setData['utime'] = time();
            $this->where($where)->update($setData);
            $ship_id = $res_data['id'];
        } else {
            // 如果设置的地址是默认的
            if ($data['is_def'] == self::SHIP_DEFAULT) {
                // 查找该用户是否有默认的地址
                $defData = $this->where(['user_id' => $data['user_id'], 'is_def' => self::SHIP_DEFAULT])->find();
                if ($defData) {
                    $this->where('id',$defData['id'])->update(['is_def' => self::SHIP_DEFAULT_NO]);
                }
            }
            $ship_data = [
                'user_id' => $data['user_id'],
                'area_id' => $data['area_id'],
                'address' => $data['address'],
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'utime' => time(),
                'is_def' => $data['is_def'] ? $data['is_def'] : self::SHIP_DEFAULT_NO
            ];
            $ship_id = $this->insertGetId($ship_data);
        }

        return $ship_id;
    }


    /**
     * @param $data
     * @param $user_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editShip ($data,$user_id)
    {
        $res = ['status'=>false,'msg'=>'保存失败','data'=>''];
        $res_data = $this->where(['id'=>$data['id'],'user_id'=>$user_id])->find();
        if ($res_data)
        {
            if ($this->allowField(true)->save($data,['id'=>$data['id'],'user_id'=>$user_id])){
                $res['status'] = true;
                $res['msg'] = '保存成功';
            }
        } else {
            $res['msg'] = '该地址不存在';
        }
        return $res;
    }


    /**
     *
     *  收货地址删除
     * @param $id
     * @param $user_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function removeShip ($id, $user_id)
    {
        $res = ['status' => false, 'msg' => '删除失败', 'data' => ''];
        $data = $this->where(['id'=>$id,'user_id'=>$user_id])->find();
        // 判断收货地址是否存在
        if ($data) {
            // 如果要删除的是默认地址
            if ($data['is_def'] === self::SHIP_DEFAULT)
            {
                // 查询是否有其他地址
                $where[] = ['id','neq',$id];
                $where[] = ['user_id','eq',$user_id];
                $list = $this->where($where)->order('utime desc')->find();
                if ($list) {
                    $this->startTrans();
                    try {
                        $this->save([ 'is_def' => self::SHIP_DEFAULT ], [ 'id' => $list[ 'id' ], 'user_id' => $user_id ]);
                        $this->where([ 'id' => $id, 'user_id' => $user_id ])->delete();
                        $this->commit();
                        $res[ 'status' ] = true;
                        $res[ 'msg' ] = '删除成功';
                    } catch ( \Exception $e ) {
                        $this->rollback();
                        $res[ 'msg' ] = $e->getMessage();
                    }
                } else {
                    $this->where([ 'id' => $id, 'user_id' => $user_id ])->delete();
                    $res['status'] = true;
                    $res['msg'] = '删除成功';
                }
            } else {
                $this->where([ 'id' => $id, 'user_id' => $user_id ])->delete();
                $res['status'] = true;
                $res['msg'] = '删除成功';
            }
        } else {
            $res['msg'] = '该收货地址不存在';
        }
        return $res;
    }


    /**
     *
     *  设置为默认地址
     * @param $id
     * @param $user_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setDefaultShip ($id,$user_id)
    {
        $res = ['status'=>false,'msg'=>'保存失败','data'=>''];
        $data = $this->where(['id'=>$id,'user_id'=>$user_id])->find();
        if ($data)
        {
            // 是否有默认
            $def = $this->where(['user_id' => $user_id,'is_def' => self::SHIP_DEFAULT])->find();
            if ($def)
            {
                $this->startTrans();
                try {
                    $this->save(['is_def'=>self::SHIP_DEFAULT_NO],['id'=>$def['id'],'user_id'=>$user_id]);
                    $this->save(['is_def'=>self::SHIP_DEFAULT],['id'=>$data['id'],'user_id'=>$user_id]);
                    $this->commit();
                    $res['status'] = true;
                    $res['msg'] = '保存成功';
                } catch (\Exception $e){
                    $this->rollback();
                    $res['msg'] = $e->getMessage();
                }
            } else {
                // 没有默认的直接设置为默认
                if ($this->save(['is_def'=>self::SHIP_DEFAULT],['id'=>$data['id'],'user_id'=>$user_id]))
                {
                    $res['status'] = true;
                    $res['msg'] = '保存成功';
                }
            }
        } else {
            $res['msg'] = '该地址不存在';
        }
        return $res;
    }



    /**
     * 获取收货地址详情
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShipById($id,$user_id)
    {
        $ship_data = $this->where('id', 'eq', $id)->where('user_id','eq',$user_id)
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
        foreach($info as $k => &$v)
        {
            $v['area_name'] = get_area($v['area_id']);
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
        if($res['area_id'])
        {
            $res['area_name'] = get_area($res['area_id']);
            $return = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        }
        else
        {
            $return = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res
            ];
        }
        return $return;
    }
}