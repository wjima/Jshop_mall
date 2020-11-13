<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Validate;

class AdvertPosition extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    protected $rule =   [
        'name'  =>  'require|max:50',
        'sort'  =>   'number',
    ];

    protected $msg  =   [
        'name.require'  =>  '名称必须',
        'name.max'      =>  '名称最多不能超过50个字符',
        'sort.number'   =>  '排序只能是数字格式',
    ];


    /**
     * @param $post
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);

        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }


    /**
     *  获取广告位列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        return $this->field('id,code,name')->select();
    }


    /**
     *
     *  添加广告位
     * @param $data
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addData($data)
    {
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        $validate = new Validate($this->rule,$this->msg);
        // 验证
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            // 判断该模板是否已经添加
            if ($this->where('code', $data['code'])->find()) {
                $result[ 'status' ] = false;
                $result[ 'msg' ] = error_code(10820,true);  //该广告位模板已经添加
            } else {
                // 写入数据
                if (!$this->allowField(true)->save($data)) {
                    $result['status'] = false;
                    $result['msg'] = error_code(10004,true);
                }
            }
        }
        return $result;
    }


    /**
     *  广告位编辑更新
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data,['id'=>$data['id']])) {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        return $result;
    }


    /**
     *
     *  广告位删除
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del($id=0)
    {
        //广告位下有广告禁止删除
        $result = ['status' => true,'msg' => '删除成功','data'=>''];
        if ($this->advert()->where('position_id', $id)->find()) {
            $result['status'] = false;
            $result['msg'] = error_code(10821,true);    //该广告位下有广告删除失败
        } else {
            if (!$this->where('id', $id)->delete()) {
                $result['status'] = false;
                $result['msg'] = error_code(10023,true);
            }
        }
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $val){
            $val['ctime'] = getTime($val['ctime']);
            $val['utime'] = getTime($val['utime']);
        }
        return $list;
    }


    /**
     * where 搜索条件
     * author: tianyu
     * @param $post
     *
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'eq', $post['name']];
        }
        if(isset($post['utime']) && $post['utime'] != ""){
            $date_array = explode('~',$post['utime']);
            $sutime = strtotime($date_array[0].'00:00:00',time());
            $eutime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['utime', ['EGT',$sutime],['ELT',$eutime],'and'];
        }
        if(isset($post['state']) && $post['state'] != ""){
            $where[] = ['state', 'eq', $post['state']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


//    public function changeState($id=0,$state=false)
//    {
//        $where[] = ['id','eq',$id];
//        $result = ['status'=>true,'msg'=>'','data'=>''];
//        switch($state)
//        {
//            case 'true':
//                $this->save(['state'=>self::POSITION_STATE_YES],$where);
//                $result['msg'] = '已'.config('params.advertisement')['position'][self::POSITION_STATE_YES];
//                break;
//            case 'false':
//                $this->save(['state'=>self::POSITION_STATE_NO],$where);
//                $result['msg'] = '已'.config('params.advertisement')['position'][self::POSITION_STATE_NO];
//                break;
//            default:
//                $result['status'] = false;
//                $result['msg'] = '非法操作';
//                break;
//        }
//
//        return $result;
//    }


    /**
     *  获取广告位列表
     * User:tianyu
     * @param int $page
     * @param int $limit
     * @param string $order
     */
    public function  getOptionsList($page,$limit)
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $field = 'id,name,code';
        $list = $this->field($field)->page($page,$limit)->select();

        $count = $this->field($field)->count();

        $result['data'] = [
            'list' => $list,
            'count' => $count
        ];

        return $result;
    }

    /**
     *  广告位 和 广告 一对多关联
     * User:tianyu
     * @return \think\model\relation\HasMany
     */
    public function advert()
    {
        return $this->hasMany('advertisement','position_id','id');
    }
}