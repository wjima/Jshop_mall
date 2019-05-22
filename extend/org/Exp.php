<?php
// +----------------------------------------------------------------------
// | JuhePHP [ NO ZUO NO DIE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2015 http://juhe.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Juhedata <info@juhe.cn-->
// +----------------------------------------------------------------------

//----------------------------------
// 快递100查询API调用代码
//----------------------------------
namespace org;

class Exp{
    private $key;      // 申请的快递查询KEY

    private $customer;  //  分配给贵司的的公司编号

    private $url = 'http://poll.kuaidi100.com/poll/query.do';

    public function __construct()
    {
        $this->key       = config('jshop.api_express.key');
        $this->customer = config('jshop.api_express.customer');

        if (getSetting('kuaidi100_customer') && getSetting('kuaidi100_key')) {
            $this->customer = getSetting('kuaidi100_customer');
            $this->key       = getSetting('kuaidi100_key');
        }
    }


    /**
     *  组装params
     *
     */
    public function assembleParam ($com, $no)
    {
        $data['customer'] = $this->customer;
        $data['param'] = '{"com":"'.$com.'","num":"'.trim($no).'"}';
        $data['sign'] = $this->toSign($data['param'], $this->key, $data['customer']);
        $vo = '';
        foreach ($data as $k => $v) {
            $vo.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }

        $data = substr($vo, 0, -1);

        return $data;
    }


    /**
     * 执行签名
     *
     */
    private function toSign ($params, $key, $customer) {
        return strtoupper(md5($params.$key.$customer));
    }



    /**
     * curl post请求
     *
     */
    public function postCurl($params){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        $result = curl_exec($ch);
        curl_close( $ch );
        $data = str_replace("\"",'"',$result );
        $data = json_decode($data,true);
        return $data;
    }
}