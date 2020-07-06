<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Db;

class WeixinMessage extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    const TYPE_TEXT = 1;        //文本消息
    const TYPE_IMAGE = 2;       //图文消息
    const TYPE_IMAGE_TEXT = 3;       //图文消息
    const ATTENTION_YSE = 1;    //是关注自动回复
    const ATTENTION_NO = 2;     //不是关注自动回复
    const DEFAULT_YES = 1;  //默认回复，无关键词时回复
    const DEFAULT_NO = 2;   //不是默认回复
    const ENABLE_YES = 1; //启用
    const ENABLE_NO = 2; //禁用

    /**
     * 保存消息
     * @param array $params
     * @return array
     */
    public function addData($params = [])
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => [],
        ];
        //        $id     = 0;
        if (!isset($params['enable'])) {
            $params['enable'] = self::ENABLE_YES;
        }
        if (!isset($params['is_attention'])) {
            $params['is_attention'] = self::ATTENTION_NO;
        }
        if (!isset($params['is_default'])) {
            $params['is_default'] = self::DEFAULT_NO;
        }
        if ($params['type'] != self::TYPE_IMAGE_TEXT) {
            $params['params'] = json_encode($params['params']);
        }
        Db::startTrans();

        if ($params['is_default'] == self::DEFAULT_YES) {
            $this->update(['is_default' => self::DEFAULT_NO], ['is_default' => self::DEFAULT_YES]);
        }
        if ($params['is_attention'] == self::ATTENTION_YSE) {
            $this->update(['is_attention' => self::ATTENTION_NO], ['is_attention' => self::ATTENTION_YSE]);
        }
        if ($params['id']) {
            if ($this->save($params, ['id' => $params['id']]) === false) {
                Db::rollback();
                return  error_code(10004);
            }
            $id = $params['id'];
        } else {
            if (!$this->save($params)) {
                Db::rollback();
                return  error_code(10004);
            }
            $id = $this->getLastInsID();
        }
        if ($params['is_attention']) {
            $res = $this->where([['id', 'neq', $id]])->update(['is_attention' => self::ATTENTION_NO]);
            if ($res === false) {
                Db::rollback();
                return  error_code(10004);
            }
        }
        Db::commit();
        return $result;
    }


    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%' . $post['name'] . '%'];
        }
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', '=', $post['type']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id desc'];
        return $result;
    }


    /**
     * 数据转换
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as &$val) {
            $val['type_name'] = config('params.weixin_message')['type'][$val['type']];
            $val['utime']     = getTime($val['utime']);

            $val['is_attention'] = config('params.weixin_message')['attention'][$val['is_attention']];
            $val['is_default']   = config('params.weixin_message')['default'][$val['is_default']];
            $val['enable']       = config('params.weixin_message')['enable'][$val['enable']];
        }
        return $list;
    }

    /**
     * 获取自动回复消息内容
     * @param string $type
     * @param string $keyword
     * @return array|null|\PDOStatement|string|\think\Model
     */
    public function weixinReply($type = '', $keyword = '')
    {
        $wechat  = &load_wechat('Receive');
        $where   = [];
        $where[] = ['enable', '=', self::ENABLE_YES];
        if ($type == 'subscribe') {
            $where[] = ['is_attention', '=', self::ATTENTION_YSE];
        } else {
            if ($keyword != '') {
                $where[] = ['name', 'like', "%" . $keyword . "%"];
            }
        }
        $data = $this->where($where)->find();
        if (!$data) { //没有查询到的话，就取默认
            $where   = [];
            $where[] = ['enable', '=', self::ENABLE_YES];
            $where[] = ['is_default', '=', self::DEFAULT_YES];
            $data    = $this->where($where)->find();
        }
        if ($data) {
            $data = $data->toArray();


            if ($data['type'] == self::TYPE_TEXT || $data['type'] == self::TYPE_IMAGE) {
                $data['params'] = json_decode($data['params'], true);
                return $wechat->text($data['params']['content'])->reply();
            } elseif ($data['type'] == self::TYPE_IMAGE_TEXT) {
                $data['params'] = json_decode($data['params'], true);
                if (isset($data['params']['media_id']) && $data['params']['media_id']) {
                    $weixinMediaMessage = new WeixinMediaMessage();
                    foreach ($data['params']['media_id'] as $val) {
                        $data['media'][] = $weixinMediaMessage->getInfo($val);
                    }
                    $newsdata = [];
                    foreach ($data['media'] as $key => $val) {
                        //未填写url时，自动输出前台页面当前站点地址
                        if (!$val['url']) {
                            $host = \request()->host();
                            $host = (\request()->isSsl() ? 'https://' : 'http://') . $host; //增加洗衣判断
                            $val['url'] = $host . '/wap/#/pages/article/index?id=' . $val['id'] . '&id_type=3';
                        }
                        $newsdata[] = [
                            'Title'       => $val['title'],
                            'Description' => $val['brief'],
                            'PicUrl'      => $val['image_url'],
                            'Url'         => $val['url'],
                        ];
                    }
                    return $wechat->news($newsdata)->reply();
                }
            }
        }
        return $wechat->text('欢迎光临')->reply(); //没有默认的时候返回这个
    }

    /**
     * 根据id获取信息
     * @param int $id
     * @return array
     */
    public function getInfo($id = 0)
    {
        return $this->get($id);
    }
}
