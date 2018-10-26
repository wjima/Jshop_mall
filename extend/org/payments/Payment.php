<?php

namespace org\payments;

interface Payment
{
    function pay($paymentInfo);
    function callback();
    function refund($refundInfo,$paymentInfo);
}
