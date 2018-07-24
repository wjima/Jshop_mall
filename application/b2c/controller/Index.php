<?php
namespace app\b2c\controller;

use app\common\controller\Base;


class Index extends Base
{
    public function index()
    {
        $this->redirect('/wap/index','302');
    }
}
