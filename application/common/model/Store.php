<?php
namespace app\common\model;
use think\Validate;

/**
 * Class Store
 * @package app\common\model
 */
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
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if(isset($post['limit']))
        {
            $limit = $post['limit'];
        }
        else
        {
            $limit = config('paginate.list_rows');
        }

        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])
            ->where($tableWhere['where'])
            ->order($tableWhere['order'])
            ->paginate($limit);
        $data = $this->tableFormat($list->getCollection());
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }


    /**
     * 门店添加
     * @param array $data
     * @return array
     */
    public function addData($data = [])
    {
        $result = ['status' => true, 'msg' => '保存成功','data' => ''];
        $validate = new Validate($this->rule,$this->msg);
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        }
        else
        {
            if(!$this->allowField(true)->save($data))
            {
                $result['status'] = false;
                $result['msg'] = '保存失败';
            }
        }
        return $result;
    }


    /**
     * 门店修改
     * @param array $data
     * @return array
     */
    public function editData($data = [])
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data' => ''
        ];
        $validate = new Validate($this->rule,$this->msg);
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        }
        else
        {
            if(!$this->allowField(true)->save($data,['id' => $data['id']]))
            {
                $result['status'] = false;
                $result['msg'] = '保存失败';
            }
        }
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
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
     * 获取商户门店
     * @param int $seller_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeList($seller_id = 0)
    {
        $data = $this
            ->field('seller_id,store_name,mobile,linkman,logo,area_id,address,coordinate')
            ->where('seller_id',$seller_id)
            ->select();

        if(!$data->isEmpty())
        {
            $count = $this
                ->field('seller_id,store_name,mobile,linkman,logo,area_id,address,coordinate')
                ->where('seller_id',$seller_id)
                ->count();

            foreach($data as $key => $val)
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
        }
        else
        {
            $result = [
                'status'=> false,
                'msg'   => '获取失败',
                'data'  => ''
            ];
        }
        return $result;
    }


    /**
     * 获取店铺名称
     * @param $store_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreName($store_id)
    {
        $where[] = ['id', 'eq', $store_id];
        $result = $this->field('store_name')->where($where)->find();
        return $result['store_name']?$result['store_name']:'';
    }


    /**
     * 判断店铺是否存在
     * @param $store_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeExist($store_id)
    {
        $where[] = ['id', 'eq', $store_id];
        $result = $this->where($where)->find();
        return $result?true:false;
    }


    /**
     * 获取全部店铺
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllList()
    {
        return $this->field('id,store_name')->select();
    }


    /**
     * 获取默认店铺
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultStore()
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        $return['data'] = $this->order('ctime desc')->find();
        if($return['data'])
        {
            $return['data']['all_address'] = get_area($return['data']['area_id']).$return['data']['address'];
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }
        return $return;
    }


    /**
     * 获取全部店铺列表
     * @param string $key
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllStoreList($key = '')
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];

        $where = [];
        if($key)
        {
            $where[] = ['store_name', 'like', '%'.$key.'%'];
        }

        $return['data'] = $this->where($where)->select();
        if($return['data'])
        {
            foreach($return['data'] as &$v)
            {
                $v['all_address'] = get_area($v['area_id']).$v['address'];
            }
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }
        return $return;
    }
}