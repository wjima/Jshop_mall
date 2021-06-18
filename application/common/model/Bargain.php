<?php

namespace app\common\model;

use think\Validate;
use think\Model;

class Bargain extends Common
{

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    protected $rule = [
        'name'        => 'require',
        'goods_id'    => 'require',
        'start_price' => 'require',
        'end_price'   => 'require',
        /*'bargain_max_price' => 'require',
        'bargain_min_price' => 'require',*/
        'sort'        => 'integer|gt:0',
        'total_times' => 'require',
        'total_times' => 'gt:0',

    ];

    protected $msg = [
        'name'                      => '请填写活动名称',
        'goods_id'                  => '请选择商品',
        'sort.integer'              => '权重请给定一个合理的数值',
        'sort.gt'                   => '权重数值必须大于零',
        'start_price.require'       => '砍价起始金额必填',
        'end_price.require'         => '砍价成交金额必填',
        'bargain_max_price.require' => '砍价每次最大金额必填',
        'bargain_min_price.require' => '砍价每次最小金额必填',
        'total_times.require'       => '请输入砍价次数',
        'total_times.gt'            => '砍价总次数必须大于0',
    ];

    const STATUS_ON = 1; //启用
    const STATUS_OFF = 2; //禁用

    const PROGRESS_STATUS_ING = '2'; //进行中
    const PROGRESS_STATUS_SUCCESS = '3'; //砍价成功
    const PROGRESS_STATUS_END = '4'; //砍价结束

    const TYPE_SELF = 1; //自己砍
    const TYPE_OTHER = 2; //给别人砍

    /**
     * 返回layui的table所需要的格式
     * @param $post
     * @return mixed
     */
    public function tableData($post, $api = false)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        if ($api) {


            $tableWhere            = $this->tableWhere($post);
            $tableWhere['where'][] = ['b.stime', '<=', time()];
            $tableWhere['where'][] = ['b.etime', '>', time()];

            //增加上下架过滤
            $goodsModel            = new Goods();
            $tableWhere['where'][] = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];

            $list = $this->field('b.*,g.name as goods_name,g.image_id as goods_image_id')
                ->alias('b')
                ->join("goods g", "g.id = b.goods_id")
                ->where($tableWhere['where'])
                ->order($tableWhere['order'])
                ->page($post['page'], $limit)
                ->select();

            $count = $this->field('b.*,g.name as goods_name,g.image_id as goods_image_id')
                ->alias('b')
                ->join("goods g", "g.id = b.goods_id")
                ->where($tableWhere['where'])
                ->order($tableWhere['order'])
                ->count();
            $data  = $this->tableFormat($list, $api);
        } else {
            $tableWhere = $this->tableWhere($post);

            $list  = $this->field('b.*,g.name as goods_name,g.image_id as goods_image_id')
                ->alias('b')
                ->join("goods g", "g.id = b.goods_id")
                ->where($tableWhere['where'])
                ->order($tableWhere['order'])
                ->paginate($limit);
            $data  = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $count = $list->total();
        }


        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $count;
        $re['data']  = $data;

        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['b.status', 'eq', $post['status']];
        }
        if (isset($post['name']) && $post['name'] != '') {
            $where[] = ['b.name', 'like', '%' . $post['name'] . '%'];
        }
        if (input('?param.date')) {
            $theDate = explode(' 到 ', input('param.date'));
            if (count($theDate) == 2) {
                $where[] = ['b.stime', '<', strtotime($theDate[1])];
                $where[] = ['b.etime', '>', strtotime($theDate[0])];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['b.sort' => 'asc', 'b.id' => 'desc'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = getTime($v['ctime']);
            $list[$k]['stime'] = getTime($v['stime']);
            $list[$k]['etime'] = getTime($v['etime']);
            $list[$k]['image'] = _sImage($v['goods_image_id']);
        }
        return $list;
    }


    //添加&编辑
    public function toAdd($data)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => [],
        ];

        // 校验数据
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['msg'] = $validate->getError();
            return $result;
        }
        $theDate = explode(' 到 ', $data['date']);
        if (count($theDate) != 2) {
            return error_code(15002);
        }
        $data['stime'] = strtotime($theDate[0]);
        $data['etime'] = strtotime($theDate[1]);

        //判断商品是否有参加过拼团
        $where[] = ['goods_id', '=', $data['goods_id']];
        $where[] = ['status', '=', self::STATUS_ON];

        if (isset($data['id']) && $data['id']) {
            $where[] = ['id', 'neq', $data['id']];
        }
        $re = $this->where($where)->find();
        if ($re) {
            $goodsModel = new Goods();
            $goodsInfo  = $goodsModel->field('name')->get($re['goods_id']);
            if ($goodsInfo) {
                $result['msg'] = error_code(17632, true, $goodsInfo['name']); //"商品：" . $goodsInfo['name'] . " 参加过砍价了"
                return $result;
            } else {
                return error_code(10000);
            }
        }

        if (isset($data['id']) && $data['id']) {
            $this->allowField(true)->save($data, $data['id']);
        } else {
            $this->allowField(true)->save($data);
        }

        $result['status'] = true;
        $result['msg']    = '操作成功';

        return $result;
    }

    /***
     * 获取砍价详情
     * @param $bargain_id
     * @param int $type
     * @param int $record_id
     * @param int $user_id
     * @return array
     */
    public function getBargainDetial($bargain_id, $type = 1, $record_id = 0, $user_id = 0)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => [],
        ];
        if (!$bargain_id || !$user_id) {
            //            $result['msg'] = '参数丢失';
            return error_code(10051);
        }
        $goodsModel         = new Goods();
        $logModel           = new BargainLog();
        $bargainRecordModel = new BargainRecord();
        $userModel          = new User();

        $info = $this->field('id,name,intro,desc,sales_num,goods_id,max_goods_nums,start_price,end_price,etime')->get($bargain_id);

        $goods = $goodsModel->getGoodsDetial($info['goods_id']);
        if (!$goods['status']) {
            //            $result['msg'] = '参数丢失';
            return error_code(10051);
        }
        $info['goods'] = $goods['data'];

        $aWhere = $fWhere = [];
        $aWhere['bargain_id'] = $bargain_id;
        $attendance_record    = $bargainRecordModel->getList('id,bargain_id,user_id,ctime,status,etime,stime', $aWhere, ['ctime' => 'desc'], 1, 50); //todo 参与活动记录要拆分开

        $info['attendance_record'] = $attendance_record['data'];
        $aWhere['id']              = $record_id;

        $info['sales_num'] = $bargainRecordModel->where([['bargain_id', '=', $bargain_id]])->whereNotNull('order_id')->count();
        //亲友团
        if ($type == self::TYPE_SELF) {
            $aWhere['user_id'] = $user_id;
            $record            = $bargainRecordModel->where($aWhere)->find();
            $record_id         = $record['id'];
        } else {
            $record = $bargainRecordModel->where($aWhere)->find();
        }
        $fWhere['bargain_id']   = $bargain_id;
        $fWhere['record_id']    = $record_id;
        $friends_record         = $logModel->getList('*', $fWhere, ['ctime' => 'asc'], 1, 50); //todo 亲友参与记录要拆分开
        $info['friends_record'] = $friends_record['data'];

        $info['lasttime'] = secondConversionArray($record['etime'] - time());

        //已经砍的价格
        $info['cut_off_price'] = bcsub($record['start_price'], $record['price'], 2); //砍掉多少钱
        $dvalue                = bcsub($record['start_price'], $record['end_price'], 2);

        if ($dvalue == 0) {
            $progress = 1;
        } else {
            $progress = bcdiv($info['cut_off_price'], $dvalue, 2);
        }
        $info['cut_off_progress'] = ($progress > 1 ? 1 : $progress) * 100; //砍价进度条
        $info['status_progress']  = $record['status'];
        $info['current_price']    = $record['price'];

        //活动数量
        if ($info['max_goods_nums'] == 0) {
            $info['max_goods_nums'] = $goods['data']['product']['stock'];
        }

        $bargain_user         = $userModel->getUserInfo($record['user_id']);
        $info['bargain_user'] = $bargain_user['data'];
        $result['status']     = true;
        $result['data']       = $info;
        return $result;
    }


    /**
     * 当前价格
     * @param $bargain_id
     * @param int $record_id
     * @return array
     */
    public function geBargainPrice($bargain_id, $user_id = 0)
    {
        $result             = [
            'status' => false,
            'msg'    => '',
            'data'   => [],
        ];
        $data               = [
            'cut_price'        => 0,
            'current_price'    => 0,
            'cut_off_progress' => 0,
            'status_progress'  => self::PROGRESS_STATUS_ING,
        ];
        $bargainRecordModel = new BargainRecord();
        $record             = $bargainRecordModel->where([['bargain_id', '=', $bargain_id], ['user_id', '=', $user_id], ['status', 'in', [$bargainRecordModel::STATUS_ING, $bargainRecordModel::STATUS_SUCCESS]]])->find();
        if (!$record) {
            //            $result['msg'] = '砍价记录不存在，请先参加活动';
            return error_code(17633);
        }

        $data['cut_price'] = bcsub($record['start_price'], $record['price'], 2);
        $dvalue            = bcsub($record['start_price'], $record['end_price'], 2);
        if ($data['cut_price'] == 0 || $dvalue == 0) {
            $progress = 1;
        } else {
            $progress = bcdiv($data['cut_price'], $dvalue, 2);
        }
        $data['cut_off_progress'] = ($progress > 1 ? 1 : $progress) * 100;
        $data['status_progress']  = $record['status'];
        $data['current_price']    = $record['price'];
        $result['data']           = $data;
        $result['status']         = true;
        return $result;
    }

    /**
     * 砍一刀
     * @param $id 活动id
     * @param int $type 自己砍还是别人砍价
     * @param int $user_id 当前用户id
     * @param int $record_id 发起人id
     * @return array
     */
    public function doBargain($id, $type = 1, $user_id = 0, $record_id = 0)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => [],
        ];

        $info = $this->get($id);
        if (!$info) {
            //            $result['msg'] = '砍价活动不存在';
            return error_code(17612);
        }
        $current_time = time();
        if ($current_time < $info['stime']) {
            //            $result['msg'] = '砍价活动暂未开始';
            return error_code(17601);
        }
        if ($current_time > $info['etime']) {
            //            $result['msg'] = '砍价活动已结束';
            return error_code(17602);
        }
        $logModel           = new BargainLog();
        $bargainRecordModel = new BargainRecord();
        //查看当前用户是否参加过
        $section_price = $last_goods_price = 0; //剩余砍价区间
        $where         = [];
        $where[]       = ['id', '=', $record_id];
        $record        = $bargainRecordModel->where($where)->find();
        if (!$record) {
            //            $result['msg'] = '砍价活动不存在';
            return error_code(17612);
        }

        if ($record['status'] == $bargainRecordModel::STATUS_SUCCESS) {
            //            $result['msg'] = '该砍价已成功，请先支付后再继续参与活动';
            return error_code(17616);
        }
        if ($current_time > $record['etime']) {
            //            $result['msg'] = '该用户砍价活动已结束';
            return error_code(17602);
        }
        if ($record['status'] != $bargainRecordModel::STATUS_ING) {
            //            $result['msg'] = '该用户砍价活动已结束';
            return error_code(17602);
        }
        $where   = [];
        $where[] = ['user_id', '=', $user_id];
        $where[] = ['record_id', '=', $record_id];
        $nums    = $logModel->where($where)->count();
        //如果有记录，则砍价开始价为上次砍过后的价
        if ($record) {
            $info['start_price'] = $record['price'];
        }
        $section_price = bcsub($info['start_price'], $info['end_price'], 2); //剩余砍价区间

        //已砍价次数
        $totalRecord = $logModel->where([['record_id', '=', $record_id]])->count();
        if ($totalRecord >= $info['total_times'] && $info['total_times']) {
            //            $result['msg'] = '此商品只能砍价' . $info['total_times'] . '次';
            return error_code(17634, false, $info['total_times']);
        }

        if ($record && $record['price'] <= $info['end_price']) {
            //            $result['msg'] = '此商品只能已砍到底价了';
            return error_code(17635);
        }
        //当前次数够了,直接成功吧
        if ($totalRecord + 1 == $info['total_times']) {
            $lastRecord = $logModel->where([['record_id', '=', $record_id]])->order('id', 'desc')->find();
            if (!$lastRecord) {
                $lastRecord['goods_price'] = $info['start_price'];
            }
            $bargain_price = abs($lastRecord['goods_price'] - $info['end_price']);
        } else {
            //砍一刀金额计算
            $bargain_price = self::k($section_price, $info['total_times'], 900, $totalRecord + 1, 50);
        }


        if ($nums >= 1) { //暂时限定一个人只能参加1次 todo 以后考虑接入任务
            //            $result['msg'] = '您已超过该活动最大参加次数，看看别的活动吧~';
            return error_code(17636);
        }
        $last_goods_price = $record['price']; //砍价前金额

        $last_goods_price = $last_goods_price - $bargain_price; //砍价后金额
        //计算砍价金额
        $newLog               = [];
        $newLog['record_id']  = $record_id;
        $newLog['user_id']    = $user_id;
        $newLog['bargain_id'] = $id;
        $newLog['type']       = $type;
        $newLog['ctime']      = $current_time;
        $newLog['ip']         = get_client_ip(0, true);
        if ($section_price < $info['bargain_min_price']) { //区间金额都小于最小砍价金额了，直接砍价成功吧
            $bargain_price         = $section_price;
            $newLog['goods_price'] = $info['end_price'];
        } elseif ($last_goods_price < $info['end_price']) {
            $last_goods_price      = $info['end_price'];
            $bargain_price         = abs($record['price'] - $last_goods_price);
            $newLog['goods_price'] = $info['end_price'];
        } else {
            $newLog['goods_price'] = $last_goods_price;
        }
        $newLog['bargain_price'] = $bargain_price;

        if ($logModel->save($newLog)) {

            if ($record['price'] - $bargain_price < $record['end_price']) { //避免出现最后金额低于最低金额情况
                $bargain_price = $record['end_price'] - ($record['price'] - $bargain_price);
            }

            $bargainRecordModel->where([['id', '=', $record_id]])->setDec('price', $bargain_price);
            //判断是否砍价成功
            if ($record['price'] - $bargain_price <= $record['end_price']) {
                $bargainRecordModel->updateRecord($record_id, ['status' => $bargainRecordModel::STATUS_SUCCESS]); //砍价成功
            }
        }
        //累计砍掉多少钱
        $result['status'] = true;
        $result['data']   = [
            'current_price' => $last_goods_price,
            'bargain_price' => $bargain_price,
        ];
        $result['msg']    = '砍价成功';
        return $result;
    }


    /**
     * 计算砍价金额
     * @param $money 金额
     * @param $nums 预计砍n刀
     * @param $n 离散值，0~1000,越小越平均
     * @param $k 第n刀
     * @param $rand 随机值0~100的整数
     * @return bool|float
     */
    static function k($money, $nums, $n, $k, $rand = 0)
    {
        $n = $nums - $n + 1;
        if ($k < 0 || $k > 1000) {
            return false;
        }
        $rand = $rand / 100;
        $bc   = $money / $nums;
        if ($money <= 0 || $nums <= 0 || $bc <= 0.01) {
            return false;
        }
        $bi = self::gs($money, $k) / ($money);

        $y  = self::gs($bc * ($nums - $n + 1), $k);
        $i2 = ($nums - $n);
        $y2 = self::gs($bc * $i2, $k);

        $re = ($y - $y2) / $bi;
        if ($re < 0.01) {
            return 0.01;
        } else {
            $re = round($re, 2);
        }
        //取随机值
        $re = $re * 100;
        $re = $re + mt_rand(0, $re * 2 * $rand) - $re * $rand;
        $re = $re / 100;
        return sprintf("%.2f", $re);
    }

    static function gs($x, $k = 1)
    {
        return pow($x, 1 + $k / 100);    //如果还想把离散值的差距搞大，这里的第二个参数往上加
    }


    /**
     * 在加入购物车的时候，判断是否有参加拼团的商品
     * @param $product_id 货品id
     * @param int $user_id 用户id
     * @param int $nums 加入购物车数量
     * @return array|mixed
     */
    public function addCart($product_id, $user_id = 0, $nums = 1)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];

        $productModel = new Products();
        $info         = $productModel->where(['id' => $product_id])->find();
        if (!$info) {
            return error_code(17603);
        }
        $where[] = ['status', 'eq', self::STATUS_ON];
        $where[] = ['goods_id', 'eq', $info['goods_id']];

        $binfo = $this
            ->where($where)
            ->order('sort asc')
            ->find();
        //检查是否下过单
        if (!$binfo) {
            return error_code(17612);
        }
        $bargainRecordModel = new BargainRecord();
        $record             = $bargainRecordModel->field('id,status')->where([['bargain_id', '=', $binfo['id']], ['user_id', '=', $user_id]])->order('ctime', 'desc')->find();

        if (!$record) {
            return error_code(17612);
        }
        if ($record['status'] == $bargainRecordModel::STATUS_HAVE_ORDER) {
            return error_code(17613);
        }
        if ($record['status'] == $bargainRecordModel::STATUS_END) {
            return error_code(17614);
        }
        if ($record['status'] == $bargainRecordModel::STATUS_CANCLE) {
            return error_code(17615);
        }
        if ($binfo['stime'] > time()) {
            return error_code(17601);
        }
        if ($binfo['etime'] < time()) {
            return error_code(17602);
        }
        $this->where([['id', '=', $binfo['id']]])->setInc('sales_num', 1); //销量加1
        $result['status'] = true;
        return $result;
    }

    /**
     * 获取砍价价格信息
     * @param $list
     * @param $user_id
     * @return array|mixed
     */
    public function bargainInfo(&$list, $user_id)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        foreach ($list as $k => $v) {
            $where   = [];
            $where[] = ['status', 'eq', self::STATUS_ON];
            $where[] = ['goods_id', 'eq', $v['products']['goods_id']];
            $pinfo   = $this
                ->where($where)
                ->field('id,stime,etime,goods_id')
                ->find();
            if (!$pinfo) {
                return error_code(17603);
            }
            if ($pinfo['stime'] > time()) {
                return error_code(17601);
            }
            if ($pinfo['etime'] < time()) {
                return error_code(17602);
            }
            $price = $this->geBargainPrice($pinfo['id'], $user_id);

            $list[$k]['products']['price']            = $price['data']['current_price'];
            $list[$k]['products']['promotion_amount'] = $price['data']['cut_price'];
            if ($list[$k]['products']['price'] < 0) {
                return error_code(10000);
            }
        }
        $result['status'] = true;
        return $result;
    }
}
