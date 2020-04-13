<?php

namespace addons\Pc\controller;

class Api
{

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
            exit;
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
    }

    //楼层
    public function getFloor()
    {
        $floorModel = new \addons\Pc\model\PcFloor();
        $page       = input('page', 1);
        $limit      = input('limit', 5);
        $keyWord    = input('key_word', '');
        return $floorModel->getFloor($page, $limit, $keyWord);
    }

    public function getMenu()
    {
        $result = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => [],
        ];
        //取菜单
        $menuTypeModel = new \addons\Pc\model\PcMenu();
        $menu          = $menuTypeModel->getMenu();
        //基本设置
        $settingsModel = new \addons\Pc\model\PcSettings();
        $settings      = $settingsModel->select();
        //友链
        $linkModel = new \addons\Pc\model\PcFriendshipLink();
        $linkPage  = input('link_page', 1);
        $linkLimit = input('link_limit', 10);
        $isPage    = input('link_isPage', false);
        $link      = $linkModel->getLink($linkPage, $linkLimit, $isPage);
        if ($menu && $settings && $link) {
            $result = [
                'status' => true,
                'msg'    => '获取成功',
                'data'   => [
                    'menu'     => $menu,
                    'settings' => $settings,
                    'link'     => $link
                ],
            ];
        }
        return json($result);
    }
}
