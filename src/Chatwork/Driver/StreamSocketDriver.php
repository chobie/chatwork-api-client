<?php
namespace Chatwork\Driver;

use \Chatwork\API\Request;
use \Chatwork\Authentication\HeaderAuthentication;
use \Chatwork\Authentication\NothingAuthentication;
use \Chatwork\Driver;
use \Chatwork\Strategy\HeadlessStrategy;

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
class StreamSocketDriver
    implements Driver
{
    protected $context;

    public function __construct($context_config = array())
    {
        if ($context_config) {
            $this->context = stream_context_create($context_config);
        } else {
            $this->context = stream_context_create();
        }
    }

    public function request(Request $request)
    {
        $context = stream_context_create(array(
            'http' => array(
                'method'          => $request->getRequestMethod(),
                'header'          => $request->getHeadersAsString(),
                'proxy'           => $request->getProxy(),
                'content'         => $request->getContentBody(),
                'request_fulluri' => $request->hasProxy(),
                'ignore_errors'   => true,
            ),
        ));

        /* Note: stream_socket_client does not support http(s) protocol. we have to use fopen here. */
        $socket  = fopen($request->getUrl(), 'r', false, $context);
        if (!is_resource($socket)) {
            throw new \Exception("can't create stream socket.");
        }

        $headers      = array();
        $meta_data    = stream_get_meta_data($socket);
        if (isset($meta_data['wrapper_data'])) {
            foreach ($meta_data['wrapper_data'] as $value) {
                if (strpos($value, "HTTP/") === 0) {
                    list($dummy, $status, $dummy) = explode(" ", $value, 3);
                    $headers['HTTP_CODE'] = $status;
                } else {
                    list($key, $value) = explode(":", $value, 2);
                    $headers[$key] = $value;
                }
            }
        }

        if ($headers['HTTP_CODE'][0] != 2) {
            throw new \Exception(sprintf("API Server returns %s code: %s", $headers['HTTP_STATUS'], fread($socket, 8192)));
        }

        $data = stream_get_contents($socket);
        fclose($socket);

        return array(
            $headers,
            $data,
        );
    }
}