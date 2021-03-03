<?php

namespace addons\WeRun\model;

use app\common\model\Common;
use app\common\model\UserWx;
use org\Wx;

class WerunLog extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

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
        }
        return $where;
    }


    public function getWeather($city = '')
    {
        if ($city == '') {
            // 根据当前的IP地址获取所在地址
            // $ip = get_client_ip(0,true);
            $city = '郑州';
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

    public function getInfo($userId, $city = '')
    {
        // 先获取天气
        $weather = $this->getWeather($city);

        // 获取当前用户今天的步数
        $date = date('y-m-d 00:00:00', time());
        $date = strtotime($date);
        $log = $this->where('date', '>=', $date)->where('user_id', $userId)->find();
        $count = $this->group('user_id')->count();  // 总会员数
        // 比此人步数多的有
        $count2 = $this->where('date', '>=', $date)->where('step', '>', $log['step'])->count();
        $persent = round(($count - $count2) / $count * 100) . '%';
        if (!$log) return \error_code(10000);
        $log['ctime'] = getTime($log['ctime']); // 
        $log['utime'] = getTime($log['utime']); // 上次更新时间
        return [
            'status' => true,
            'msg' => '',
            'data' => [
                'weather' => $weather,
                'log' => $log,
                'total_count' => $count,
                'persent' => $persent
            ]
        ];
    }

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
        // 测试数据
        // $data= [];
        // for ($i=1; $i <= 30; $i++) { 
        //     $data[] = [
        //         'timestamp'=>strtotime('2021-03-'.$i),
        //         'step'=>rand(2000,9999)
        //     ];
        // }
        // 同步最近30天的记录
        // 获取最近30天的记录
        $tableData = $this->order('date', 'desc')->limit(30)->select();
        $logs = [];
        foreach ($tableData as $val) {
            $logs[$val['date_str']] = $val['id'];
        }
        $saveData = [];
        foreach ($data as $val) {
            $item = [
                'user_id' => $userId,
                'date_str' => date('Y-m-d', $val['timestamp']),
                'date' => $val['timestamp'],
                'step' => $val['step']
            ];
            if ($logs[$item['date_str']]) $item['id'] = $logs[$item['date_str']];
            $saveData[] = $item;
        }
        if (!$this->saveAll($saveData)) {
            $result['msg'] = '同步数据失败，请重试';
            return $result;
        }
        $result['status'] = true;
        return $result;
    }

    /**
     * 获取里程数及公里数
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-03
     * @param int $userId
     * @param integer $type  
     * @param string $year
     * @param string $month
     * @param string $day
     * @return 
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
                $month_start = strtotime($date); //指定月份月初时间戳  
                $month_end = mktime(23, 59, 59, date('m', strtotime($date)) + 1, 00); //指定月份月末时间戳  
                $where = [
                    ['date', '>=', $month_start],
                    ['date', '<=', $month_end],
                    ['user_id', '=', $userId]
                ];
                break;
            case 3:
                // 某日
                $where = [
                    ['date_str', '=', strtotime($year . '-' . $month . '-' . $day . ' 00:00:00')],
                    ['user_id', '=', $userId]
                ];
                break;
            default:
                return \error_code(10000);
                break;
        }

        $sum = $this->where($where)->sum('step');
        return [
            'status'=>true,
            'msg'=>'',
            'data' => [
                'steps' => $sum,
                'course' => $this->stepToCourse($sum)
            ]
        ];
    }

    public function stepToCourse($step)
    {
        $course = $step * 60;   // 1步约等于 60cm
        // 转化为公里数
        $course = bcdiv($course, 100 * 1000, 2);
        return $course;
    }
}
