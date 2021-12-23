<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\facade\Cache;
use think\model\concern\SoftDelete;
use think\Validate;

class Article extends Common
{
    const IS_PUB_YES = 1;   //发布
    const IS_PUB_NO = 2;    //暂不发布

    use SoftDelete;
    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';


    //验证规则
    protected $rule     =   [
        'title'         =>  'require|max:200',
        'brief'         =>  'max:100',
        'type_id'       =>  'require',
        'sort'          =>  'number',
    ];

    protected $msg          =   [
        'title.require'     =>  '文章标题必须填写',
        'title.max'         =>  '标题名称最多不能超过200个字符',
        'brief.max'         =>  '文章简介最多不能超过100个字符',
        'type_id.require'   =>  '请选择文章分类',
        'sort.number'       =>  '排序必须是数字类型',
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
        $list = $this->with('articleType')->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv')->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        foreach($list as $key => $val)
        {
            $list[$key]['cover'] = _sImage($val['cover']);
            $list[$key]['is_pub'] = config('params.article')['is_pub'][$val['is_pub']];
            $list[$key]['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
            $list[$key]['utime'] = date('Y-m-d H:i:s',$val['utime']);
        }
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }


    /**
     *  添加文章数据   方法
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {

        $validate = new Validate($this->rule,$this->msg);
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data))
            {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        Cache::clear();
        return $result;
    }


    /**
     *  文章编辑更新
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if(!$this->allowField(true)->save($data,['id'=>$data['id']]))
            {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        Cache::clear();
        return $result;
    }


    /**
     * where 搜索条件
     * author: tianyu
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
        if(isset($post['utime']) && $post['utime'] != ""){
            $date_array = explode('到',$post['utime']);
            $sutime = strtotime($date_array[0].'00:00:00',time());
            $eutime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['utime', ['EGT',$sutime],['ELT',$eutime],'and'];
        }
        if(isset($post['type_id']) && $post['type_id'] != ""){
            $where[] = ['type_id', 'eq', $post['type_id']];
        }
        if(isset($post['is_pub']) && $post['is_pub'] != ""){
            $where[] = ['is_pub', 'eq', $post['is_pub']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


    /**
     * 获取文章列表
     * @param bool $type_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleList($type_id = 0, $page = 1, $limit = 10)
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        $articleTypeModel = new ArticleType();

        $type_name = "文章分类";
        if($type_id != 0){
            $info = $articleTypeModel->where('id',$type_id)->find();
            if($info){
                $type_name = $info['type_name'];
            }
        }


        // 发布状态
        $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $where[] = ['type_id', 'eq', $type_id];

        $list = $this->where($where)
            ->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv,brief')
            ->order('sort asc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']);
                $v['ctime'] = getTime($v['ctime']);
            }
        }

        //子文章分类
        $articleTypeModel = new ArticleType();
        foreach ($list as &$v){
            $res = $articleTypeModel->where(['id'=>$v['type_id']])->find();
            $v['type_name'] = $res['type_name'];
        }
        $articleTypeList = $articleTypeModel->where('pid', $type_id)->order('sort asc')->select();

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
            'article_type' => $articleTypeList,
            'type_name' => $type_name
        ];

        return $result;
    }


    /**
     * 文章搜索
     * @param $search_name
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search($search_name, $page = 1, $limit = 10){

        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        // 发布状态
        $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $where[] = ['title|brief', 'like', '%'.$search_name.'%'];

        $list = $this->where($where)
            ->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv,brief')
            ->order('sort asc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)->count();

        if(!$list->isEmpty())
        {
            $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']);
                $v['ctime'] = getTime($v['ctime']);
            }
        }

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
        ];

        return $result;

    }


    /**
     * 获取指定id 的文章详情
     * @param $article_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleDetail($article_id)
    {
        $result = error_code(10801); //文章不存在或已删除


        $where[] = ['id', 'eq', $article_id];
        $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $data = $this->field('id,title,content,type_id,ctime,utime,pv,brief')
            ->where($where)
            ->find();

        if(!empty($data))
        {
            //$data['content'] = clearHtml($data['content'], ['width', 'height']);//清除文章中宽高
            $data['content'] = str_replace("clear: both;", "", $data['content']);
            $data['content'] = str_replace("<img", "<img style='max-width: 100%'", $data['content']);
            $typeModel = new ArticleType();
            $data['article_type'] = $typeModel->getArticleTypeFather($data['type_id']);
            $data['ctime'] = time_ago($data['ctime']);
            $add_pv = $this->where(['id' => $article_id])->update(['pv' => $data['pv'] + 1]);
            if (!$add_pv) {
                return error_code(10037);
            }
            //上一篇，下一篇
            $data['up'] = [];
            $data['down'] = [];

            $uwhere['is_pub'] = ['is_pub', 'eq', self::IS_PUB_YES];
            $uwhere['type_id'] = ['type_id', 'eq', $data['type_id']];
            $list = $this->field('id,title')->where($uwhere)->select();
            if(count($list)>1){
                foreach($list as $k => $v){
                    if($v['id'] == $data['id']){
                        if($k == 0 || $k == count($list)-1){
                            if($k == 0){
                                $data['down'] = $list[$k+1];
                            }else{
                                $data['up'] = $list[$k-1];
                            }
                        }else{
                            $data['up'] = $list[$k-1];
                            $data['down'] = $list[$k+1];
                        }
                    }
                }
            }

            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }
        return $result;
    }


    /**
     * @return \think\model\relation\HasOne
     */
    public function articleType()
    {
        return $this->hasOne('ArticleType','id','type_id')->bind(['type_name']);
    }

}
