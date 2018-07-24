<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/3/13 0013
 * Time: 19:00
 */
namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class BackstageNotice extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


    use SoftDelete;

    protected $deleteTime = 'isdel';


    //验证规则
    protected $rule     =   [
        'title'         =>  'require|max:200',
        'content'       =>  'require',
        'is_pub'        =>  'number|require',
        'sort'          =>  'number',

    ];

    protected $msg      =   [
        'title.require'     =>  '请填写公告标题',
        'title.max'         =>  '公告标题名称最多不能超过200个字符',
        'content.require'   =>  '公告内容必须填写',
        'is_pub.require'    =>  '请选择发布状态',
        'sort.number'       =>  '排序有非法字符',
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
        $list = $this->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        foreach($list as $key => $val)
        {
            $list[$key]['is_pub'] = config('params.article')['is_pub'][$val['is_pub']];
        }
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }


    /**
     * @param string $id
     *
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($id = '')
    {
        return $this->where('id',$id)->find();
    }


    /**
     *  总后台添加公告
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = ['status' => true, 'msg' => '保存成功', 'data' => ''];
        $validate = new Validate($this->rule,$this->msg);
        if(!$validate->check($data))
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
     *  总后台公告编辑
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $result = ['status' => true, 'msg' => '保存成功', 'data' => ''];
        $validate = new Validate($this->rule,$this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data,['id'=>$data['id']])) {
                $result['status'] = false;
                $result['msg'] = '保存失败';
            }
        }
        return $result;
    }


    /**
     * @param string $id
     *
     * @return array
     */
    public function del($id = '')
    {
        $result = ['status' => true,'msg' => '删除成功','data'=>''];
        if (!$this->destroy($id)) {
            $result['status'] = false;
            $result['msg'] = '删除失败';
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
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
            $list[$k]['utime'] = date('Y-m-d H:i:s',$v['utime']);
        }
        return $list;
    }


    /**
     * @param $post
     *
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['title']) && $post['title'] != ""){
            $where[] = ['title', 'like', '%'.$post['title'].'%'];
        }
        if(isset($post['ctime']) && $post['ctime'] != ""){
            $date_array = explode('~',$post['ctime']);
            $sctime = strtotime($date_array[0].'00:00:00',time());
            $ectime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['ctime', ['EGT',$sctime],['ELT',$ectime],'and'];
        }
        if(isset($post['utime']) && $post['utime'] != ""){
            $date_array = explode('~',$post['utime']);
            $sutime = strtotime($date_array[0].'00:00:00',time());
            $eutime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['utime', ['EGT',$sutime],['ELT',$eutime],'and'];
        }
        if(isset($post['is_pub']) && $post['is_pub'] != ""){
            $where[] = ['is_pub', 'eq', $post['is_pub']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort ASC'];
        return $result;
    }


    /**
     * @param $post
     *
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function showData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        return  $this->where('is_pub',1)->order('utime DESC')->paginate($limit);
    }
}