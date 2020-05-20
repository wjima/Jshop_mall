<?php

namespace org\promotion\result;

interface Result
{
    function jshop($params, &$cart, $promotionInfo);
    function manageCheck($params);
    function getMsg($params);
}
