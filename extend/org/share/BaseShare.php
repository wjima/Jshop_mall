<?php
namespace org\share;

/**
 * Interface BaseShare
 * @package org\share
 */
interface BaseShare
{
    function share($client, $page, $userShareCode, $url, $params);
}
