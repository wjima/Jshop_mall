<?php
namespace app\common\model;
use think\Db;
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
        'address'       =>  'require',
        'coordinate'    =>  'require',
        'longitude'     =>  'require',
        'latitude'     =>   'require',
    ];

    protected $msg  =   [
        'store_name.require'    =>  '请输入门店名称',
        'store_name.max'        =>  '门店名称不超过50个字符',
        'mobile.mobile'          => '手机号格式错误',
        'logo.require'          =>  '请上传门店logo',
        'address.require'       =>  '请输入门店详细地址',
        'coordinate.require'    =>  '请选择门店坐标位置',
        'longitude'             => '经度必须',
        'latitude'              => '纬度必须'
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
                return error_code(10004);
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
                return error_code(10004);
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
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeList()
    {
        $data = $this
            ->field('store_name,mobile,linkman,logo,area_id,address,coordinate')
            ->select();

        if(!$data->isEmpty())
        {
            $count = $this
                ->field('store_name,mobile,linkman,logo,area_id,address,coordinate')
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
            return  error_code(10025);
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


    /***
     * 获取默认店铺
     * @param string $longitude 经度
     * @param string $latitude 维度
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultStore($longitude = '', $latitude = '')
    {
        $return = error_code(10025);
        if (!$longitude || !$latitude) {

            $return['data'] = $this->order('ctime desc')->find();
            if ($return['data']) {
                $return['data']['all_address'] = get_area($return['data']['area_id']) . $return['data']['address'];
                $return['status']              = true;
                $return['msg']                 = '获取成功';
            }
        } else {
            if ($longitude && $latitude) {
                //距离计算
                $sqrt = 'SQRT(POW(SIN((' . $latitude . '*PI()/180-`latitude`*PI()/180)/2),2)+COS(' . $latitude . '*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN((' . $longitude . '*PI()/180-`longitude`*PI()/180)/2),2))';
                //查询结果排序
                $prefix = config('database.prefix');
                $sql    = "select `id`,`store_name`,`area_id`,`longitude`,`latitude`,`mobile`,`linkman`,`address`,`distance` from (select * ,ROUND(6378.138*2*ASIN($sqrt)*1000) AS 'distance' from  " . $prefix . "store" . "   order by 'distance' desc ) as a  ORDER BY `distance` asc limit 0,1";

                $res = Db::query($sql);

                $return['data'] = $res[0];
                if ($return['data']) {
                    $return['data']['all_address'] = get_area($return['data']['area_id']) . $return['data']['address'];
                    if ($longitude && $latitude) {

                        if ($return['data']['distance']) {
                            $return['data']['distance_c'] = bcdiv($return['data']['distance'], 1000, 2);
                            if ($return['data']['distance'] >= 1000) {
                                $return['data']['distance'] = bcdiv($return['data']['distance'], 1000, 2) . 'km';
                            } else {
                                $return['data']['distance'] = $return['data']['distance'] . 'm';
                            }
                        } else {
                            $return['data']['distance_c'] = '100';
                            $return['data']['distance']   = '未知';
                        }
                    }
                    $return['status'] = true;
                    $return['msg']    = '获取成功';
                }
            }
        }
        return $return;
    }


    /**
     * 获取全部店铺列表
     * @param string $key
     * @param bool $longitude
     * @param bool $latitude
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllStoreList($key = '', $longitude = false, $latitude = false)
    {
        $return = [
            'status' => false,
            'msg' => error_code(10025,true),
            'data' => [],
            'longitude' => $longitude,
            'latitude' => $latitude
        ];

        if($longitude && $latitude){
            //距离计算
            $sqrt = 'SQRT(POW(SIN(('.$latitude.'*PI()/180-`latitude`*PI()/180)/2),2)+COS('.$latitude.'*PI()/180)*COS(`latitude`*PI()/180)*POW(SIN(('.$longitude.'*PI()/180-`longitude`*PI()/180)/2),2))';
            //查询结果排序
            $prefix = config('database.prefix');
            if($key)
            {
                $where =  "'%".$key."%'" ;
                $sql = "select * from (select * ,ROUND(6378.138*2*ASIN($sqrt)*1000) AS distance from  " .$prefix ."store". "  where store_name like ".  $where ."  order by distance,id) as a ";
            }else{
                $sql = "select * from (select * ,ROUND(6378.138*2*ASIN($sqrt)*1000) AS distance from  " .$prefix ."store"."   order by distance,id) as a ";
            }

            $return['data']= Db::query($sql);
        }else{
            $where = [];
            if($key){
                $where[] = ['store_name','like','%'.$key.'%'];
            }
            $return['data'] = $this->where($where)->select();
        }


        if($return['data'])
        {
            foreach($return['data'] as &$v)
            {
                $v['all_address'] = get_area($v['area_id']).$v['address'];
                if($longitude && $latitude){
                    if($v['distance']){
                        if($v['distance'] >= 1000){
                            $v['distance'] = bcdiv($v['distance'],1000,2) . 'km';
                        }else{
                            $v['distance'] =  $v['distance'] . 'm';
                        }
                    }else{
                        $v['distance'] ='未知';
                    }
                }
                $v['logo'] = _sImage($v['logo']);

            }
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }
        return $return;
    }
}
