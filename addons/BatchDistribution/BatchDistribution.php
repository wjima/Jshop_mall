<?php
namespace addons\BatchDistribution;

use app\common\model\User;
use myxland\addons\Addons;
/**
 * 批量打印配货单
 */
class BatchDistribution extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'BatchDistribution',
        'title' => '批量打印配货单',
        'description' => '批量打印配货单',
        'status' => 0,
        'author' => 'mark',
        'version' => '1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }

    /**
     * 批量打印按钮
     */
    public function orderExtBtn()
    {
        echo '<button class="layui-btn layui-btn-sm" id="batch-print-distribution"><i class="iconfont icon-cancel"></i>批量打印配货单</button>';
    }
    /**
     * 批量打印JS
     */
    public function orderExtJs()
    {
        $str = " $('#batch-print-distribution').on('click', function () {
            var checkStatus = tables.checkStatus('order'), data = checkStatus.data;
            if (data.length < 1) {
                layer.msg('请先选中订单');
                return false;
            }
            var ids = '';
            $.each(data, function () {
                ids += this.order_id + ',';
            });
            ids = ids.substring(0, ids.length - 1);
            var url = '" . get_addon_url('BatchDistribution://Order/batchPrint') . "?ids=' + ids;
            var totalPage = data.length;
            var page = 1;
            if (ids) {
                window.open(url, '_blank');
            }
            return false;
        });";
        echo $str;
    }

}