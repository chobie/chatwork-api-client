<?php
namespace Chatwork;

interface Driver
{
    public function request($http_method = "GET", $endpoint, $query, $params, $post_field = array());

}