<?php
namespace Chatwork\Server\Http;

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
class Response
{
    protected $server;
    protected $client;

    protected $code = 200;
    protected $headers = array();
    protected $body = array();
    protected $http_version = "1.0";

    public function __construct($server, $client)
    {
        $this->server = $server;
        $this->client = $client;
    }

    public function writeHead($code, array $headers)
    {
        $this->code = $code;
        $this->headers = $headers;
    }

    public function write($data)
    {
        $this->body[] = $data;
    }

    public function end()
    {
        // Todo: implement correctly
        $buffer = "HTTP/1.0 200 OK\r\n";
        foreach ($this->headers as $key => $value) {
            $buffer .= $key . ": " . $value . "\r\n";
        }
        $buffer .= "\r\n";
        $buffer .= join("", $this->body);

        uv_write($this->client, $buffer, array($this->server, "onWrite"));
    }
}