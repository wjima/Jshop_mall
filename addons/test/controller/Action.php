<?php
namespace addons\test\controller;

use myxland\addons\library\AddonController;

class Action extends AddonController
{
    public function link()
    {
        return $this->fetch();
        //echo 'hello link';
    }
}