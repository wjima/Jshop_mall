<?php


namespace app\common\model;


use app\common\model\User as UserModel;

class UserLog extends Common
{

    const USER_LOGIN = 1;     //登录
    const USER_LOGOUT = 2;    //退出
    const USER_REG = 3;    //注册
    const USER_EDIT = 4;    //用户编辑信息

    const USER_TYPE = 1;//用户类型，会员
    const MANAGE_TYPE = 2;//用户类型，管理员

    //总后台的登陆记录
    public function getList($user_id = 0, $type = self::USER_TYPE, $limit = 10)
    {
        $where = [];
        if ($user_id) {
            $where[] = ['user_id', 'eq', $user_id];
        }
        $where[] = ['state', 'eq', 1];
        $where[] = ['type', 'eq', $type];
        $data    = $this->where($where)
            ->order('ctime DESC')
            ->paginate($limit);
        foreach ($data as $key => $val) {
            if ($val['type'] == self::USER_TYPE) {
                $userModel              = new UserModel();
                $userInfo               = $userModel->field('id,username,nickname,mobile')->where(['id' => $val['user_id']])->find();
                $data[$key]['username'] = isset($userInfo['mobile']) ? $userInfo['mobile'] : $userInfo['nickname'];
            } else {
                $manageModel            = new Manage();
                $manageInfo             = $manageModel->field('id,username,nickname,mobile')->where(['id' => $val['user_id']])->find();
                $data[$key]['username'] = (isset($manageInfo['mobile'])&&$manageInfo['mobile']) ? $manageInfo['mobile'] : $manageInfo['username'];
            }

            $data[$key]['state'] = config('params.user')['state'][$val['state']];
            $data[$key]['ctime'] = getTime($val['ctime']);
        }
        return $data;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['user_id']) && $post['user_id'] != ""){
            $where[] = ['user_id', 'eq', $post['user_id']];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'eq', $post['id']];
        }
        if(isset($post['type']) && $post['type']){
            $where[] = ['type','=', $post['type']];
        }
        if(!empty($post['date']))
        {
            $date_string = $post['date'];
            $date_array = explode(' 到 ', $date_string);
            $sdate = strtotime($date_array[0].' 00:00:00');
            $edate = strtotime($date_array[1].' 23:59:59');
            $where[] = array('ctime', ['>=', $sdate], ['<', $edate], 'and');
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "ctime desc";
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
        foreach($list as $k => $v) {
            if ($v['type'] == self::USER_TYPE) {
                $userModel              = new UserModel();
                $userInfo               = $userModel->field('id,username,nickname,mobile')->where(['id' => $v['user_id']])->find();
                $list[$k]['username'] = isset($userInfo['mobile']) ? $userInfo['mobile'] : $userInfo['nickname'];
            } else {
                $manageModel            = new Manage();
                $manageInfo             = $manageModel->field('id,username,nickname,mobile')->where(['id' => $v['user_id']])->find();
                $list[$k]['username'] = (isset($manageInfo['mobile'])&&$manageInfo['mobile']) ? $manageInfo['mobile'] : $manageInfo['username'];
            }
            $list[$k]['state'] = config('params.user')['state'][$v['state']];
            $list[$k]['ctime'] = getTime($v['ctime']);
        }
        return $list;
    }


    /**
     *  添加日志
     * User:tianyu
     * @param $user_id
     * @param string $state
     */
    public function setLog($user_id, $state, $data = [], $type = self::USER_TYPE)
    {

        $data = [
            'user_id' => $user_id,
            'state'   => $state,
            'ctime'   => time(),
            'params'  => json_encode($data),
            'ip'      => get_client_ip(0, true),
            'type'    => $type
        ];
        $this->allowField(true)->save($data);
    }

    /**
     * 按天统计商户下面的数据
     */
    public function statistics($day,$state)
    {
        $where['state'] = $state;
        $field = 'state,DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d") as day, count(*) as nums';

        $res = $this->field($field)->where($where)->where("TIMESTAMPDIFF(DAY,from_unixtime(ctime),now()) <7")->group('DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d")')->select();

        $data = get_lately_days($day, $res);
        return ['day' => $data['day'], 'data' => $data['data']];


    }

    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id'   => 'username',
                'desc' => '登录用户',
            ],
            [
                'id'   => 'ctime',
                'desc' => '登录时间',
            ],
            [
                'id'   => 'ip',
                'desc' => '登录ip',
            ]
        ];
    }


    /**
     * 获取csv数据
     * @param $post
     * @return array
     */
    public function getCsvData($post)
    {
        $result   = error_code(10083);
        $header   = $this->csvHeader();
        $userData = $this->tableDatas($post, false);

        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body     = [];
            $i        = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                foreach ($header as $hk => $hv) {
                    if (isset($val[$hv['id']]) && $val[$hv['id']] && isset($hv['modify'])) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) && !empty($val[$hv['id']])) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }
            }
            $result['status'] = true;
            $result['msg']    = '导出成功';
            $result['data']   = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableDatas($post, $isPage = true)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWheres($post);
        $list       = [];
        if ($isPage) {
            $list        = $this->with('grade')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
            $data        = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $re['count'] = $list->total();
        } else {
            $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->select();
            if (!$list->isEmpty()) {
                $data = $this->tableFormat($list->toArray());
            }
            $re['count'] = count($list);
        }
        $userModel = new User();
        foreach ($data as &$v)
        {
            $userInfo = $userModel->field('id,username,nickname,mobile')->where(['id' => $v['user_id']])->select();
            $v['username'] = $userInfo[0]['mobile'];
        }
        $re['code'] = 0;
        $re['msg']  = '';

        $re['data'] = $data;

        return $re;
    }


    protected function tableWheres($post)
    {
        $where = [];
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'in', $post['id']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "id desc";
        return $result;
    }



//    /**
//     *  ip获取定位城市
//     * User:tianyu
//     * @param string $ip
//     * @return bool|string
//     */
//    private function getCity( $ip = '' ){
//
///*        if( empty($ip) ) $ip = get_client_ip();
//
//        $url='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
//
//        $result = file_get_contents($url);
//
//        $result = json_decode($result,true);
//
//        if($result['code']!==0 || !is_array($result['data'])) return false;
//
//        return $result['data']['region'].$result['data']['city'];*/
//        return '';
//    }
}