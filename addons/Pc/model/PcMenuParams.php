<?php

namespace addons\Pc\model;

use app\common\model\Article;
use app\common\model\ArticleType;
use think\Db;
use think\Model;
use think\Validate;

class PcMenuParams extends Model
{

    protected $rule = [
        'menu_id' => 'require',
        'val'     => 'require',

    ];
    protected $msg = [
        'menu_id.require' => '所属菜单必须',
        'val.require'     => '数据必须',

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
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }
        if (isset($post['category']) && $post['category'] != "") {
            $where[] = ['category', 'eq', $post['category']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['utime DESC'];
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {
        $typeModel = new PcMenu();
        foreach ($list as &$val) {
            $val['name']          = $typeModel->getMenuName($val['type']);
            $val['category_name'] = $this->data['category'][$val['category']];
            $val['utime']         = getTime($val['utime']);
            if ($val['category'] == 3 || $val['category'] == 2) {
                $model      = new Article();
                $val['val'] = $model->where('id', 'eq', $val['val'])->value('title');
            }
            if ($val['category'] == 4) {

                $val['val'] = get_goods_info($val['val']);
            }
        }
        return $list;
    }

    //添加
    public function addMenu($data, $isAll = false)
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
        if ($isAll) {
            $new_data = [];
            foreach ($data['val'] as $k => $v) {
                $new_data [$k]       = $data;
                $new_data[$k]['val'] = $v;
            }
            $rel = $this->insertAll($new_data);
            if ($rel) {
                $return['status'] = true;
                $return['msg']    = '添加成功';
            }
        } else {
            $rel = $this->allowField(true)->save($data);
            if ($rel) {
                $return['status'] = true;
                $return['msg']    = '添加成功';
            }
        }

        return $return;
    }

    //编辑
    public function editMenu($data, $type)
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

        $menuModel = new PcMenu();
        if ($type == $menuModel::TYPE_GOODS) {
            $new_data = [];
            foreach ($data['val'] as $k => $v) {
                if (!$this->where([['menu_id', 'eq', $data['menu_id']], ['val', 'eq', $v]])->find()) {
                    $new_data [$k] = [
                        'menu_id' => $data['menu_id'],
                        'utime'   => time(),
                        'val'     => $v
                    ];
                }
            }

            // 启动事务
            Db::startTrans();
            try {

                $del = $this->where([['menu_id', 'eq', $data['menu_id']], ['val', 'not in', $data['val']]])->delete();

                if (count($new_data) != 0) {
                    $insert = $this->insertAll($new_data);

                } else {
                    $insert = true;
                }
                $menuModel = new PcMenu();
                $menu      = $menuModel->where('id', $data['menu_id'])->update(['type' => $type]);
                if ($insert && $del !== false && $menu !== false) {
                    $return['status'] = true;
                    $return['msg']    = '编辑成功';
                    Db::commit();
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $return['msg'] = $e;
            }
            return $return;
        } else {

            Db::startTrans();
            try {
                $menuModel = new PcMenu();
                $menu      = $menuModel->where('id', $data['menu_id'])->update(['type' => $type]);
                $rel       = $this->allowField(true)->save($data, ['id' => $data['id']]);
                if ($rel !== false && $menu !== false) {
                    $return['status'] = true;
                    $return['msg']    = '编辑成功';
                    Db::commit();
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $return['msg'] = $e;
            }
            return $return;
        }
    }

    //信息
    public function getInfo($id, $type)
    {

        $rel = $this->where('menu_id', 'eq', $id)->find();
        if ($type == 2) {
            $model       = new ArticleType();
            $rel['name'] = $model->where('id', 'eq', $rel['val'])->value('type_name');
        }
        if ($type == 3) {
            $model       = new Article();
            $rel['name'] = $model->where('id', 'eq', $rel['val'])->value('title');
        }
        if ($type == 5) {
            $rel['name'] = get_goods_info($rel['val']);
        }
        if ($type == 4) {
            $rel = $this->where('menu_id', 'eq', $id)->select();
            foreach ($rel as $k => $v) {
                $v['good_name'] = get_goods_info($v['val']);
            }
            $rel = [
                'id'  => 'true',
                'val' => $rel
            ];
        }
        return $rel;
    }

    //批量删除
    public function delMenu($ids = 0)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '菜单不存在'
        ];
        $goods  = $this::get($ids);
        if (!$goods) {
            return $result;
        }
        $this->startTrans();
        $res = $this->where(['id' => $ids])->delete();
        if (!$res) {
            $this->rollback();
            $result['msg'] = '删除失败';
            return $result;
        }
        $this->commit();
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }
}
