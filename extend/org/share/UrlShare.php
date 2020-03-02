<?php

namespace org\share;

class UrlShare implements BaseShare
{

    //场景值，1首页，2商品详情页，3拼团详情页
    const PAGE_INDEX = 1;
    const PAGE_GOODS = 2;
    const PAGE_PINTUAN = 3;


    public function share($client, $page, $userShareCode, $url, $params){
        $re = $this->getCode($client, $page, $userShareCode, $url, $params);
        if(!$re['status']){
            return $re;
        }
        $re['data'] = $this->getUrl($url,$re['data']['code']);
        return $re;
    }

    //获得分享的code
    protected function getCode($client, $page, $userShareCode, $url, $params){
        //检查params参数是否正确
        $result = $this->en_params($page,$params);
        if(!$result['status']){
            return $result;
        }

        $code = $this->en_url($page, $userShareCode, $result['data']);
        $result['data'] = [
            'code' => $code
        ];
        return $result;
    }

    //根据获得的code，拼接url
    protected function getUrl($url,$code){
        return $url."?scene=".$code;
    }

    //url参数加密
    private function en_url($page, $userShareCode, $params_str){
        return $page. "-".$userShareCode."-".$params_str;
    }
    //url参数解密
    public function de_url($code){
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
        if(count($arr) != 3){
            return $result;
        }
        $result['data']['page'] = $arr[0];
        $result['data']['userShareCode'] = $arr[1];

        $params = $this->de_params($arr[0], $arr[2]);
        if($params['status']){
            $result['data']['params'] = $params['data'];
            $result['status'] = true;
        }else{
            return $params;
        }
        return $result;
    }

    //检查参数，拼接参数
    private function en_params($page, $params){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $str = "";
        switch($page){
            case self::PAGE_INDEX :

                break;
            case self::PAGE_GOODS :
                if(isset($params['goods_id'])){
                    $str = $params['goods_id'];
                }else{
                    $result['msg'] = '参数必须传goods_id';
                    return $result;
                }
                break;
            case self::PAGE_PINTUAN :
                if(isset($params['goods_id'])){
                    $str = $params['goods_id'];
                }else{
                    $result['msg'] = '参数必须传goods_id';
                    return $result;
                }
                if(isset($params['group_id'])){     //拼团规则ID
                    $str .= "_" . $params['group_id'];
                }else{
                    $result['msg'] = '参数必须传group_id';
                    return $result;
                }
                if(isset($params['team_id'])){     //团队ID
                    $str .= "_".$params['team_id'];
                }else{
                    $result['msg'] = '参数必须传team_id';
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
    private function de_params($page, $str){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $arr = explode('_', $str);

        switch($page){
            case self::PAGE_INDEX :
                $result['status'] = true;
                break;
            case self::PAGE_GOODS :
                if(count($arr) == 1){
                    $result['data']['goods_id'] = $arr[0];
                    $result['status'] = true;
                }
                break;
            case self::PAGE_PINTUAN :
                if(count($arr) == 3){
                    $result['data']['goods_id'] = $arr[0];
                    $result['data']['group_id'] = $arr[1];
                    $result['data']['team_id'] = $arr[2];
                    $result['status'] = true;
                }
                break;

            default:
                return error_code(10000);
        }
        return $result;
    }
}
