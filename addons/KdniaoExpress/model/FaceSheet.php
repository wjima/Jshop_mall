<?php
/**
 * 电子面单模型
 */
namespace addons\KdniaoExpress\model;

use think\Model;

class FaceSheet extends Model
{

    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];

        if (isset($post['logi_code']) && $post['logi_code']) {
            $where[] = ['logi_code', 'like', '%' . $post['logi_code'] . '%'];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id'=>'desc'];
        return $result;
    }

    /**
     * 格式化数据
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        if (!$list->isEmpty()) {
            $list = $list->toArray();
            foreach ((array)$list as $key => $value) {
                $list[$key]['logi_name']       = get_logi_info($value['logi_code'], 'logi_name');
            }
        }
        return $list;
    }

    public function faceSheetSave($input){
        $result = [
            'status' => true,
            'msg' => '成功',
            'data' => []
        ];

        $data = [];
        $data['logi_code'] = $input['logi_code'];
        $data['customer_name'] = $input['customer_name'];
        $data['customer_pwd'] = $input['customer_pwd'];
        $data['send_site'] = $input['send_site'];
        $data['template_size'] = $input['template_size'];

        if(isset($input['id']) && $input['id']){
            if(!$this->save($data, ['id'=>$input['id']])){
                $result['status'] = false;
                $result['msg'] = '失败';
            }
        }else{
            if(!$this->save($data)){
                $result['status'] = false;
                $result['msg'] = '失败';
            }
        }
        return $result;
    }

    /**
     * 订单列表打印面单获取设置好的
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList(){
        $list = $this->select();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['logi_name'] = get_logi_info($v['logi_code'], 'logi_name');
            }
        }
        return $list;
    }

}
