<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/3/19 0019
 * Time: 13:56
 */

namespace app\common\model;

use think\Validate;

class Store extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


    protected $rule =   [
        'store_name'    =>  'require|max:50',
        'mobile'        =>  'mobile',
        'linkman'       =>  'require|max:25',
        'address'       =>  'require|chsAlphaNum',
        'coordinate'    =>  'require'
    ];

    protected $msg  =   [
        'store_name.require'    =>  '请输入门店名称',
        'store_name.max'        =>  '门店名称不超过50个字符',
        'mobile.mobile'          => '手机号格式错误',
        'logo.require'          =>  '请上传门店logo',
        'address.require'       =>  '请输入门店详细地址',
        'address.chsAlphaNum'   =>  '详细地址格式错误',
        'coordinate.require'    =>  '请选择门店坐标位置'
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
     *  门店添加
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function addData( $data= [] )
    {

        $result = ['status' => true, 'msg' => '保存成功','data' => ''];

        $validate = new Validate($this->rule,$this->msg);

        if ( !$validate->check($data) )
        {
            $result['status'] = false;

            $result['msg'] = $validate->getError();

        } else {

            if (!$this->allowField(true)->save($data)) {

                $result['status'] = false;

                $result['msg'] = '保存失败';
            }
        }

        return $result;
    }


    /**
     *  门店修改
     * User:tianyu
     * @param array $data
     * @return array
     */
    public function editData( $data= [] )
    {

        $result = [ 'status' => true, 'msg' => '保存成功', 'data' => ''];

        $validate = new Validate($this->rule,$this->msg);

        if ( !$validate->check($data) )
        {
            $result['status'] = false;

            $result['msg'] = $validate->getError();

        } else {

            if (!$this->allowField(true)->save($data,['id' => $data['id']])) {

                $result['status'] = false;

                $result['msg'] = '保存失败';
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
        foreach( $list as $val )
        {
            $val['logo'] = _sImage($val['logo']);
            $val['area'] = get_area($val['area_id']);
            $val['ctime'] = getTime($val['ctime']);
            $val['utime'] = getTime($val['utime']);
        }

        return $list;
    }


    /**
     *  获取商户门店
     * User:tianyu
     * @param string $seller_id
     * @return array
     */
    public function storeList( $seller_id = 0)
    {

        $data = $this
            ->field('seller_id,store_name,mobile,linkman,logo,area_id,address,coordinate')
            ->where('seller_id',$seller_id)
            ->select();

        if ( !$data->isEmpty() )
        {
            $count = $this
                ->field('seller_id,store_name,mobile,linkman,logo,area_id,address,coordinate')
                ->where('seller_id',$seller_id)
                ->count();

            foreach ( $data as $key => $val )
            {
                $data[$key]['area'] = get_area($val['area_id']);
            }

            $result = [
                'status'=> true,
                'msg'   => '获取成功',
                'data'  => [
                    'list' => $data,
                    'count' => $count
                ]
            ];

        }else{

            $result = [
                'status'=> false,
                'msg'   => '获取失败',
                'data'  => ''
            ];

        }

        return $result;

    }

}