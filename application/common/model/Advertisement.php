<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\model\AdvertPosition;
use think\Validate;

class Advertisement extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const URL_TYPE          = 1;
    const GOODS_TYPE        = 2;
    const ARTICLE_TYPE      = 3;
    const ARTICLE_LIST_TYPE = 4;

    protected $rule =   [
        'name'              =>  'require|max:50',
        'img'               =>  'require|alphaNum',
        'type'              =>  'number|require',
        'url'               =>  'requireIf:type,'.self::URL_TYPE.'|url',
        'goods_id'          =>  'requireIf:type,'.self::GOODS_TYPE.'|number',
        'article_id'        =>  'requireIf:type,'.self::ARTICLE_TYPE.'|number',
        'article_type_id'   =>  'requireIf:type,'.self::ARTICLE_LIST_TYPE.'|number',
        'position_id'       =>  'require',
        'sort'              =>  'number',
    ];

    protected $msg  =   [
        'name.require'              =>  '请输入广告名称',
        'name.max'                  =>  '广告名最多不能超过50个字符',
        'type.number'               =>  '广告类型非法',
        'type.require'              =>  '请选择要添加的广告类型',
        'img.require'               =>  '请选择要上传的广告图片',
        'img.alphaNum'              =>  '广告图片非法',
        'url.url'                   =>  'url链接不是有效的地址',
        'url.requireIf'             =>  '请输入广告URL链接',
        'goods_id.requireIf'        =>  '请选择广告商品',
        'article_id.requireIf'      =>  '请选择广告文章',
        'article_type_id.requireIf' =>  '请选择文章分类',
        'position_id.require'       =>  '请选择要添加的广告位',
        'sort'                      =>  '排序必须是数字',
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
        $list = $this->with('advertPosition')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }

    /**
     *  添加广告方法
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status' => true, 'msg' => '保存成功' , 'data' => ''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            $positionModel = new AdvertPosition();
            $data['code'] = $positionModel->where('id',$data['position_id'])->value('code');
            switch ($data['type']) {
                case self::URL_TYPE:
                    $data['val'] = $data['url'];
                    break;
                case self::GOODS_TYPE:
                    $data['val'] = $data['goods_id'];
                    break;
                case self::ARTICLE_TYPE:
                    $data['val'] = $data['article_id'];
                    break;
                case self::ARTICLE_LIST_TYPE:
                    $data['val'] = $data['article_type_id'];
                    break;
                default:
                    break;
            }
            if (!$this->allowField(true)->save($data)) {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        return $result;
    }


    /**
     *  修改广告信息
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status' => true, 'msg' => '保存成功' , 'data' => ''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            $positionModel = new AdvertPosition();
            $data['code'] = $positionModel->where('id',$data['position_id'])->value('code');
            switch ($data['type']) {
                case self::URL_TYPE:
                    $data['val'] = $data['url'];
                    break;
                case self::GOODS_TYPE:
                    $data['val'] = $data['goods_id'];
                    break;
                case self::ARTICLE_TYPE:
                    $data['val'] = $data['article_id'];
                    break;
                case self::ARTICLE_LIST_TYPE:
                    $data['val'] = $data['article_type_id'];
                    break;
                default:
                    break;
            }
            if (!$this->allowField(true)->save($data,['id'=>$data['id']])) {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        return $result;

    }




    /**
     *  table WHERE搜索
     * User:tianyu
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if(isset($post['position_id']) && $post['position_id'] != ""){
            $where[] = ['position_id', 'eq', $post['position_id']];
        }
        if(isset($post['utime']) && $post['utime'] != ""){
            $date_array = explode('~',$post['utime']);
            $sutime = strtotime($date_array[0].'00:00:00',time());
            $eutime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['utime', ['EGT',$sutime],['ELT',$eutime],'and'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


    protected function tableFormat($list)
    {
        foreach ( $list as $key => $val ) {
            $list[$key]['img'] = _sImage($val['img']);
            $list[$key]['advertType'] = config('params.advertType')['type'][$val['type']];
            $list[$key]['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
            $list[$key]['utime'] = date('Y-m-d H:i:s', $val['utime']);

            if ($val['type'] == self::GOODS_TYPE) {
                $list[$key]['value'] = model('Goods')->where('id', $val['val'])->value('name');
            } elseif ($val['type'] == self::ARTICLE_TYPE) {
                $list[$key]['value'] = model('Article')->where('id', $val['val'])->value('title');
            } elseif ($val['type'] == self::ARTICLE_LIST_TYPE){
                $list[$key]['value'] = model('ArticleType')->where('id', $val['val'])->value('type_name');
            } else {
                $list[$key]['value'] = $val['val'];
            }
        }

        return $list;
    }


    /**
     *
     *  根据广告位code 获取广告列表
     * @param $code
     * @param $page
     * @param $limit
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdvertList($code,$page,$limit)
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];


        $field = 'id,position_id,code,name,img,type,val,sort';
        $where[] = ['code', 'eq', $code];

        $list = $this
            ->field($field)
            ->where($where)
            ->order('sort ASC')
            ->page($page,$limit)
            ->select();

        $count = $this
            ->field($field)
            ->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            foreach($list as $key => $val)
            {
                $list[$key]['img'] = _sImage($val['img']);
            }
        }
        $result['data']['list'] = $list;
        $result['data']['count'] = $count;
        return $result;
    }


    /**
     *  关联广告位表
     * @return \think\model\relation\HasOne
     */
    public function advertPosition()
    {
        return $this->hasOne('AdvertPosition','id','position_id')->bind(['pname'=>'name']);
    }

}