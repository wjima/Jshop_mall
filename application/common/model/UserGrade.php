<?php


namespace app\common\model;


class UserGrade extends Common
{
    const IS_DEF_YES = 1;
    const IS_DEF_NO = 2;

    //新增或者修改用户等级信息
    public function toEdit($id,$name,$is_def){
        $result = [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];

        if($is_def != self::IS_DEF_NO && $is_def != self::IS_DEF_YES){
            $is_def = self::IS_DEF_NO;
        }

        $where[] = ['id','eq',$id];
        $info = $this->where($where)->find();
        if($info){
            //编辑
            $data['name'] = $name;
            $data['is_def'] = $is_def;

            $this->save($data,$where);
        }else{
            //新增
            $data['id'] = $id;
            $data['name'] = $name;
            $data['is_def'] = $is_def;
            $this->save($data);
        }
        if($is_def == self::IS_DEF_YES){
            $data1['is_def'] = self::IS_DEF_NO;
            $where1[] = ['id','neq', $id];
            $where1[] = ['is_def','eq', self::IS_DEF_YES];
            $s = new UserGrade();
            $s->save($data1,$where1);
        }

        return $result;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $result['where'] = [];
        $result['field'] = "*";
        $result['order'] = "id asc";
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
        foreach($list as $k => $v){
            if($v['is_def'] == self::IS_DEF_YES){
                $list[$k]['is_def'] = "默认";
            }else{
                $list[$k]['is_def'] = "";
            }
        }
        return $list;
    }

    /***
     * 获取全部会员等级
     * @return array
     */
    public function getAll()
    {
        $data = $this->order('id asc')->select();
        if (!$data->isEmpty()) {
            return $data->toArray();
        }
        return [];
    }

}