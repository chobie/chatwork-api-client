<?php
namespace Chatwork\Driver;

use \Chatwork\Driver;

/**
 * Chatwork API Client
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2013, Shuhei Tanuma. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class CurlDriver
    implements Driver
{
    protected $curl;

    public function __construct()
    {
        $ch = curl_init();
        $cookie_file = ".chatwork";

        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch ,CURLOPT_HTTPHEADER, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        $this->curl = $ch;
    }

    public function request($http_method = "GET", $endpoint, $query, $params, $post_field = array())
    {
        $curl = curl_copy_handle($this->curl);
        $url = $endpoint . $query;
        if (!empty($params)) {
            $url .= "?" . http_build_query($params);
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        if ($http_method == "POST") {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($post_field) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_field);
            }
        } else if ($http_method == "GET") {
            curl_setopt($curl, CURLOPT_GET, 1);
        } else if ($http_method == 'PUT') {
            curl_setopt($curl, CURLOPT_PUT, 1);
            if ($post_field) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_field);
            }
        } else if ($http_method == 'DELETE') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            if ($post_field) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_field);
            }
        } else {
            throw new \Exception("unsupported httpd method: " . $http_method);
        }

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        $header = substr($response, 0, $info['header_size']);
        $output = substr($response, $info['header_size']);

        return array(
            $header,
            $output
        );

    }
}