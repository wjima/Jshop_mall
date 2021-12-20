<?php
namespace app\common\model;

use think\Db;
use think\model\concern\SoftDelete;

class BillReship extends Common
{
    protected $pk = 'reship_id';

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';

    const STATUS_WAIT_SHIP = 1;       //状态，等待发货
    const STATUS_SHIPPED = 2;        //状态，已发货
    const STATUS_SUCCESS = 3;       //状态，已收货


    /**
     * @param $user_id
     * @param $order_id
     * @param $aftersales_id
     * @param $aftersalesItems
     * @return array|mixed
     * @throws \Exception
     */
    public function toAdd($user_id,$order_id,$aftersales_id,$aftersalesItems)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        if(count($aftersalesItems) == 0){
            return error_code(13209);
        }

        $data['reship_id'] = get_sn(7);
        $data['order_id'] = $order_id;
        $data['aftersales_id'] = $aftersales_id;
        $data['user_id'] = $user_id;
        $re = $this->save($data);
        if($re){
            //保存退货单明细
            foreach($aftersalesItems as $v){
                $row['reship_id'] = $data['reship_id'];
                $row['order_items_id'] = $v['order_items_id'];
                $row['goods_id'] = $v['goods_id'];
                $row['product_id'] = $v['product_id'];
                $row['sn'] = $v['sn'];
                $row['bn'] = $v['bn'];
                $row['name'] = $v['name'];
                $row['image_url'] = $v['image_url'];
                $row['nums'] = $v['nums'];
                $row['addon'] = $v['addon'];

                $itemsData[] = $row;
            }
            $billReshipItemsModel = new BillReshipItems();
            if (!$billReshipItemsModel->saveAll($itemsData)) {
                return error_code(10004);
            }
        } else {
            return error_code(10004);
        }
        $result['status'] = true;
        $result['data'] = $data;
        return $result;
    }


    /**
     * 用户发送退货包裹
     * @param $user_id //用户id
     * @param $reship_id //退货单id
     * @param $logi_code //退货物流公司
     * @param $logi_no //退货订单号
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sendReship($user_id,$reship_id,$logi_code,$logi_no)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where = [
            'reship_id' => $reship_id,
            'user_id' => $user_id,
            'status' => self::STATUS_WAIT_SHIP
        ];
        $info = $this->where($where)->find();
        if(!$info){
            return error_code(13211);
        }
        $data['logi_code'] = $logi_code;
        $data['logi_no'] = $logi_no;
        $data['status'] = self::STATUS_SHIPPED;

        $this->where($where)->data($data)->update();
        hook("adminmessage",array('user_id'=>$user_id,"code"=>"after_ship","params"=>$reship_id));
        $result['status'] = true;
        $result['msg'] = '保存成功';
        return $result;

    }


    /**
     * 平台客服收到退款包裹，确认收货，这时候，在业务上可以让退款员去退款了
     * @param $reship_id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function confirmReship($reship_id)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];

        $where = [
            'reship_id' => $reship_id,
            'status'    => self::STATUS_SHIPPED
        ];
        $info  = $this->where($where)->find();


        if (!$info) {
            return error_code(13211);
        }
        Db::startTrans();

        $data['status'] = self::STATUS_SUCCESS;

        $this->where($where)->data($data)->update();

        //退货
        $billReshipItemsModel = new BillReshipItems();
        $items      = $billReshipItemsModel->where([[
            'reship_id', '=', $info['reship_id']
        ]])->select();
        if (!$items->isEmpty()) {
            $goodsModel = new Goods();
            foreach ($items as $key => $val) {
                $goodsRes = $goodsModel->changeStock($val['product_id'], 'return', $val['nums']);
                if (!$goodsRes['status']) {
                    Db::rollback();
                    return $goodsRes;
                }
            }
        }
        Db::commit();
        $result['status'] = true;
        $result['msg']    = '收货成功';
        return $result;
    }



    /**
     * @param $post
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
        // $re['sql'] = $this->getLastSql();
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;


        return $re;
    }


    /**
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['reship_id']) && $post['reship_id'] != ""){
            $where[] = ['reship_id', 'like', '%'.$post['reship_id'].'%'];
        }
        if(isset($post['order_id']) && $post['order_id'] != ""){
            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
        }
        if(isset($post['id']) && $post['id'] != ""){
            $where[] = ['reship_id', 'in', $post['id']];
        }
        if(isset($post['date']) && $post['date'] != "")
        {
            $date = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0].' 00:00:00', $date[1].' 23:59:59']];
        }
        if(isset($post['logi_no']) && $post['logi_no'] != ""){
            $where[] = ['logi_no', 'like', '%'.$post['logi_no'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }

        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "status asc,utime desc";
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @param $list //array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v)
        {
            if($v['status']) {
                $list[$k]['status_name'] = config('params.bill_reship')['status'][$v['status']];
            }
            if($v['user_id']) {
                $list[$k]['user_id'] = get_user_info($v['user_id'], 'showname');
            }

            if(isset($v['ctime']) && $v['ctime']) {
                $list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
            }else{
                $list[$k]['ctime'] = '';
            }

            if(isset($v['utime']) && $v['utime']) {
                $list[$k]['utime'] = date('Y-m-d H:i:s',$v['utime']);
            }else{
                $list[$k]['utime'] = '';

            }

            if($v['logi_code']) {
                $list[$k]['logi_code'] = get_logi_info($v['logi_code']);

            }
        }
        return $list;
    }


    /**
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BillReshipItems','reship_id','reship_id');
    }


    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id' => 'reship_id',
                'desc' => '退货单号',
                'modify'=>'convertString'
            ],
            [
                'id' => 'order_id',
                'desc' => '订单号',
                'modify'=>'convertString'
            ],
            [
                'id' => 'user_id',
                'desc' => '用户',
            ],
            [
                'id' => 'status_name',
                'desc' => '状态',
            ],

            [
                'id' => 'logi_code',
                'desc' => '物流公司',
            ],
            [
                'id' => 'logi_no',
                'desc' => '物流单号',

            ],
            [
                'id' => 'ctime',
                'desc' => '申请时间',

            ],
            [
                'id' => 'utime',
                'desc' => '更新时间',

            ],
        ];
    }


    /**
     * 获取csv数据
     * @param $post
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCsvData($post)
    {
        $result = error_code(10083);
        $header = $this->csvHeader();
        $userData = $this->getExportList($post);

        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body = [];
            $i = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                foreach ($header as $hk => $hv) {
                    if (isset($val[$hv['id']]) && $val[$hv['id']] && isset($hv['modify'])) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) &&!empty($val[$hv['id']])) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }
            }
            $result['status'] = true;
            $result['msg'] = '导出成功';
            $result['data'] = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }


    /**
     * 导出验证
     * @param array $params
     * @return array
     */
    public function exportValidate(&$params = [])
    {
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '验证成功',
        ];
        return $result;
    }


    /**
     * 导出格式
     * @param array $post
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getExportList($post = [])
    {
        $return_data =  error_code(10025);
        $where = [];

        if(isset($post['reship_id']) && $post['reship_id'] != ""){
            $where[] = ['reship_id', 'like', '%'.$post['reship_id'].'%'];
        }
        if(isset($post['order_id']) && $post['order_id'] != ""){
            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
        }
        if(isset($post['id']) && $post['id'] != ""){
            $where[] = ['reship_id', 'in', $post['id']];
        }
        if(isset($post['date']) && $post['date'] != "")
        {
            $date = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0].' 00:00:00', $date[1].' 23:59:59']];
        }
        if(isset($post['logi_no']) && $post['logi_no'] != ""){
            $where[] = ['logi_no', 'like', '%'.$post['logi_no'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }

        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        $list = $this->where($where)
            ->order('status asc,utime desc')
            ->select();

        if($list){
            $count = $this->where($where)->count();
            foreach($list as $k => $v) {
                if($v['status']) {
                    $list[$k]['status_name'] = config('params.bill_reship')['status'][$v['status']];
                }
                if($v['user_id']) {
                    $list[$k]['user_id'] = get_user_info($v['user_id'], 'showname');
                }

                if(isset($v['ctime']) && $v['ctime']) {
                    $list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
                }else{
                    $list[$k]['ctime'] = '';
                }

                if(isset($v['utime']) && $v['utime']) {
                    $list[$k]['utime'] = date('Y-m-d H:i:s',$v['utime']);
                }else{
                    $list[$k]['utime'] = '';

                }

                if($v['logi_code']) {
                    $list[$k]['logi_code'] = get_logi_info($v['logi_code']);

                }
            }
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $list,
                'count' => $count
            ];
        }
        return $return_data;
    }


    /**
     * @param $aftersales_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAftersalesStatus($aftersales_id)
    {
        $where[] = ['aftersales_id', 'eq', $aftersales_id];
        $info = $this->where($where)->find();
        if($info)
        {
            if($info['status'] == self::STATUS_WAIT_SHIP)
            {
                $text = '待发退货';
            }
            else if($info['status'] == self::STATUS_SHIPPED)
            {
                $text = '已发退货';
            }
            else if($info['status'] == self::STATUS_SUCCESS)
            {
                $text = '已收退货';
            }
            else
            {
                $text = '状态异常';
            }
        }
        else
        {
            $text = '待发退货';
        }
        return $text;
    }
}
