<?php

/**
 * 分销商等级设置模型
 */
namespace addons\DistributionCenter\model;

use app\common\model\UserGrade;
use think\Model;
use think\Validate;


class DistributionGrade extends Model
{

    const DEFAULT_YES = 1;//默认
    const DEFAULT_NO = 2;//不是默认
    const UPGRADE_YES = 1;//自动升级
    const UPGRADE_NO = 2;//不自动升级

    public function addData($data)
    {
        $result = [
            'status' => false,
            'msg'    => '保存失败',
            'data'   => [],
        ];
        //判断是否存在
        $info             = $this->get($data['id']);
        $data['grade_id'] = $data['id'];//当前传过来的id是会员等级id，直接作为当前表的主键
        if ($data['is_default'] == self::DEFAULT_YES) {
            $this->where([['id', 'neq', $data['id']]])->update(['is_default' => self::DEFAULT_NO]);
        }
        if ($info) {
            if ($this->allowField(true)->save($data, ['id' => $info['id']]) !== false) {
                $result['msg']    = '保存成功';
                $result['status'] = true;
                return $result;
            }
        } else {
            if ($this->allowField(true)->save($data) !== false) {
                $result['msg']    = '保存成功';
                $result['status'] = true;
                return $result;
            }
        }
        return $result;
    }

    /**
     * 获取默认等级
     * @return int|mixed
     */
    public function getDefault()
    {
        $data = $this->where([['is_default', '=', self::DEFAULT_YES]])->field('grade_id')->find();
        if ($data) {
            return $data['grade_id'];
        } else {
            return 0;
        }
    }

    /**
     * 返回所有用户等级
     * @return array
     */
    public function getGradeList()
    {
        $list = $this
            ->field('*')
            ->alias('dr')
            ->join(config('database.prefix') . 'user_grade ug', 'ug.id = dr.grade_id')
            ->select();
        if (!$list->isEmpty()) {
            return $list->toArray();
        } else {
            return [];
        }
    }


}
