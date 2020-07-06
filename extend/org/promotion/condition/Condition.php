<?php

namespace org\promotion\condition;

interface Condition
{
    function jshop($params, &$cart, $promotionInfo);
    function manageCheck($params);
    function getMsg($params);
}
