<?php


namespace app\common\model;


class UserLog extends Common
{

    const USER_LOGIN = 1;     //登录
    const USER_LOGOUT = 2;    //退出
    const USER_REG = 3;    //注册
    const USER_EDIT = 4;    //用户编辑信息


    //总后台的登陆记录
    public function getList( $user_id, $limit = 10 )
    {
        $where = [];
        if($user_id){
            $where[] = ['user_id','eq',$user_id];
        }
        $data = $this->where($where)
            ->order('ctime DESC')
            ->paginate($limit);
        foreach( $data as $key => $val )
        {
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
    public function setLog( $user_id,$state,$data = [] )
    {

        $data = [
            'user_id' => $user_id,
            'state' => $state,
            'ctime' => time(),
            'params' => json_encode($data),
            'ip' => get_client_ip()
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