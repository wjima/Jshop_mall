<?php
namespace app\common\model;
use think\model\concern\SoftDelete;

class TemplateMessage extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const SEND_STATUS_NO = 1; //未发送
    const SEND_STATUS_YES = 2; //已发送
    const TYPE_ORDER = 'create_order'; //下单
    const TYPE_PAYMENT = 'order_payed'; //支付成功
    const TYPE_AFTER_SALE = 'after_sale'; //售后

    /**
     * 添加发送记录
     * @param $data
     * @return array
     */
    public function addSend($data)
    {
        $return = error_code(10026);
        $return['data'] = $this->save($data);
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '创建成功';
        }

        return $return;
    }


    /**
     * 发送成功
     * @param $id
     * @return array
     */
    public function sendSuccess($id)
    {
        $return = error_code(10018);
        $data['status'] = self::SEND_STATUS_YES;
        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->save($data, $where);
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '操作成功';
        }
        return $return;
    }


    /**
     * 获取消息信息
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($id)
    {
        $return = error_code(10025);

        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->where($where)->find();
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }

        return $return;
    }


    /**
     * 获取没有发生的消息列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNoSendMessage()
    {
        $return = [
            'status' => false,
            'msg' => error_code(10025,true),
            'data' => [],
            'count' => 0
        ];

        $where[] = ['status', 'eq', self::SEND_STATUS_NO];
        $return['data'] = $this->where($where)->select();
        $return['count'] = $this->where($where)->count();
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }
        return $return;
    }


    /**
     * 删除消息
     * @param $id
     * @return array
     */
    public function delMessage($id)
    {
        $return = error_code(10034);

        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->where($where)->delete();
        if($return['data'] !== false)
        {
            $return['status'] = true;
            $return['msg'] = '删除消息成功';
        }
        return $return;
    }
}