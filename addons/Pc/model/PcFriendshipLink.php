<?php

namespace addons\Pc\model;

use think\Model;
use think\Validate;

class PcFriendshipLink extends Model
{

    protected $rule = [
        'name' => 'require|max:20',
        'link' => 'require|url',
        'sort' => 'require|number|between:1,100',


    ];
    protected $msg = [
        'name.require' => '网站名称必须',
        'name.max'     => '网站名最多20个字符',
        'link.require' => 'Url必须',
        'link.url'     => 'Url格式错误',
        'sort.require' => '排序必须',
        'sort.number'  => '排序格式错误',
        'sort.between' => '排序须在1-100之间',

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
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;

        return $re;
    }


    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', "%" . $post['name'] . "%"];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort ASC'];
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {
        return $list;
    }

    //添加
    public function add($data)
    {
        $return   = [
            'status' => false,
            'msg'    => '添加失败',
            'data'   => []
        ];
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }
        $rel = $this->allowField(true)->save($data);
        if ($rel) {
            $return['status'] = true;
            $return['msg']    = '添加成功';
        }
        return $return;
    }

    //编辑
    public function edit($data)
    {
        $return   = [
            'status' => false,
            'msg'    => '编辑失败',
            'data'   => []
        ];
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }
        $rel = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if ($rel || $rel === 0) {
            $return['status'] = true;
            $return['msg']    = '编辑成功';
        }
        return $return;
    }

    //信息
    public function getInfo($id)
    {
        return $this->where('id', 'eq', $id)->find();
    }

    //api获取友链
    public function getLink($page, $limit, $isPage = false)
    {
        if ($isPage) {
            $rel = $this->order('sort', 'asc')->page($page, $limit)->select();
        } else {
            $rel = $this->order('sort', 'asc')->select();
        }
        return $rel;
    }
}
