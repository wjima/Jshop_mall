<?php

namespace addons\WeRun\model;

use app\common\model\Addons;
use app\common\model\Common;
use app\common\model\User;
use app\common\model\UserPointLog;
use app\common\model\UserWx;
use org\Wx;
use think\Db;

class Werun extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const STATUS_NO = 1;    // 待兑换
    const STATUS_YES = 2;   // 已兑换
    const STATUS_INVALID = 3;   // 已失效
    const STATUS_NULL = 4;   // 不满足条件


    const POINT_TYPE = 10;  // 积分类型。

    /**
     * 后台获取计步列表
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param [type] $data
     * @return void
     */
    public function getList($data)
    {
        $page = isset($data['page']) ? $data['page'] : 1;
        $limit = isset($data['limit']) ? $data['limit'] : config('jshop.page_limit');
        $where = $this->_where($data);
        $query = $this->alias('r')
            ->leftJoin('user u', 'r.user_id=u.id');
        if ($where) $query->where($where);

        $datas = $query->field(['r.*', 'u.username'])->order('r.id', 'desc')->page($page, $limit)->select();
        $count = $query->count();

        foreach ($datas as &$data) {
            $data['ctime'] = getTime($data['ctime']);
            $data['utime'] = getTime($data['utime']);
        }

        return [
            'code' => 0,
            'data' => $datas,
            'count' => $count,
            'msg' => '获取成功'
        ];
    }

    private function _where($data)
    {
        $where = [];
        if ($data) {
            if (isset($data['date']) && $data['date'] != '') {
                $theDate = explode(' 到 ', $data['date']);
                if (count($theDate) == 2) {
                    $where[] = ['r.date', '>', strtotime($theDate[0] . '  00:00:00')];
                    $where[] = ['r.date', '<=', strtotime($theDate[1] . '  23:59:59')];
                }
            }
            if (isset($data['username']) && $data['username'] != "") {
                $where[] = ['u.username|nickname|mobile', 'like', '%' . $data['username'] . '%'];
            }
            if (isset($data['status']) && $data['status']) {
                $where[] = ['r.status', 'eq', $data['status']];
            }
        }
        return $where;
    }


    private function get_address($address)
    {
        preg_match('/(.*?(省|自治区|北京|天津|上海|重庆))/', $address, $matches);
        if (count($matches) > 1) {
            $province = $matches[count($matches) - 2];
            $address = preg_replace('/(.*?(省|自治区|北京|天津|上海|重庆))/', '', $address, 1);
        }
        preg_match('/(.*?(市|自治州|地区|区划|县))/', $address, $matches);
        if (count($matches) > 1) {
            $city = $matches[count($matches) - 2];
            $address = str_replace($city, '', $address);
        }
        preg_match('/(.*?(区|县|镇|乡|街道))/', $address, $matches);
        if (count($matches) > 1) {
            $area = $matches[count($matches) - 2];
            $address = str_replace($area, '', $address);
        }

        return [
            'province' => isset($province) ? $province : '',
            'city' => isset($city) ? $city : '',
            'area' => isset($area) ? $area : '',
            "address" => $address
        ];
    }
    /**
     * 获取天气
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param string $city
     * @return array
     */
    public function getWeather($city = '')
    {
        if ($city == '') {
            // 根据当前的IP地址获取所在地址
            // $ip = get_client_ip(0,true);
            $city = '郑州';
        } else {
            $add = $this->get_address($city);
            $city = $add['area'] ?? $add['city'] ?? $add['province'];
        }
        //天气接口URL
        $urls = "http://wthrcdn.etouch.cn/weather_mini?city=" . $city;
        //得到URL中的内容
        $contents = file_get_contents("compress.zlib://" . $urls);
        //转化为json
        $datas = json_decode($contents, true);
        if ($datas['status'] && $datas['status'] == 1000) {
            return [
                'city' => $datas['data']['city'],
                'today' => $datas['data']['forecast'][0],
                'ganmao' => $datas['data']['ganmao'],
                'wendu' => $datas['data']['wendu']
            ];
        } else {
            return [];
        }
    }

    /**
     * 获取排行榜数据
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-09
     * @return array
     */
    public function rank()
    {
        // 获取今日排行榜
        $today_rank = $this->with('user')->field(['user_id', 'step'])->where('date_str', 'eq', date('Y-m-d', time()))->order('step', 'desc')->limit(10)->select();
        foreach ($today_rank as &$val) {
            $val['image'] = _sImage($val['avatar']);
        }
        // 获取累计排行榜
        $total_rank = $this->with('user')->field(['user_id', 'sum(step) step'])->where('date_str', 'neq', date('Y-m-d', time()))->group('user_id')->order('step', 'desc')->limit(10)->select();
        foreach ($total_rank as &$val) {
            $val['image'] = _sImage($val['avatar']);
        }

        return [
            'today_rank' => $today_rank,
            'total_rank' => $total_rank,
        ];
    }

    /**
     * 获取天气、今天的步数及排行榜信息
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param int $userId
     * @param string $city
     * @return array
     */
    public function getInfo($userId, $city = '')
    {
        // 先获取天气
        $weather = $this->getWeather($city);

        // 获取当前用户今天的步数
        $date = date('Y-m-d 00:00:00', time());
        $date = strtotime($date);
        $log = $this->where('date', '>=', $date)->where('user_id', $userId)->find();
        $today = date('Y-m-d', time());
        $count = $this->where('date_str', 'eq', $today)->group('user_id')->count();  // 今日参与总会员数
        if (!$log) {
            $log = 'update';
        } else {
            // 今日运动比此人步数多的有
            $count2 = $this->where('date_str', 'eq', $today)->where('step', '>', $log['step'])->where('user_id', 'neq', $userId)->group('user_id')->count();
            $persent = round(($count - $count2) / $count * 100) . '%';
            $log['ctime'] = getTime($log['ctime']); // 
            $log['utime'] = getTime($log['utime']); // 上次更新时间
            $log['persent'] = $persent;
        }
        $rank = $this->rank();
        // 获取从那一天开始记录运动步数的
        $first = $this->where('user_id', $userId)->order('date', 'asc')->find();
        if (!$first) {
            $first = [
                'date_str' => date('Y-m-d')
            ];
        }
        return [
            'status' => true,
            'msg' => '',
            'data' => [
                'weather' => $weather,
                'log' => $log,
                'total_count' => $count,
                'rank' => $rank,
                'first_time' => $first['date_str']
            ]
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->bind(['nickname', 'avatar']);
    }
    /**
     * 同步步数
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param integer $userId
     * @param string $encryptedData
     * @param string $iv
     * @return array
     */
    public function updateLog(int $userId, string $encryptedData, string $iv)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        $userWxModel = new UserWx();
        $info = $userWxModel->where([
            ['user_id', 'eq', $userId],
            ['type', 'eq', $userWxModel::TYPE_MINIPROGRAM]    // 取微信小程序的
        ])->find();
        if (!$info) {
            $result['msg'] = '请使用微信小程序内部使用此功能';
            return $result;
        }
        $wx = new Wx();
        $res = $wx->decrypt($encryptedData, $iv, $info['session_key']);
        if (!$res['status']) return $res;
        if (!isset($res['data']['stepInfoList']) || !$res['data']['stepInfoList']) {
            $result['msg'] = '没有需要同步的步数记录';
            return $result;
        }
        $data = $res['data']['stepInfoList'];
        // 同步最近31天的记录
        // 获取最近31天的记录   (今天以及前30天的运动数据，故未31天)
        $tableData = $this->where('user_id', $userId)->order('date', 'desc')->limit(31)->select();
        $logs = [];
        foreach ($tableData as $val) {
            $logs[$val['date_str']] = $val;
        }
        $first = $this->where('user_id', $userId)->order('date', 'asc')->limit(1)->find();
        $addData = [];
        $updateData = [];
        $today_steps = 0;
        $addonModel = new Addons();
        $setting    = $addonModel->getSetting('Werun');
        $min_steps = $setting['min_steps'];
        foreach ($data as $val) {
            $val['date'] = date('Y-m-d', $val['timestamp']);
            // 说明是第一次同步运动就同步当天即可
            if (!$first) {
                if ($val['date'] == date('Y-m-d')) {
                    $item = [
                        'user_id' => $userId,
                        'date_str' => $val['date'],
                        'date' => $val['timestamp'],
                        'step' => $val['step'],
                        'status' => self::STATUS_NO
                    ];
                    $today_steps = $val['step'];
                    $addData[] = $item;
                    break;
                } else {
                    continue;
                }
            }
            if (date('Y-m-d', $first['ctime']) <= $val['date'] && $val['date'] <= date('Y-m-d')) {
                $item = [
                    'user_id' => $userId,
                    'date_str' => $val['date'],
                    'date' => $val['timestamp'],
                    'step' => $val['step'],
                    'status' => self::STATUS_NO
                ];
                // 有此日的记录
                if (isset($logs[$item['date_str']])) {
                    // 获取今日的最新数据，会返回到前台
                    if ($item['date_str'] == date('Y-m-d')) {
                        $today_steps = $val['step'];
                        $item['id'] = $logs[$item['date_str']]['id'];
                        $updateData[] = $item;
                        continue;
                    }
                    // 如果此步数已经兑换或已过期，则无需更新
                    if ($logs[$item['date_str']]['status'] == self::STATUS_YES || $logs[$item['date_str']]['status'] == self::STATUS_INVALID) continue;
                    // 如果当前步数为待兑换 且步数有更新 那就更新吧
                    if ($logs[$item['date_str']]['status'] == self::STATUS_NO) {
                        $item['id'] = $logs[$item['date_str']]['id'];
                        // 如果更新后的步数 还比要求的小，那就说明这一天的运动步数确实是不满足条件了。
                        if ($logs[$item['date_str']]['step'] != $val['step']) {
                            if ($val['step'] < $min_steps) {
                                $item['status'] = self::STATUS_NULL;
                            }
                        } else {
                            if ($val['step'] < $min_steps) {
                                $item['status'] = self::STATUS_NULL;
                            }
                        }
                        $updateData[] = $item;
                    }
                } else {
                    // 无此日的记录  则新增
                    $addData[] = $item;
                }
            }
        }
        // 要是既不需要更新 也不需要新增，就直接返回吧
        if (!$addData && !$updateData) {
            $result['status'] = true;
            $result['data'] = $today_steps;
            return $result;
        }
        if ($addData) {
            $model = new Werun();
            $flag1 = $model->saveAll($addData);
            if (!$flag1) {
                $result['msg'] = '同步失败,请重试';
                return $result;
            }
        }
        if ($updateData) {
            $model = new Werun();
            $flag2 = $model->saveAll($updateData);
            if (!$flag2) {
                $result['msg'] = '同步失败,请重试';
                return $result;
            }
        }

        $result['status'] = true;
        $result['data'] = $today_steps;
        return $result;
    }

    /**
     * 获取步数及公里数
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-03
     * @param int $userId
     * @param integer $type  
     * @param string $year
     * @param string $month
     * @param string $day
     * @return array
     */
    public function statistic($userId, $type = 1, $year = '2021', $month = '', $day = '')
    {
        $where = [];
        switch ($type) {
            case 1:
                // 某年
                $where = [
                    ['date', '>=', strtotime($year . '-01-01 00:00:00')],
                    ['date', '<=', strtotime($year . '-12-31 23:59:59')],
                    ['user_id', '=', $userId]
                ];
                break;
            case 2:
                // 某月
                $date = $year . '-' . $month;
                // $month_start = strtotime($date); //指定月份月初时间戳  
                // $month_end = mktime(23, 59, 59, date('m', strtotime($date)) + 1, 00); //指定月份月末时间戳  
                $month_start = $date . '-01';
                if ($month == 2) {
                    $month_end = $date . '-28';
                    if ($year % 4 == 0) {
                        $month_end = $date . '-29';
                    }
                } else if (in_array($month, [1, 3, 5, 7, 8, 10, 12])) {
                    $month_end = $date . '-31';
                } else {
                    $month_end = $date . '-30';
                }

                $where = [
                    ['date', '>=', strtotime($month_start . ' 00:00:00')],
                    ['date', '<=', strtotime($month_end . ' 23:59:59')],
                    ['user_id', '=', $userId]
                ];
                break;
            case 3:
                // 某日
                $where = [
                    ['date_str', '=', date('Y-m-d', strtotime($year . '-' . $month . '-' . $day . ' 00:00:00'))],
                    ['user_id', '=', $userId]
                ];
                break;
            default:
                return \error_code(10000);
                break;
        }

        $sum = $this->where($where)->sum('step');
        return [
            'status' => true,
            'msg' => '',
            'data' => [
                'steps' => $this->convertStep($sum),
                'course' => $this->stepToCourse($sum)
            ]
        ];
    }

    /**
     * 将步数转化为公里数
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param int $step
     * @return float
     */
    public function stepToCourse($step)
    {
        $course = $step * 60;   // 1步约等于 60cm
        // 转化为公里数
        $course = bcdiv($course, 100 * 1000, 2);
        return $course;
    }

    /**
     * 将去年所有的未兑换的步数 作废
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-09
     * @return array
     */
    public function clearStepsByYears()
    {
        $year = date('Y');
        $where = [
            ['date', '<=', strtotime($year . '-01-01 00:00:00')],
            ['status', 'eq', self::STATUS_NO]
        ];
        $res = $this->save(['status' => self::STATUS_INVALID], $where);
        if ($res) {
            return [
                'status' => true,
                'msg' => '步数重置成功'
            ];
        }
        return [
            'status' => false,
            'msg' => '步数重置失败，或无待重置的步数'
        ];
    }


    /**
     * 将1个月前的不满足条件的步数作废。
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-09
     * @return array
     */
    public function clearStepsByMonth()
    {
        $addonModel = new Addons();
        $setting    = $addonModel->getSetting('Werun');
        $time = strtotime('-31 day');
        $where = [
            ['date', '<=', $time],
            ['status', 'eq', self::STATUS_NO],
            ['step', '<', $setting['min_steps']],
        ];
        $count = $this->where($where)->count();
        if ($count == 0) {
            return [
                'status' => true,
                'msg' => '无待重置的步数'
            ];
        }
        $res = $this->save(['status' => self::STATUS_INVALID], $where);
        if (!$res) {
            return [
                'status' => false,
                'msg' => '步数重置失败，或无待重置的步数'
            ];
        }

        return [
            'status' => true,
            'msg' => '步数重置成功'
        ];
    }

    public function exchangePoint($userId)
    {

        $time = strtotime('today'); // 获取今天0点的时间戳
        // 获取步数与积分转化的配置
        $addonModel = new Addons();
        $setting    = $addonModel->getSetting('Werun');
        $datas = $this->where([
            ['user_id', 'eq', $userId],
            ['date', '<', $time],
            ['status', 'eq', self::STATUS_NO],
            ['step', '>=', $setting['min_steps']],
        ])->order('date', 'asc')->select();
        if ($datas->isEmpty()) return [
            'status' => false,
            'data' => '',
            'msg' => '无待兑换积分的步数'
        ];
        $updateData = [];
        $steps = 0;
        $points = 0;
        foreach ($datas as $data) {
            // 计算兑换的积分 
            $point = $this->convertPoint($data['step'], $setting['min_steps'], $setting['min_jifen'], $setting['more_steps'], $setting['more_jifen']);
            // 总积分
            $points += $point;
            // 总步数 
            $steps += $data['step'];
            $updateData[] = [
                'id' => $data['id'],
                'status' => self::STATUS_YES,
                'point' => $point,
                'step' => $data['step']
            ];
        }
        $date_str = $datas[0]['date_str'] . ' 至 ' . $data['date_str'];
        $log = [
            'user_id' => $userId,
            'steps' => $steps,
            'points' => $points,
            'date_str' => $date_str
        ];
        try {
            Db::startTrans();
            // 更新每日步数兑换的状态及兑换的积分数量
            $this->saveAll($updateData);
            // 记录兑换日志
            $logModel = new WerunLog();
            $logModel->save($log);
            // 用户增加积分
            $userPointModel = new UserPointLog();
            $userPointModel->setPoint($userId, $points, self::POINT_TYPE, '微信运动（' . $date_str . '）兑换积分');
            Db::commit();
            return [
                'status' => true,
                'data' => [
                    'steps' => $steps,
                    'points' => $points,
                ],
                'msg' => '兑换成功',
            ];
        } catch (\Exception $e) {
            Db::rollback();
            dump($e);
            return [
                'status' => false,
                'data' => '',
                'msg' => $e->getMessage(),
            ];
        }
    }
    /**
     * 积分转化
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param integer $steps
     * @param integer $min_steps
     * @param integer $min_jifen
     * @param integer $more_steps
     * @param integer $more_jifen
     * @return int
     */
    private function convertPoint(int $steps, int $min_steps, int $min_jifen, int $more_steps, int $more_jifen)
    {
        if ($min_steps <= 0) return 0;
        if ($steps < $min_steps) return 0;

        if ($more_steps <= 0) return $min_jifen;
        $remainder = intval(($steps - $min_steps) / $more_steps);
        return $min_jifen + $remainder * $more_jifen;
    }

    private function convertStep($steps)
    {
        $length = strlen($steps);  //数字长度
        if ($length > 8) { //亿单位
            $str = substr_replace(strstr($steps, substr($steps, -7), ' '), '.', -1, 0) . "亿";
        } elseif ($length > 4) { //万单位
            //截取前俩为
            $str = substr_replace(strstr($steps, substr($steps, -3), ' '), '.', -1, 0) . "万";
        } else {
            return $steps;
        }
        return $str;
    }

    /**
     * 获取待兑换的积分
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-09
     * @param int $userId
     * @return array
     */
    public function getCollect($userId)
    {
        $time = strtotime('today'); // 获取今天0点的时间戳
        // 获取步数与积分转化的配置
        $addonModel = new Addons();
        $setting    = $addonModel->getSetting('Werun');
        $datas = $this->where([
            ['user_id', 'eq', $userId],
            ['date', '<', $time],
            ['status', 'eq', self::STATUS_NO],
            ['step', '>=', $setting['min_steps']],
        ])->order('date', 'asc')->select();
        $res = [
            'steps' => 0,
            'point' => 0,
            'setting' => [
                'min_steps' => $setting['min_steps'],
                'min_jifen' => $setting['min_jifen'],
                'more_steps' => $setting['more_steps'],
                'more_jifen' => $setting['more_jifen'],
            ]
        ];
        if (!$datas->isEmpty()) {
            $steps = 0;
            $points = 0;
            foreach ($datas as $data) {
                // 计算兑换的积分 
                $point = $this->convertPoint($data['step'], $setting['min_steps'], $setting['min_jifen'], $setting['more_steps'], $setting['more_jifen']);
                // 总积分
                $points += $point;
                // 总步数 
                $steps += $data['step'];
            }
            $res['steps'] = $steps;
            $res['points'] = $points;
        }
        return [
            'status' => true,
            'msg' => '',
            'data' => $res
        ];
    }
}
