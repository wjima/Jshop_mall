<?php
namespace org;
use addons\Pintuan\model\PintuanRule;
use app\common\model\Goods;
use app\common\model\Images;
use app\common\model\User;

/**
 * 海报
 * @author keinx
 * Class Poster
 * @package org
 */
class Poster
{
    const FONT_ONE = ROOT_PATH . 'public' . DS . 'static' . DS . 'share' . DS . 'Deng.ttf';


    /**
     * 判断目录创建目录
     * Poster constructor.
     */
    public function __construct()
    {
        if(!is_dir(ROOT_PATH . 'public/static/poster/1/'))
        {
            mkdirs(ROOT_PATH . 'public/static/poster/1/');
        }
        if(!is_dir(ROOT_PATH . 'public/static/poster/2/'))
        {
            mkdirs(ROOT_PATH . 'public/static/poster/2/');
        }
        if(!is_dir(ROOT_PATH . 'public/static/poster/3/'))
        {
            mkdirs(ROOT_PATH . 'public/static/poster/3/');
        }
        if(!is_dir(ROOT_PATH . 'public/static/poster/4/'))
        {
            mkdirs(ROOT_PATH . 'public/static/poster/4/');
        }
        if(!is_dir(ROOT_PATH . 'public/static/qrcode/h5/'))
        {
            mkdirs(ROOT_PATH . 'public/static/qrcode/h5/');
        }
        if(!is_dir(ROOT_PATH . 'public/static/qrcode/wechat/'))
        {
            mkdirs(ROOT_PATH . 'public/static/qrcode/wechat/');
        }
        if(!is_dir(ROOT_PATH . 'public/static/qrcode/toutiao/'))
        {
            mkdirs(ROOT_PATH . 'public/static/qrcode/toutiao/');
        }
    }


    /**
     * 海报生成方法
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function posterGenerate($data)
    {
        $return = [
            'status' => true,
            'msg'    => '生成海报',
            'data'   => ''
        ];

        $user_id        = $data['user_id'];     //用户ID
        $type           = $data['type'];        //分享类型 1=商品海报 2=邀请海报 3=拼团海报 4=店铺分享
        $id             = $data['id'];          //类型值 1商品海报就是商品ID 2邀请海报无需填 3拼团的商品ID
        $group_id       = $data['group_id'];    //拼团的规则ID，拼团海报可用
        $team_id        = $data['team_id'];     //拼团的团ID，拼团海报可用
        $source         = $data['source'];      //来源 1=普通H5页面 2=微信小程序 3=微信公众号H5 4=头条系小程序
        $return_url     = $data['return_url'];  //返回URL地址
        $tt_platform    = $data['tt_platform']; //头条系小程序对应的具体平台
        $path           = ROOT_PATH . 'public/static/poster/' . $type . '/' . $source . '-' . md5($type . '-' . $id . '-' . $return_url . '-' . $user_id) . '.jpg';
        $paths          = '/static/poster/' . $type . '/' . $source . '-' . md5($type . '-' . $id . '-' . $return_url . '-' . $user_id) . '.jpg';
        $return['data'] = request()->domain() . str_replace("\\", "/", $paths);

        //判断来源和类型准备生成的材料
        //判断来源和分享类型和用户ID和返回URL生成所需的二维码
        include_once ROOT_PATH . 'extend/org/phpqrcode.php';
        $qrc_text = '扫描或长按识别二维码';
        $userModel = new User();
        switch($source)
        {
            case 1:
                //普通H5页面 普通二维码
                if($user_id)
                {
                    $qrc_name = md5($return_url . $type . $id . $group_id . $team_id . $user_id);
                }
                else
                {
                    $qrc_name = md5($return_url . $type . $id . $group_id . $team_id);
                }
                $qrc_uri = '../public/static/qrcode/h5/' . $qrc_name . '.png';
                $qrc     = $qrc_uri;
                if($type == 1)
                {
                    //商品
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=2&invite='.$code.'&id='.$id);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=2&id='.$id);
                    }
                }
                else if($type == 2)
                {
                    //邀请
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=3&invite='.$code);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=3');
                    }
                }
                else if($type == 3)
                {
                    //拼团
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        if($team_id)
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&invite='.$code.'&id='.$id.'&team_id='.$team_id);
                        }
                        else
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&invite='.$code.'&id='.$id);
                        }
                    }
                    else
                    {
                        if($team_id)
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&id='.$id.'&team_id='.$team_id);
                        }
                        else
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&id='.$id);
                        }
                    }
                }
                else if($type == 4)
                {
                    //店铺首页
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=9&invite='.$code.'&id='.$id);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=9&id='.$id);
                    }
                }
                else
                {
                    //默认首页
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.urlencode('type=3&invite='.$code);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.urlencode('type=3');
                    }
                }
                QRcode::png($qrc_data, $qrc_uri, 'L', 10, 2);
                break;
            case 2:
                //微信小程序 小程序码
                $qrc_text = '扫描或长按识别小程序码';
                $page = 'pages/share/jump';
                if($type == 1)
                {
                    //商品
                    $code = $userModel->getShareCodeByUserId($user_id);
                    $wx            = new Wx();
                    $wx_appid      = getSetting('wx_appid');
                    $wx_app_secret = getSetting('wx_app_secret');
                    $accessToken   = $wx->getAccessToken($wx_appid, $wx_app_secret);
                    if($accessToken)
                    {
                        $style['width'] = 300;
                        $wxImg = $wx->getParameterQRCode($accessToken, $page, $code, $type, $id, $group_id, $team_id, $style, $wx_appid);
                        if($wxImg['status'])
                        {
                            $qrc = $wxImg['data'];
                        }
                        else
                        {
                            return $wxImg;
                        }
                    }
                    else
                    {
                        return $return = [
                            'status' => false,
                            'msg'    => '后台小程序配置的APPID和APPSECRET错误，无法生成海报',
                            'data'   => ''
                        ];
                    }
                }
                else if($type == 2)
                {
                    //邀请
                    $code          = $userModel->getShareCodeByUserId($user_id);
                    $wx            = new Wx();
                    $wx_appid      = getSetting('wx_appid');
                    $wx_app_secret = getSetting('wx_app_secret');
                    $accessToken   = $wx->getAccessToken($wx_appid, $wx_app_secret);
                    if($accessToken)
                    {
                        $style['width'] = 500;
                        $wxImg = $wx->getParameterQRCode($accessToken, $page, $code, $type, $id, $group_id, $team_id, $style, $wx_appid);
                        if ($wxImg['status'])
                        {
                            $qrc = $wxImg['data'];
                        }
                        else
                        {
                            return $wxImg;
                        }
                    }
                    else
                    {
                        return $return = [
                            'status' => false,
                            'msg'    => '后台小程序配置的APPID和APPSECRET错误，无法生成海报',
                            'data'   => ''
                        ];
                    }
                }
                else if($type == 3)
                {
                    //拼团
                    $code = $userModel->getShareCodeByUserId($user_id);
                    //商品
                    $wx            = new Wx();
                    $wx_appid      = getSetting('wx_appid');
                    $wx_app_secret = getSetting('wx_app_secret');
                    $accessToken   = $wx->getAccessToken($wx_appid, $wx_app_secret);
                    if($accessToken)
                    {
                        $style['width'] = 300;
                        $wxImg = $wx->getParameterQRCode($accessToken, $return_url, $code, $type, $id, $group_id, $team_id, $style, $wx_appid);
                        if($wxImg['status'])
                        {
                            $qrc = $wxImg['data'];
                        }
                        else
                        {
                            return $wxImg;
                        }
                    }
                    else
                    {
                        return $return = [
                            'status' => false,
                            'msg'    => '后台小程序配置的APPID和APPSECRET错误，无法生成海报',
                            'data'   => ''
                        ];
                    }
                }
                else if($type == 4)
                {
                    //店铺首页
                    $code          = $userModel->getShareCodeByUserId($user_id);
                    $wx            = new Wx();
                    $wx_appid      = getSetting('wx_appid');
                    $wx_app_secret = getSetting('wx_app_secret');
                    $accessToken   = $wx->getAccessToken($wx_appid, $wx_app_secret);
                    if($accessToken)
                    {
                        $style['width'] = 500;
                        $wxImg = $wx->getParameterQRCode($accessToken, $page, $code, $type, $id, $group_id, $team_id, $style, $wx_appid);
                        if ($wxImg['status'])
                        {
                            $qrc = $wxImg['data'];
                        }
                        else
                        {
                            return $wxImg;
                        }
                    }
                    else
                    {
                        return $return = [
                            'status' => false,
                            'msg'    => '后台小程序配置的APPID和APPSECRET错误，无法生成海报',
                            'data'   => ''
                        ];
                    }
                }
                else
                {
                    //邀请
                    $code          = $userModel->getShareCodeByUserId($user_id);
                    $wx            = new Wx();
                    $wx_appid      = getSetting('wx_appid');
                    $wx_app_secret = getSetting('wx_app_secret');
                    $accessToken   = $wx->getAccessToken($wx_appid, $wx_app_secret);
                    if($accessToken)
                    {
                        $style['width'] = 500;
                        $wxImg = $wx->getParameterQRCode($accessToken, $page, $code, $type, $id, $group_id, $team_id, $style, $wx_appid);
                        if ($wxImg['status'])
                        {
                            $qrc = $wxImg['data'];
                        }
                        else
                        {
                            return $wxImg;
                        }
                    }
                    else
                    {
                        return $return = [
                            'status' => false,
                            'msg'    => '后台小程序配置的APPID和APPSECRET错误，无法生成海报',
                            'data'   => ''
                        ];
                    }
                }
                break;
            case 4:
                //头条系小程序
                $tt = new Tt();
                $accessToken = $tt->getAccessToken();
                if(!$accessToken['status']){
                    return $accessToken;
                }
                $width = 300;
                $set_icon = true;
                $code = $userModel->getShareCodeByUserId($user_id);

                $qrc_text = '扫描或长按识别小程序码';
                $page = 'pages/share/jump';

                if($type == 1)
                {
                    //商品详情页
                    if($code)
                    {
                        $parameter = share_parameter_encode('type=2&invite='.$code.'&id='.$id);
                    }
                    else
                    {
                        $parameter = share_parameter_encode('type=2&id='.$id);
                    }
                }
                else if($type == 2)
                {
                    //首页
                    if($code)
                    {
                        $parameter = share_parameter_encode('type=3&invite='.$code);
                    }
                    else
                    {
                        $parameter = share_parameter_encode('type=3');
                    }
                }
                else if($type == 3)
                {
                    //拼团
                    if($code)
                    {
                        if($team_id)
                        {
                            $parameter = share_parameter_encode('type=5&invite='.$code.'&id='.$id.'&team_id='.$team_id);
                        }
                        else
                        {
                            $parameter = share_parameter_encode('type=5&invite='.$code.'&id='.$id);
                        }
                    }
                    else
                    {
                        if($team_id)
                        {
                            $parameter = share_parameter_encode('type=5&id='.$id.'&team_id='.$team_id);
                        }
                        else
                        {
                            $parameter = share_parameter_encode('type=5&id='.$id);
                        }
                    }
                }
                else if($type == 4)
                {
                    //店铺首页
                    if($code)
                    {
                        $parameter = share_parameter_encode('type=9&invite='.$code.'&id='.$id);
                    }
                    else
                    {
                        $parameter = share_parameter_encode('type=9&id='.$id);
                    }
                }
                else
                {
                    //默认首页
                    if($code)
                    {
                        $parameter = share_parameter_encode('type=3&invite='.$code);
                    }
                    else
                    {
                        $parameter = share_parameter_encode('type=3');
                    }
                }

                $wxImg = $tt->getParameterQRCode($accessToken['data'], $tt_platform, $page, $parameter, $width, $set_icon);
                if($wxImg['status'])
                {
                    $qrc = $wxImg['data'];
                }
                else
                {
                    return $wxImg;
                }
                break;
            default:
                //其他全部生成普通二维码
                if($user_id)
                {
                    $qrc_name = md5($return_url . $type . $id . $group_id . $team_id . $user_id);
                }
                else
                {
                    $qrc_name = md5($return_url . $type . $id . $group_id . $team_id);
                }
                $qrc_uri = '../public/static/qrcode/h5/' . $qrc_name . '.png';
                $qrc     = $qrc_uri;
                if($type == 1)
                {
                    //商品
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=2&invite='.$code.'&id='.$id);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=2&id='.$id);
                    }
                }
                else if($type == 2)
                {
                    //邀请
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=3&invite='.$code);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=3');
                    }
                }
                else if($type == 3)
                {
                    //拼团
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        if($team_id)
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&invite='.$code.'&id='.$id.'&team_id='.$team_id);
                        }
                        else
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&invite='.$code.'&id='.$id);
                        }
                    }
                    else
                    {
                        if($team_id)
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&id='.$id.'&team_id='.$team_id);
                        }
                        else
                        {
                            $qrc_data = $return_url.'?scene='.share_parameter_encode('type=5&id='.$id);
                        }
                    }
                }
                else if($type == 4)
                {
                    //店铺首页
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=9&invite='.$code.'&id='.$id);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.share_parameter_encode('type=9&id='.$id);
                    }
                }
                else
                {
                    //默认首页
                    if($user_id)
                    {
                        $code     = $userModel->getShareCodeByUserId($user_id);
                        $qrc_data = $return_url.'?scene='.urlencode('type=3&invite='.$code);
                    }
                    else
                    {
                        $qrc_data = $return_url.'?scene='.urlencode('type=3');
                    }
                }
                QRcode::png($qrc_data, $qrc_uri, 'L', 10, 2);
                break;
        }

        //判断类型得到所需要的背景图和素材图
        if($type == 1)
        {
            //商品海报
            //商品信息查询获取商品图片、商品名称、什么价格
            $goodsModel              = new Goods();
            $goods_info              = $goodsModel->getGoodsDetial($id, 'id,name,image_id,price,spes_desc');
            $new_data['goods_img']   = $goods_info['data']['image_url'];
            $new_data['qrc_img']     = $qrc;
            $new_data['goods_name']  = $goods_info['data']['name'];
            $new_data['goods_price'] = getMoney($goods_info['data']['price']);
            $new_data['qrc_text']    = $qrc_text;

            //开始生成
            $config = $this->goodsPosterConfig($new_data);
            $this->createPoster($config, $path);
        }
        else if($type == 2)
        {
            //邀请海报
            //通过用户ID获取用户头像、昵称
            $code     = $userModel->getShareCodeByUserId($user_id);
            $nickname = $userModel->getUserNickname($user_id);
            $avatar   = $userModel->field('avatar')->where('id', 'eq', $user_id)->find();
            $shopname = getSetting('shop_name');

            $data['avatar_img'] = _sImage($avatar['avatar']);
            $data['qrc_img']    = $qrc;
            $data['nickname']   = $nickname;
            $data['shop_name']  = $shopname;
            $data['share_code'] = $code;
            $data['qrc_text']   = $qrc_text;

            //开始生成
            $config = $this->indexPosterConfig($data);
            $this->createPoster($config, $path);
        }
        else if($type == 3)
        {
            //拼团海报
            //商品信息查询获取商品图片、商品名称、什么价格
            $goodsModel                  = new \app\common\model\PintuanRule();
            $goods_info                  = $goodsModel->getPintuanInfo($id);
            $new_data['goods_img']       = _sImage($goods_info['image_id']);
            $new_data['qrc_img']         = $qrc;
            $new_data['goods_name']      = $goods_info['name'];
            $new_data['goods_price']     = bcsub(floatval($goods_info['price']),floatval($goods_info['discount_amount']),2);
            $new_data['goods_price']     = $new_data['goods_price']>0?$new_data['goods_price']:0;
            $new_data['old_goods_price'] = getMoney($goods_info['price']);
            $new_data['qrc_text']        = $qrc_text;

            //开始生成
            $config = $this->piecePosterConfig($new_data);
            $this->createPoster($config, $path);
        }
        else if($type == 4)//分销中心，店铺
        {
            $code     = $userModel->getShareCodeByUserId($user_id);
            $nickname = $userModel->getUserNickname($user_id);
            $avatar   = $userModel->field('avatar')->where('id', 'eq', $user_id)->find();
            $shopname = '';
            if(get_addons_status('DistributionCenter')){
                $distribution = db('distribution')->field('id,store_name')->where('user_id',$user_id)->find();
                $shopname = $distribution['store_name'];
            }
            if(!$shopname){
                $shopname = getSetting('shop_name');
            }
            $data['avatar_img'] = _sImage($avatar['avatar']);
            $data['qrc_img']    = $qrc;
            $data['nickname']   = $nickname;
            $data['shop_name']  = $shopname;
            $data['share_code'] = $code;
            $data['qrc_text']   = $qrc_text;

            //开始生成
            $config = $this->indexPosterConfig($data);
            $this->createPoster($config, $path);
        }

        //保存图片到图片空间
        $imageModel     = new Images();
        $image          = $imageModel->saveImage($return['data'], true);
        $return['data'] = $image['data']['url'];
        return $return;
    }


    /**
     * 商品海报生成需要的配置
     * @param $data
     * @return array
     */
    public function goodsPosterConfig($data)
    {
        $goods_config = [
            'image'      => [
                [
                    'url'     => $data['goods_img'],
                    'left'    => 0,
                    'top'     => 0,
                    'stream'  => 0,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 560,
                    'height'  => 560,
                    'opacity' => 100
                ],
                [
                    'url'     => $data['qrc_img'],
                    'left'    => -20,
                    'top'     => 575,
                    'stream'  => 0,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 150,
                    'height'  => 150,
                    'opacity' => 100
                ]
            ],
            'text'       => [
                [
                    'text'       => $data['goods_name'],
                    'left'       => 20,
                    'top'        => 580,
                    'width'      => 350,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 20,
                    'fontColor'  => '0,0,0',
                    'angle'      => 0,
                    'lineHeight' => 36,
                    'length'     => 25,
                ],
                [
                    'text'       => '￥' . $data['goods_price'],
                    'left'       => 20,
                    'top'        => 680,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 30,
                    'fontColor'  => '255,0,0',
                    'angle'      => 0,
                    'width'      => 340,
                    'lineHeight' => 36,
                    'length'     => 23,
                ],
                [
                    'text'       => $data['qrc_text'],
                    'left'       => 370,
                    'top'        => 725,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 10,
                    'fontColor'  => '50,50,50',
                    'angle'      => 0,
                    'width'      => 170,
                    'lineHeight' => 20,
                    'length'     => 12,
                    'center'     => true
                ]
            ],
            'background' => '../public/static/share/goods.png',
        ];
        return $goods_config;
    }


    /**
     * 邀请海报生成需要的配置
     * @param $data
     * @return array
     */
    public function indexPosterConfig($data)
    {
        $index_config = [
            'image'      => [
                [
                    'url'     => $data['avatar_img'],
                    'left'    => 50,
                    'top'     => 40,
                    'stream'  => 0,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 100,
                    'height'  => 100,
                    'opacity' => 100
                ],
                [
                    'url'     => $data['qrc_img'],
                    'left'    => 120,
                    'top'     => 215,
                    'stream'  => 0,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 320,
                    'height'  => 320,
                    'opacity' => 100
                ]
            ],
            'text'       => [
                [
                    'text'       => '您的好友【' . $data['nickname'] . '】',
                    'left'       => 170,
                    'top'        => 60,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 18,
                    'fontColor'  => '255,255,255',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 30,
                ],
                [
                    'text'       => '发现了一家好店，邀您查看',
                    'left'       => 170,
                    'top'        => 100,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 16,
                    'fontColor'  => '255,255,255',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 30,
                ],
                [
                    'text'       => $data['shop_name'],
                    'top'        => 555,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 16,
                    'fontColor'  => '0,0,0',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 20,
                    'center'     => true
                ],
                [
                    'text'       => $data['qrc_text'],
                    'top'        => 590,
                    'left'       => 0,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 14,
                    'fontColor'  => '30,30,30',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 20,
                    'center'     => true
                ],
                [
                    'text'       => '进入【' . $data['shop_name'] . '】一起寻好物吧！',
                    'top'        => 620,
                    'left'       => 0,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 14,
                    'fontColor'  => '30,30,30',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 60,
                    'center'     => true
                ],
                [
                    'text'       => $data['share_code'],
                    'top'        => 715,
                    'left'       => 0,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 32,
                    'fontColor'  => '255,0,0',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 30,
                    'center'     => true
                ],
                [
                    'text'       => '我的专属邀请码',
                    'top'        => 760,
                    'left'       => 0,
                    'width'      => 400,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 16,
                    'fontColor'  => '0,0,0',
                    'angle'      => 0,
                    'lineHeight' => 20,
                    'length'     => 30,
                    'center'     => true
                ],
            ],
            'background' => '../public/static/share/index.png',
        ];
        return $index_config;
    }


    /**
     * 商品海报生成需要的配置
     * @param $data
     * @return array
     */
    public function piecePosterConfig($data)
    {
        $goods_config = [
            'image'      => [
                [
                    'url'     => $data['goods_img'],
                    'left'    => 0,
                    'top'     => 0,
                    'stream'  => 0,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 560,
                    'height'  => 560,
                    'opacity' => 100
                ],
                [
                    'url'     => $data['qrc_img'],
                    'left'    => -20,
                    'top'     => 575,
                    'stream'  => 0,
                    'right'   => 0,
                    'bottom'  => 0,
                    'width'   => 150,
                    'height'  => 150,
                    'opacity' => 100
                ]
            ],
            'text'       => [
                [
                    'text'       => $data['goods_name'],
                    'left'       => 20,
                    'top'        => 580,
                    'width'      => 350,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 20,
                    'fontColor'  => '0,0,0',
                    'angle'      => 0,
                    'lineHeight' => 36,
                    'length'     => 25,
                ],
                [
                    'text'       => '原价：￥' . $data['old_goods_price'],
                    'left'       => 20,
                    'top'        => 655,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 12,
                    'fontColor'  => '0,0,0',
                    'angle'      => 0,
                    'width'      => 340,
                    'lineHeight' => 36,
                    'length'     => 23,
                ],
                [
                    'text'       => '拼团价：',
                    'left'       => 20,
                    'top'        => 695,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 14,
                    'fontColor'  => '0,0,0',
                    'angle'      => 0,
                    'width'      => 340,
                    'lineHeight' => 36,
                    'length'     => 23,
                ],
                [
                    'text'       => '￥' . $data['goods_price'],
                    'left'       => 85,
                    'top'        => 695,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 30,
                    'fontColor'  => '255,0,0',
                    'angle'      => 0,
                    'width'      => 340,
                    'lineHeight' => 36,
                    'length'     => 23,
                ],
                [
                    'text'       => $data['qrc_text'],
                    'left'       => 370,
                    'top'        => 725,
                    'fontPath'   => self::FONT_ONE,
                    'fontSize'   => 10,
                    'fontColor'  => '50,50,50',
                    'angle'      => 0,
                    'width'      => 170,
                    'lineHeight' => 20,
                    'length'     => 12,
                    'center'     => true
                ]
            ],
            'background' => '../public/static/share/goods.png',
        ];
        return $goods_config;
    }


    /**
     * 生成海报
     * @param $config
     * @param string $filename
     * @return bool|string
     */
    public function createPoster($config, $filename = '')
    {
        if(empty($filename))
        {
            header("content-type: image/png");
        }

        $imageDefault = [
            'left'    => 0,
            'top'     => 0,
            'right'   => 0,
            'bottom'  => 0,
            'width'   => 100,
            'height'  => 100,
            'opacity' => 100
        ];
        $textDefault  = [
            'text'       => '',
            'left'       => 0,
            'top'        => 0,
            'fontSize'   => 24,
            'width'      => 0,
            'lineHeight' => 30,
            'length'     => 100,
            'fontColor'  => '255,255,255',
            'angle'      => 0,
            'center'     => false
        ];
        $background   = $config['background'];

        //获取背景
        $backgroundInfo   = getimagesize($background);
        $backgroundFun    = 'imagecreatefrom' . image_type_to_extension($backgroundInfo[2], false);
        $background       = $backgroundFun($background);
        $backgroundWidth  = imagesx($background);
        $backgroundHeight = imagesy($background);
        $imageRes         = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color            = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);
        // imageColorTransparent($imageRes, $color);  //颜色透明
        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));

        //处理图片
        if(!empty($config['image']))
        {
            foreach($config['image'] as $key => $val)
            {
                $val      = array_merge($imageDefault, $val);
                $info     = getimagesize($val['url']);
                $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
                if($val['stream'])
                {
                    $info     = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res       = $function($val['url']);
                $resWidth  = $info[0];
                $resHeight = $info[1];
                $canvas    = imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) - $val['width'] : $val['left'];
                $val['top']  = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
                imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']);
            }
        }

        //处理文字
        if(!empty($config['text']))
        {
            foreach($config['text'] as $key => $val)
            {
                $val = array_merge($textDefault, $val);
                list($R, $G, $B) = explode(',', $val['fontColor']);
                $val['fontColor'] = imagecolorallocate($imageRes, $R, $G, $B);
                $val['left']      = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) : $val['left'];
                $val['top']       = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
                if($val['length'] != 0)
                {
                    if(mb_strlen($val['text'], 'utf8') > $val['length'])
                    {
                        $val['text'] = mb_substr($val['text'], 0, $val['length'], 'utf8') . '...';
                    }
                }
                $temp_string = '';
                $rows        = 0;
                for($i = 0; $i < mb_strlen($val['text']); $i++)
                {
                    $box            = imagettfbbox($val['fontSize'], $val['angle'], $val['fontPath'], $temp_string);
                    $_string_length = $box[2] - $box[0];
                    $tempText       = mb_substr($val['text'], $i, 1);
                    $temp           = imagettfbbox($val['fontSize'], $val['angle'], $val['fontPath'], $tempText);
                    if($_string_length + $temp[2] - $temp[0] < $val['width'])
                    {
                        $temp_string .= mb_substr($val['text'], $i, 1);
                        if($i == mb_strlen($val['text']) - 1)
                        {
                            $val['top'] += $val['lineHeight'];
                            $rows++;
                            if($val['center'])
                            {
                                $fontBox = imagettfbbox($val['fontSize'], $val['angle'], $val['fontPath'], $temp_string);
                                imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'] + ceil(($backgroundWidth - $val['left'] - $fontBox[2]) / 2), $val['top'], $val['fontColor'], $val['fontPath'], $temp_string);
                            }
                            else
                            {
                                imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $val['fontColor'], $val['fontPath'], $temp_string);
                            }
                        }
                    }
                    else
                    {
                        $texts    = mb_substr($val['text'], $i, 1);
                        $isSymbol = preg_match("/[\\\\pP]/u", $texts) ? true : false;
                        if($isSymbol)
                        {
                            $temp_string .= $texts;
                            $f  = mb_substr($val['text'], $i + 1, 1);
                            $fh = preg_match("/[\\\\pP]/u", $f) ? true : false;
                            if($fh)
                            {
                                $temp_string .= $f;
                                $i++;
                            }
                        }
                        else
                        {
                            $i--;
                        }
                        $tmp_str_len = mb_strlen($temp_string);
                        $s           = mb_substr($temp_string, $tmp_str_len - 1, 1);
                        $symbol      = array("\"", "“", "'", "<", "《",);
                        $symbolRes   = in_array($s, $symbol);
                        if($symbolRes)
                        {
                            $temp_string = rtrim($temp_string, $s);
                            $i--;
                        }
                        $val['top'] += $val['lineHeight'];
                        $rows++;
                        if($val['center'])
                        {
                            $fontBox = imagettfbbox($val['fontSize'], $val['angle'], $val['fontPath'], $temp_string);
                            imagettftext($imageRes, $val['fontSize'], $val['angle'], ceil(($backgroundWidth - $val['width'] - $fontBox[2]) / 2), $val['top'], $val['fontColor'], $val['fontPath'], $temp_string);
                        }
                        else
                        {
                            imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $val['fontColor'], $val['fontPath'], $temp_string);
                        }
                        $temp_string = "";
                    }
                }
            }
        }

        //生成图片
        if(!empty($filename))
        {
            $res = imagejpeg($imageRes, $filename, 95); //保存到本地
            //$res = imagepng($imageRes,$filename,9);
            //$res = imagegif($imageRes,$filename);
            imagedestroy($imageRes);
            if (!$res) return false;
            return $filename;
        }
        else
        {
            imagejpeg($imageRes);     //在浏览器上显示
            imagedestroy($imageRes);
        }
    }


    /**
     * 分享URL生成
     * @param $data
     * @return array
     */
    public function urlGenerate($data)
    {
        $return = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => ''
        ];

        $user_id        = $data['user_id'];     //用户ID
        $type           = $data['type'];        //分享类型 1=商品海报 2=邀请海报 3=拼团海报 4=店铺分享
        $id             = $data['id'];          //类型值 1商品海报就是商品ID 2邀请海报无需填 3拼团的商品ID
        $team_id        = $data['team_id'];     //拼团的团ID，拼团海报可用
        $return_url     = $data['return_url'];  //返回URL地址

        $userModel = new User();
        //普通H5页面 微信公众号H5
        if($type == 1)
        {
            //商品
            if($user_id)
            {
                $code     = $userModel->getShareCodeByUserId($user_id);
                $url = $return_url.'?scene='.share_parameter_encode('type=2&invite='.$code.'&id='.$id);
            }
            else
            {
                $url = $return_url.'?scene='.share_parameter_encode('type=2&id='.$id);
            }
        }
        else if($type == 2)
        {
            //邀请
            if($user_id)
            {
                $code     = $userModel->getShareCodeByUserId($user_id);
                $url = $return_url.'?scene='.share_parameter_encode('type=3&invite='.$code);
            }
            else
            {
                $url = $return_url.'?scene='.share_parameter_encode('type=3');
            }
        }
        else if($type == 3)
        {
            //拼团
            if($user_id)
            {
                $code     = $userModel->getShareCodeByUserId($user_id);
                if($team_id)
                {
                    $url = $return_url.'?scene='.share_parameter_encode('type=5&invite='.$code.'&id='.$id.'&team_id='.$team_id);
                }
                else
                {
                    $url = $return_url.'?scene='.share_parameter_encode('type=5&invite='.$code.'&id='.$id);
                }
            }
            else
            {
                if($team_id)
                {
                    $url = $return_url.'?scene='.share_parameter_encode('type=5&id='.$id.'&team_id='.$team_id);
                }
                else
                {
                    $url = $return_url.'?scene='.share_parameter_encode('type=5&id='.$id);
                }
            }
        }
        else if($type == 4)
        {
            //店铺首页
            if($user_id)
            {
                $code     = $userModel->getShareCodeByUserId($user_id);
                $url = $return_url.'?scene='.share_parameter_encode('type=9&invite='.$code.'&id='.$id);
            }
            else
            {
                $url = $return_url.'?scene='.share_parameter_encode('type=9&id='.$id);
            }
        }
        else
        {
            //默认首页
            if($user_id)
            {
                $code     = $userModel->getShareCodeByUserId($user_id);
                $url = $return_url.'?scene='.urlencode('type=3&invite='.$code);
            }
            else
            {
                $url = $return_url.'?scene='.urlencode('type=3');
            }
        }

        $return['data'] = $url;
        return $return;
    }
}