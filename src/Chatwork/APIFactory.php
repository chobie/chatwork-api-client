<?php
namespace Chatwork;

use \Chatwork\API\Client;

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
class APIFactory
{
    public static function createInstance($config = array())
    {
        $default_config = array(
            "endpoint"       => "https://www.chatwork.com/",
            "authentication" => "\\Chatwork\\Authentication\\HeaderAuthentication",
            "strategy"       => "\\Chatwork\\Strategy\\API\\V1Strategy",
            "token"          => "",
            "login"          => "",
            "password"       => "",
            "proxy"          => getenv("HTTP_PROXY"),
            "driver"         => "Chatwork\\Driver\\CurlDriver",
            "driver_option" => array(
            )
        );

        $config = array_merge($default_config, $config);
        if ($config['driver'] == "Chatwork\\Driver\\CurlDriver") {
            if (!extension_loaded("curl")) {
                throw new \RuntimeException("your php does not support curl. please rebuild php");
            }
            if (!extension_loaded("openssl")) {
                throw new \RuntimeException("curl requires openssl extension. please rebuild php");
            }
        }

        if (!empty($config['proxy'])) {
            $info = parse_url($config['proxy']);
            switch($info['scheme']) {
            case "http":
                $proto = 'tcp';
                break;
            case 'https':
                $proto = "ssl";
                break;
            default:
                $proto = 'tcp';
            }

            $config['proxy'] = sprintf("%s://%s:%d", $proto, $info['host'], $info['port']);
        }

        $api = new \Chatwork\Api\Client();
        $authentication_class = $config['authentication'];
        if (is_string($authentication_class)) {
            $authentication = new $authentication_class($api);

            if ($authentication instanceof \Chatwork\Authentication\HeaderAuthentication) {
                $authentication->setToken($config['api_key']);
            } else if ($authentication instanceof \Chatwork\Authentication\HeadlessAuthentication) {
                $authentication->setLogin($config['login']);
                $authentication->setPassword($config['password']);
            }
        } else {
            $authentication = $authentication_class;
        }

        $driver_class = $config['driver'];
        $driver = new $driver_class();

        $strategy_class = $config['strategy'];
        $strategy = new $strategy_class();
        $strategy->setAuthentication($authentication);
        $strategy->setDriver($driver);

        $api->setStrategy($strategy);

        return $api;
    }
}