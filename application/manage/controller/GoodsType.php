<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;
use app\common\model\GoodsType as typeModel;
use app\common\model\GoodsTypeSpec;
use app\common\model\GoodsTypeSpecValue;
use app\common\model\GoodsTypeSpecRel;
use app\common\model\GoodsParams;
use app\common\model\GoodsTypeParams;
use think\Db;

/**
 * 商品类型
 * Class GoodsType
 * @package app\Manage\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:07
 */
class GoodsType extends Manage
{
    /**
     * 商品类型列表
     * @return mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $typeModel = new typeModel();
            $filter = input('request.');
            return $typeModel->tableData($filter);
        }
        return $this->fetch('index');
    }


    /**
     * 添加类型
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-09 20:47
     */
    public function add()
    {
        $return = [
            'status' => false,
            'msg' => error_code(10037, true),
            'data' => ''
        ];
        $this->view->engine->layout(false);
        if (Request::isPost()) {
            $return_data = [
                'status' => false,
                'msg' => error_code(10019, true),
                'data' => '',
                'token' => \think\facade\Request::token('__Jshop_Token__', 'sha1')
            ];
            //存储添加内容
            $typeData    = [
                'name' => input('post.name'),
            ];
            $params_name = input('post.params_name/a', []);
            $paramsData = [];
            foreach ($params_name as $key => $val) {
                if ($val) {
                    $paramsData[$key]['id'] = input('post.params_id.' . $key);
                    $paramsData[$key]['name'] = input('post.params_name.' . $key);
                    $paramsData[$key]['type'] = input('post.params_type.' . $key);
                    $paramsData[$key]['value'] = input('post.params_value.' . $key);
                }
            }
            //属性值
            $typeSpecData = [];
            $type_name = input('post.type_name/a', []);
            foreach ($type_name as $key => $val) {
                if ($val) {
                    $typeSpecData[$key]['id'] = input('post.type_id.' . $key);
                    $typeSpecData[$key]['name'] = input('post.type_name.' . $key);
                    $typeSpecData[$key]['sort'] = input('post.type_sort.' . $key);
                    $typeSpecData[$key]['value'] = explode(' ', input('post.type_value.' . $key));
                }
            }
            $goodsType = new typeModel();
            Db::startTrans();
            $result = $goodsType->add($typeData);
            $type_id = $goodsType->getLastInsID();
            if ($result !== false) {
                $goodsTypeParamsModel = new GoodsTypeParams();
                $typeParamsRel        = [];
                if ($paramsData) {
                    foreach ($paramsData as $key => $val) {
                        $goodsParamsModel = new GoodsParams();
                        if ($val['id']) {
                            $goodsParamsModel->save($val, ['id' => $val['id']]);
                        } else {
                            unset($val['id']);
                            $res       = $goodsParamsModel->save($val);
                            $val['id'] = $goodsParamsModel->getLastInsID();
                        }
                        $typeParamsRel[$key]['params_id'] = $val['id'];
                        $typeParamsRel[$key]['type_id']   = $type_id;
                    }
                    $goodsTypeParamsModel->saveAll($typeParamsRel);
                }
                //保存属性
                $goodsTypeSpecValue = new GoodsTypeSpecValue();
                $goodsTypeSpecRel   = new GoodsTypeSpecRel();
                $typeSpecRel        = [];
                if ($typeSpecData) {
                    foreach ($typeSpecData as $key => $value) {
                        $goodsTypeSpec = new GoodsTypeSpec();

                        if ($value['id']) {
                            $goodsTypeSpec->save($value, ['id' => $value['id']]);
                            $goodsTypeSpecValue->where([['spec_id', '=', $value['id']]])->delete();
                            $specValue     = $value['value'];
                            $tempSpecValue = [];
                            foreach ($specValue as $sk => $sv) {
                                $tempSpecValue[] = [
                                    'spec_id' => $value['id'],
                                    'value'   => $sv,
                                ];
                            }
                            $goodsTypeSpecValue->saveAll($tempSpecValue);
                        } else {
                            unset($value['id']);
                            $res           = $goodsTypeSpec->save($value);
                            $value['id']   = $goodsTypeSpec->getLastInsID();
                            $specValue     = $value['value'];
                            $tempSpecValue = [];
                            foreach ($specValue as $sk => $sv) {
                                $tempSpecValue[] = [
                                    'spec_id' => $value['id'],
                                    'value'   => $sv,
                                ];
                            }
                            $goodsTypeSpecValue->saveAll($tempSpecValue);
                        }
                        $typeSpecRel[$key]['spec_id'] = $value['id'];
                        $typeSpecRel[$key]['type_id'] = $type_id;
                    }
                    $goodsTypeSpecRel->saveAll($typeSpecRel);
                }
                Db::commit();
                $return_data = [
                    'status' => true,
                    'msg'    => '添加成功',
                    'data'   => $result,
                    'token'  => \think\facade\Request::token('__Jshop_Token__', 'sha1')
                ];
            } else {
                Db::rollback();
            }
            return $return_data;
        }
        //获取添加页面
        $return['status'] = true;
        $return['msg'] = '成功';
        $return['data'] = $this->fetch('add');
        return $return;
    }


    /**
     * 商品类型关联属性
     * @return array
     */
    public function addRel()
    {
        $return = [
            'status' => false,
            'msg' => error_code(10037, true),
            'data' => ''
        ];
        $this->view->engine->layout(false);
        if (!Request::isPost()) {
            $id = input('get.id/d');
            if (!$id) {
                $this->error(error_code(10051, true)); //todo 统一错误页面
            }
            $typeModel = new typeModel();
            $spec = $typeModel::get($id);
            $this->assign('spec', $spec);
            $typeSpecModel = new GoodsTypeSpec();
            $specList = $typeSpecModel->getAllSpec();
            $this->assign('specList', $specList);
            //获取已关联属性
            $typeSpecRelModel = new GoodsTypeSpecRel();
            $typeSpec = $typeSpecRelModel->getRelTypeSpec($id);
            $this->assign('typeSpec', $typeSpec);
            $typeSids = [];
            if ($typeSpec) {
                $typeSids = array_column($typeSpec, 'spec_id');
            }
            $this->assign('typeSids', $typeSids);
            $return['status'] = true;
            $return['msg'] = '成功';
            $return['data'] = $this->fetch('addRel');
            return $return;
        } else {
            $return_data = [
                'status' => false,
                'msg' => error_code(10004, true),
                'data' => '',
            ];
            //存储添加内容
            $data = array(
                'type_id' => input('post.type_id/d'),
                'spec_id' => input('post.spec_id/a'),
            );
            $typeSpecRelModel = new GoodsTypeSpecRel();

            $result = $typeSpecRelModel->updateTypeSpec($data['type_id'], $data['spec_id']);
            if ($result !== false) {
                $return_data = [
                    'status' => true,
                    'msg' => '保存成功',
                    'data' => $result,
                ];
            }
            return $return_data;
        }
    }


    /**
     * 编辑类型
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-31 9:48
     * @return array
     */
    public function edit()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10037, true),
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $id = input('get.id/d');
        $goodsTypeModel = new typeModel();
        $typeSpec = $goodsTypeModel::get($id);
        $this->assign('typeSpec', $typeSpec);
        if (Request::isPost()) {
            $goodsTypeModel = new typeModel();
            $data = [
                'id' => input('post.id', 0),
                'name' => input('post.name', ''),
            ];
            $goodsTypeModel::update($data, ['id' => $data['id']]);
            $result = [
                'status' => true,
                'msg' => '保存成功',
                'data' => '',
            ];
            return $result;
        }
        $result['status'] = true;
        $result['msg'] = '成功';
        $result['data'] = $this->fetch('edit');
        return $result;
    }


    /**
     * 删除类型
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-31 9:48
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10023, true),
            'data' => '',
        ];
        $id = input('post.id', 0);
        if ($id) {
            $goodsTypeModel = new typeModel();
            $goodsTypeModel->startTrans();
            if ($goodsTypeModel->where(['id' => $id])->delete()) {
                $typeSpecRelModel = new GoodsTypeSpecRel();
                if (!$typeSpecRelModel::get(['type_id' => $id])) {
                    $goodsTypeModel->commit();
                    $result['status'] = true;
                    $result['msg'] = '删除成功';
                    return $result;
                }
                if ($typeSpecRelModel->where(['type_id' => $id])->delete()) {
                    $result['status'] = true;
                    $result['msg'] = '删除成功';
                    $goodsTypeModel->commit();
                } else {
                    $goodsTypeModel->rollback();
                }
            }
        }
        return $result;
    }


    /**
     * 获取和设置参数
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addParams()
    {
        $return = error_code(10037);
        $this->view->engine->layout(false);
        if (!Request::isPost()) {
            $id = input('id/d');
            if (!$id) {
                return error_code(10051, true);
            }
            $typeModel = new typeModel();

            $specname = $typeModel->getNameById($id);
            $this->assign('spec_name', $specname);
            $this->assign('spec_id', $id);

            $goodsParamsModel = new GoodsParams();
            $params = $goodsParamsModel->getAllParams();
            $this->assign('params', $params);

            //获取已绑定参数
            $goodsTypeParamsModel = new GoodsTypeParams();
            $typeParams = $goodsTypeParamsModel->getRelParams($id);
            $this->assign('typeParams', $typeParams);

            $typePids = [];
            if ($typeParams) {
                $typePids = array_column($typeParams, 'params_id');
            }
            $this->assign('typePids', $typePids);

            $return['status'] = true;
            $return['msg'] = '成功';
            $return['data'] = $this->fetch('addParams');
            return $return;
        } else {
            //存储添加内容
            $data = array(
                'type_id'   => input('post.type_id/d'),
                'params_id' => input('post.params_id/a'),
            );

            $goodsTypeParamsModel = new GoodsTypeParams();
            $result               = $goodsTypeParamsModel->updateTypeParams($data['type_id'], $data['params_id']);

            if ($result !== false) {
                $return = [
                    'status' => true,
                    'msg' => '保存成功',
                    'data' => $result,
                ];
            }
            return $return;
        }
    }


    /**
     * 获取所有类型
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAll()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10025, true),
            'data' => [],
        ];
        $typeModel = new typeModel();
        $typeList = $typeModel->field('id,name')->where([])->select();
        if (!$typeList->isEmpty()) {
            $result['data'] = $typeList->toArray();
            $result['status'] = true;
            $result['msg'] = '获取成功';
        }
        return $result;
    }
}
