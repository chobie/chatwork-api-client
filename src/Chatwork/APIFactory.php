<?php
namespace Chatwork;

use \Chatwork\API\Client;
use \Chatwork\Authentication\HeaderAuthentication;
use \Chatwork\Authentication\HeadlessAuthentication;

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
            "driver_option"  => array(
            ),
            "plugins"        => array(),
        );

        $config = array_merge($default_config, $config);
        if ($config['driver'] == "Chatwork\\Driver\\CurlDriver") {
            if (!extension_loaded("curl")) {
                throw new \RuntimeException("your php does not support curl. please rebuild php");
            }
            if (!extension_loaded("openssl")) {
                throw new \RuntimeException("curl requires openssl extension. please rebuild php");
            }
        } else if ($config['driver'] == "Chatwork\\Driver\\StreamSocketDriver") {
            if (!in_array("ssl", stream_get_transports())) {
                throw new \RuntimeException("stream socket must support ssl transport. please rebuild php");
            }
            if (ini_get("allow_url_fopen") == false) {
                throw new \RuntimeException("stream socket requires `allow_url_fopen`. please check your php.ini");
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

        $api = new Client();
        $authentication_class = $config['authentication'];
        if (is_string($authentication_class)) {
            $authentication = new $authentication_class($api);

            if ($authentication instanceof HeaderAuthentication) {
                $authentication->setToken($config['token']);
            } else if ($authentication instanceof HeadlessAuthentication) {
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
        /** @var \Chatwork\Strategy $strategy */

        $strategy->setAuthentication($authentication);
        $strategy->setDriver($driver);

        $api->setStrategy($strategy);

        foreach ($config['plugins'] as $plugin) {
            if (is_string($plugin)) {
                $api->registerPlugins(array(new $plugin()));
            } else {
                $api->registerPlugins(array($plugin));
            }
        }

        return $api;
    }
}