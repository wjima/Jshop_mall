<?php

namespace org;

class Payment
{
    static function create($code, $config){
        $code = '\\org\\payments\\'.$code;
        return new $code($config);
    }

}
