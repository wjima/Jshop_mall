<?php
namespace org;

class Demoqrcode{
    function demo($url=''){
        include_once 'phpqrcode.php';
        $filename = 'static/images/'.time().'.png';
        QRcode::png($url,$filename , 'L', 10, 2);
    }
}