<?php
/**
 * 微信模板
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/19
 * Time: 下午12:00
 */

namespace app\common\model;

class Template extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    const TYPE_MINI = 1; //小程序类型

    /**
     * 保存模板信息
     * @param array $data
     * @return int|string
     */
    public function doAdd($data = [])
    {
        $result = $this->save($data);
        if ($result) {
            return $this->getLastInsID();
        }
        return $result;
    }

    /**
     * 根据类型获取所有模板列表
     * @param int $type
     * @return array
     */
    public function getAllTemplate($type = self::TYPE_MINI)
    {
        $data = $this->where(['type' => $type])->select();
        if (!$data->isEmpty()) {
            return $data->toArray();
        }
        return [];
    }

    /**
     * 获取微信小程序模板配置数据
     * @param int $template_id template表中id
     * @param string $page
     * @return array
     */
    public static function getWxaPage($template_id = 0, $page = '')
    {
        $pages = [
            //0为$template_id
            '1' => [
                'pages/index/index' => [
                    'tag' => '首页',
                    'title' => '商城首页',
                ],
                'pages/other/article/article' => [
                    'tag' => '文章',
                    'title' => '文章',
                ],
                'pages/member/order/orderDetail/orderDetail' => [
                    'tag' => '订单明细',
                    'title' => '订单明细',
                ]
            ]

        ];
        return isset($pages[$template_id][$page]) ? $pages[$template_id][$page] : [];
    }


}