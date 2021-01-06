<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;


/**
 * 商品分类
 * Class GoodsCat
 * @package app\common\model
 * @author keinx
 */
class GoodsCat extends Common
{
    //const PLATFORM_ID = 0;                  //平台ID
    const TOP_CLASS_PARENT_ID = 0;          //顶级分类父类ID
    //const TOP_CLASS = 1;                    //顶级分类
    //const SUB_CLASS = 2;                    //子分类
    const DEFAULT_TYPE = 0;                 //默认类型
    const DEFAULT_TYPE_NAME = '通用类型';   //默认类型名称

    const STATUS_YES = 1;  //显示
    const STATUS_NO = 2; //不显示

    protected $autoWriteTimestamp = true;
    protected $createTime = 'utime';
    protected $updateTime = 'utime';


    /**
     * 获取商品分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $data = $this->field('id, parent_id, name, type_id, sort, image_id')
            ->order([ 'sort' => 'asc'])
            ->select();
        $return_data = $this->getTree($data);
        return $return_data;
    }


    /**
     * 获取全部分类
     * @param bool $id //排除分类ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllCat($id = false,$show = false)
    {
        if($id)
        {
            $where[] = ['id', 'neq', $id];
            $where[] = ['parent_id', 'neq', $id];
        }
        else
        {
            $where = [];
        }
        if(!$show){
            $where[] = array('status', 'eq', self::STATUS_YES);
        }
        $data = $this->field('id, parent_id, name, sort, image_id')
            ->where($where)
            ->order('sort asc')
            ->select();

        $return_data = $this->getTreeApi($data);
        return $return_data;
    }

    /**
     * 根据数组去返回树状结构
     */
    protected function getTreeApi($data,$parent_id=self::TOP_CLASS_PARENT_ID)
    {
        $children = [];
        foreach($data as $v){
            if($v['parent_id'] == $parent_id){
                $v['image_url'] = _sImage($v['image_id']);
                $v['child'] = $this->getTreeApi($data,$v['id']);
                $children[] = $v;
            }
        }
        return $children;
    }


    /**
     * 获取图片
     * @param $image_id
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getImage($image_id)
    {
        if($image_id)
        {
            return _sImage($image_id);
        }
        else
        {
            return '';
        }
    }


    /**
     * 获取类型名称
     * @param $type_id
     * @return string
     */
    protected function getTypeName($type_id)
    {
        if($type_id === self::DEFAULT_TYPE)
        {
            return self::DEFAULT_TYPE_NAME;
        }
        else
        {
            return model('common/GoodsType')->getNameById($type_id);
        }
    }



    /**
     * 获取分类
     * @param $parent_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChildClass($parent_id = self::TOP_CLASS_PARENT_ID)
    {
        $where[] = array('parent_id', 'eq', $parent_id);

        $data = $this->field('id, name, sort, image_id')
            ->where($where)
            ->order('sort asc')
            ->select();

        foreach($data as &$v)
        {
            if($v['image_id'])
            {
                $v['image_url'] = _sImage($v['image_id']);
            }
        }
        return $data;
    }


    /**
     * 添加商品分类
     * @param $data
     * @return int|string
     */
    public function add($data)
    {
        return $this->insert($data);
    }


    /**
     * 废弃，这样写真的真的没有必要。
     * 获取一个分类信息,
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//    public function getCatInfo($id)
//    {
//        $where[] = ['id', 'eq', $id];
//        $data = $this->field('id, name, parent_id, type_id, sort, image_id')
//            ->where($where)
//            ->find();
//        if($data)
//        {
//            return $data;
//        }
//        else
//        {
//            return false;
//        }
//    }


    /**
     * 编辑商品分类
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($data)
    {
        $status = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];

        if($data['id'] != ""){
            //当前是修改，就需要判断是否会陷入死循环
            if(!$this->checkDie($data['id'],$data['parent_id'])){
                return error_code(11101);
            }
            $id = $data['id'];
            unset($data['id']);
            $re = $this->save($data,['id'=>$id]);
        }else{
            $re = $this->save($data);
        }


        if($re){
            $status['status'] = true;
        }
        return $status;
    }


    /**
     * 判断这个分类是否可以删除
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getIsDel($id)
    {
        $status = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];
        $info = $this->where(['id'=>$id])->find();
        if($info){
            //父类判断是否有子类
            $result = $this->where('parent_id', 'eq', $id)->count();
            if($result>0){
                $status['msg'] = error_code(12010,true);
            }else{
                $status['status'] = true;
            }
            return $status;
        }else{
//            $status['msg'] = "没有找到此商品分类";
            return error_code(12017);
        }
    }


    /**
     * 删除商品分类
     * @param $id
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function del($id)
    {
        $is_del = $this->getIsDel($id);
        if(!$is_del['status']){
            return $is_del;
        }
        $where[] = ['id', 'eq', $id];
        $this->where($where)->delete();
        return $is_del;
    }


    /**
     * 根据父级ID获取全部子类信息
     * @param int $parentId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 16:50
     */
    public function getCatByParentId($parentId = 0)
    {
        $filter['parent_id'] = $parentId;

        $data = $this->field('id, name, parent_id, type_id, sort, image_id')
            ->where($filter)
            ->order('sort asc')
            ->select();

        return $data;
    }


    /**
     * 根据名称获取分类信息
     * @param string $name
     * @param bool $isForce
     * @return bool|int|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoByName($name = '', $isForce = false)
    {
        if (!$name ) {
            return false;
        }
        $cat_id = 0;
        $cat = $this->field('id')->where([['name', 'like', '%' . $name . '%']])->find();

        if (!$cat && $isForce) {
            $this->save([
                'name' => $name,
            ]);
            $cat_id = $this->getLastInsID();
        } elseif ($cat) {
            $cat_id = $cat['id'];
        }
        return $cat_id;
    }


    /**
     * 判断商品分类下面是否有某一个商品分类
     * @param $cat_parent_id
     * @param $cat_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isChild($cat_parent_id,$cat_id){
        if($cat_parent_id == $cat_id){
            return true;
        }
        $info = $this->where(['id'=>$cat_parent_id])->find();
        if(!$info){
            return false;
        }

        $children = $this->where(['parent_id'=>$info['id']])->select();
        foreach($children as $k => $v){
            if($this->isChild($v['id'],$cat_id)){
                return true;
            }
        }
        return false;
    }


    /**
     * 根据最后一级id 获取分类信息
     * @param $id
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCatByLastId($id, $data = [])
    {
        $info   = $this->where(['id' => $id])->find();
        $data[] = $info;
        if ($info['parent_id']) {
            return $this->getCatByLastId($info['parent_id'], $data);
        } else {
            return $data;
        }
    }


    /**
     * 获取名称
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNameById($id)
    {
        $return = error_code(10025);
        $where[] = ['id', 'eq', $id];
        $info = $this->field('name')->where($where)->find();
        if($info)
        {
            $return['status'] = true;
            $return['msg'] = '获取成功';
            $return['data'] = $info['name'];
        }

        return $return;
    }

    /***
     * 获取当前分类以及所有上级分类id
     * @param $id
     * @return string
     */
    public function getCatIdsByLastId($id)
    {
        $ids     = $this->getCatByLastId($id);
        $catInfo = _krsort($ids);
        if ($catInfo) {
            $ids = '';
            foreach ($catInfo as $key => $value) {
                $ids .= $value['id'] . ',';
            }
            return substr($ids, 0, -1);
        } else {
            return $id;
        }
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['parent_id'])){
            $where[] = ['parent_id', 'eq', $post['parent_id']];
        }


        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }

    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this::With('goodsType')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        //取所有的父节点，构建路径
        if(isset($post['parent_id'])){
            $re['parents'] = $this->getParents($post['parent_id']);
        }else{
            $re['parents'] = [];
        }


        return $re;
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
            if ($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }
            if (isset($v['image_id']) && $v['image_id']) {
                $list[$k]['image_id'] = _sImage($v['image_id']);
            }
        }
        return $list;
    }

    /**
     * 把数组构建成 一维的有缩进的树
     * @param $list
     * @param int $parent_menu_id
     * @return array
     */
    public function createTree($list,$parent_menu_id=0,$level=1)
    {
        $data = [];
        $str = "";
        for($i=0;$i<$level;$i++){
            $str .= "|----";
        }
        foreach($list as $k => $v){
            if($v["parent_id"] == $parent_menu_id){
                $v['name'] = $str.$v['name'];
                $data[] = $v;
                $ch = $this->createTree($list,$v['id'],$level+1);
                $data = array_merge($data,$ch);
            }
        }

        return $data;
    }

//递归取得所有的父节点
    public function getParents($id){
        $data = [];
        $info = $this->where(['id' => $id])->find();
        if(!$info){
            return $data;
        }
        //判断是否还有父节点，如果有，就取父节点，如果没有，就返回了
        if($info['parent_id'] !=self::TOP_CLASS_PARENT_ID){
            $data = $this->getParents($info['parent_id']);      //返回空数组或者二维数组
        }
        array_push($data,$info->toArray());
        return $data;
    }

    //商品分类
    public function goodsType()
    {
        return $this->hasOne('GoodsType','id', 'type_id')->bind([
            'type_name' => 'name'
        ]);
    }

    /**
     * 预先判断死循环
     * @param $id       当前id
     * @param $p_id     预挂载的父id
     * @param int $n    循环次数
     * @return bool     如果为true就是通过了，否则就是未通过
     */
    private function checkDie($id,$p_id,$n=10)
    {
        //设置计数器，防止极端情况下陷入死循环了（其他地方如果设置的有问题死循环的话，这里就报错了）
        if($n <= 0){
            return false;
        }
        if($id == $p_id){
            return false;
        }
        if($p_id == self::TOP_CLASS_PARENT_ID){
            return true;
        }
        $pinfo = $this->where(['id'=>$p_id])->find();
        if(!$pinfo){
            return false;
        }
        if($pinfo['parent_id'] == $id){
            return false;
        }
        $n--;
        return $this->checkDie($id,$pinfo['parent_id'],$n);
    }

}