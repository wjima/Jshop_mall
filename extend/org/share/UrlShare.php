<?php
namespace org\share;

/**
 * Class UrlShare
 * @package org\share
 */
class UrlShare implements BaseShare
{
    //场景值，1首页，2商品详情页，3拼团详情页,4邀请页面,5文章页面，6参团页面，7自定义页面，8智能表单，9团购秒杀
    const PAGE_INDEX = 1;
    const PAGE_GOODS = 2;
    const PAGE_PINTUAN = 3;
    const PAGE_INV = 4;
    const PAGE_ARTICLE = 5;
    const PAGE_ADDPINTUAN = 6;
    const PAGE_PAGE = 7;
    const PAGE_FORM = 8;
    const PAGE_GROUP = 9;
    const PAGE_BARGAIN = 10;//砍价

    //1普通h5，2微信小程序，3微信公众号（h5），4头条系小程序,5pc，6阿里小程序
    const CLIENT_H5 = 1;
    const CLIENT_WXMNAPP = 2;
    const CLIENT_WXOFFICIAL = 3;
    const CLIENT_TTMNAPP = 4;
    const CLIENT_PC = 5;
    const CLIENT_ALIMNAPP = 6;


    /**
     * @param $client
     * @param $page
     * @param $userShareCode
     * @param $url
     * @param $params
     * @return array|mixed
     */
    public function share($client, $page, $userShareCode, $url, $params)
    {
        $re = $this->getCode($client, $page, $userShareCode, $url, $params);
        if (!$re['status']) {
            return $re;
        }
        $re['data'] = $this->getUrl($url, $re['data']['code']);
        return $re;
    }


    /**
     * 获得分享的code
     * @param $client
     * @param $page
     * @param $userShareCode
     * @param $url
     * @param $params
     * @return array|mixed
     */
    protected function getCode($client, $page, $userShareCode, $url, $params)
    {
        //检查params参数是否正确
        $result = $this->en_params($page, $params);
        if (!$result['status']) {
            return $result;
        }
        $code = $this->en_url($page, $userShareCode, $result['data']);
        $result['data'] = [
            'code' => $code
        ];
        return $result;
    }


    /**
     * 根据获得的code，拼接url
     * @param $url
     * @param $code
     * @return string
     */
    protected function getUrl($url, $code)
    {
        return $url."?scene=".$code;
    }


    /**
     * url参数加密
     * @param $page
     * @param $userShareCode
     * @param $params_str
     * @return string
     */
    private function en_url($page, $userShareCode, $params_str)
    {
        return $page. "-".$userShareCode."-".$params_str;
    }


    /**
     * url参数解密
     * @param $code
     * @return array|mixed
     */
    public function de_url($code)
    {
        $result = [
            'status' => false,
            'data' => [
                'page' => '',
                'userShareCode'=> '',
                'params' => []
            ],
            'msg' => ''
        ];
        $arr = explode('-', $code);
        if (count($arr) != 3) {
            return $result;
        }
        $result['data']['page'] = $arr[0];
        $result['data']['userShareCode'] = $arr[1];

        $params = $this->de_params($arr[0], $arr[2]);
        if ($params['status']) {
            $result['data']['params'] = $params['data'];
            $result['status'] = true;
        } else {
            return $params;
        }
        return $result;
    }


    /**
     * 检查参数，拼接参数
     * @param $page
     * @param $params
     * @return array|mixed
     */
    private function en_params($page, $params)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $str = "";
        switch ($page) {
            case self::PAGE_INDEX:
                break;
            case self::PAGE_GOODS:
                if (isset($params['goods_id'])) {
                    $str = $params['goods_id'];
                } else {
                    $result['msg'] = '参数必须传goods_id';
                    return $result;
                }
                break;
            case self::PAGE_PINTUAN:
                if (isset($params['goods_id'])) {
                    $str = $params['goods_id'];
                } else {
                    $result['msg'] = '参数必须传拼团商品的goods_id';
                    return $result;
                }
                break;
            case self::PAGE_INV:
                if (isset($params['store'])) {
                    $str = $params['store'];
                } else {
                    $result['msg'] = '参数必须传store';
                    return $result;
                }
                break;
            case self::PAGE_ARTICLE:
                if (isset($params['article_id']) && isset($params['article_type'])) {
                    $str = $params['article_id'] . "_" . $params['article_type'];
                } else {
                    $result['msg'] = '参数必须传article_id,article_type';
                    return $result;
                }
                break;
            case self::PAGE_ADDPINTUAN:
                if (isset($params['goods_id']) && isset($params['group_id']) && isset($params['team_id'])) {
                    $str = $params['goods_id'] . "_" . $params['group_id'] . "_" . $params['team_id'];
                } else {
                    $result['msg'] = '参数必须传goods_id,group_id,team_id';
                    return $result;
                }
                break;
            case self::PAGE_PAGE:
                if (!isset($params['page_code'])) {
                    $str = $params['page_code'];
                } else {
                    $result['msg'] = '参数必须传page_code';
                    return $result;
                }
                break;
            case self::PAGE_FORM:
                if (!isset($params['id'])) {
                    $str = $params['id'];
                } else {
                    $result['msg'] = '参数必须传id';
                    return $result;
                }
                break;
            case self::PAGE_GROUP:
                if (isset($params['goods_id']) && isset($params['group_id'])){
                    $str = $params['goods_id'] . "_" . $params['group_id'];
                } else {
                    $result['msg'] = '参数必须传goods_id,group_id';
                    return $result;
                }
                break;
            case self::PAGE_BARGAIN:
                if (isset($params['id']) && isset($params['type'])&& isset($params['record_id'])){
                    $str = $params['id'] . "_" . $params['type']. "_" . $params['record_id'];
                } else {
                    $result['msg'] = '参数必须传id,type,record_id';
                    return $result;
                }
                break;

            default:
                return error_code(10000);
        }

        $result['status'] = true;
        $result['data'] = $str;
        return $result;
    }


    /**
     * @param $page
     * @param $str
     * @return array|mixed
     */
    private function de_params($page, $str)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $arr = explode('_', $str);
        switch ($page) {
            case self::PAGE_INDEX:
                $result['status'] = true;
                break;
            case self::PAGE_GOODS:
                if (count($arr) == 1) {
                    $result['data']['goods_id'] = $arr[0];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_PINTUAN:
                //拼团详情页
                if (count($arr) == 1) {
                    $result['data']['goods_id'] = $arr[0];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_INV:
                if (count($arr) == 1) {
                    $result['data']['store'] = $arr[0];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_ARTICLE:
                if(count($arr) == 2){
                    $result['data']['article_id'] = $arr[0];
                    $result['data']['article_type'] = $arr[1];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_ADDPINTUAN:
                if (count($arr) == 3) {
                    $result['data']['goods_id'] = $arr[0];
                    $result['data']['group_id'] = $arr[1];
                    $result['data']['team_id'] = $arr[2];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_PAGE:
                if (count($arr) == 1) {
                    $result['data']['page_code'] = $arr[0];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_FORM:
                if (count($arr) == 1) {
                    $result['data']['id'] = $arr[0];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_GROUP:
                if (count($arr) == 2) {
                    $result['data']['goods_id'] = $arr[0];
                    $result['data']['group_id'] = $arr[1];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_BARGAIN:
                if (count($arr) == 3) {
                    $result['data']['id']        = $arr[0];
                    $result['data']['type']      = $arr[1];
                    $result['data']['record_id'] = $arr[2];
                    $result['status']            = true;
                }
                break;
            default:
                return error_code(10000);
        }
        return $result;
    }
}
