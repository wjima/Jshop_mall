<?php
namespace app\common\model;

use think\Validate;

class Videos extends Common
{

    //验证规则
    protected $rule = [
        'name'        => 'require|max:50',
        'video_id'    => 'require',
        'video_cover' => 'require'
    ];

    protected $msg = [
        'name.require'        => '视频标题必须填写',
        'name.max'            => '标题名称最多不能超过50个字符',
        'video_id.require'    => '请上传视频',
        'video_cover.require' => '请上传视频封面',
    ];

    /**
     * @param $post
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        foreach ($list as &$v) {
            $v['video_cover_id'] = $v['video_cover'];
            $v['video_cover'] = _sImage($v['video_cover']);
            $v['ctime']       = getTime($v['ctime']);
            $v['video_url']   = _sFile($v['video_id']);
        }
        $data        = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        return $re;
    }


    /**
     * where 搜索条件
     * author: tianyu
     *
     * @param $post
     *
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%' . $post['name'] . '%'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id desc'];
        return $result;
    }


    /*
     * 视频添加
     * */
    public function addData($data)
    {
        $validate = new Validate($this->rule, $this->msg);
        $result   = ['status' => true, 'msg' => '保存成功', 'data' => ''];
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            $data['ctime'] = time();
            if (!$this->allowField(true)->save($data)) {
                return error_code(10004);
            }
        }
        return $result;
    }


    /*
     * 视频编辑
     * */
    public function videoEdit($data)
    {
        $validate = new Validate($this->rule, $this->msg);
        $result   = ['status' => true, 'msg' => '保存成功', 'data' => ''];
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            $data['ctime'] = time();
            $data['utime'] = time();
            unset($data['__Jshop_Token__'], $data['file']);
            $where = [
                'id' => $data['id']
            ];
            if (!$this->where($where)->update($data)) {
                return error_code(10004);
            }
        }
        return $result;
    }


}