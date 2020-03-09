<?php

namespace org\share;

interface BaseShare
{
    function share($client, $page, $userShareCode, $url, $params);
}
