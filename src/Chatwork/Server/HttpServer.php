<?php
namespace Chatwork\Server;

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
class HttpServer
{
    protected $server;

    protected $clients = array();
    protected $parsers = array();
    protected $closure;

    public function __construct()
    {
        $this->server = uv_tcp_init();
    }

    public function addListener($closure)
    {
        $this->closure = $closure;
    }

    public function onShutdown($handle, $status)
    {
        uv_close($handle, array($this, "onClose"));
    }

    public function onClose($handle)
    {
        unset($this->clients[(int)$handle]);
        unset($this->parsers[(int)$handle]);

    }

    public function onWrite($client, $status)
    {
        if ($status == 0) {
            uv_shutdown($client, array($this, "onShutdown"));
        } else {
            echo "[write_failed]";
        }

    }

    public function onRead($client, $nread, $buffer)
    {
        //echo $buffer;
        //echo "--Error: " . uv_err_name(uv_last_error(uv_default_loop())) . PHP_EOL;

        if ($nread < 0) {
            //echo "[NREAD={$nread}]\n";
            uv_shutdown($client, array($this, "onShutdown"));
        } else if ($nread == 0) {
            // nothing to do.
            //echo "[NREAD=0]\n";
        } else {
            $result = array();

            if (uv_http_parser_execute($this->parsers[(int)($client)], $buffer, $result)){
                $response = new HttpResponse($this, $client);

                $closure = $this->closure;
                $closure($result, $response, $client);
            } else {
                // nothing to do. (waiting next buffer)
            }
        }
    }

    public function onConnect($server, $status)
    {
        $client = uv_tcp_init();
        uv_tcp_nodelay($client, 1);
        uv_accept($server,$client);

        $this->clients[(int)$client]   = $client;
        $this->parsers[(int)($client)] = uv_http_parser_init();

        uv_read_start($client, array($this, "onRead"));
    }

    public function listen($port)
    {
        printf("# server listend at $port\n");

        uv_tcp_nodelay($this->server, 1);
        uv_tcp_bind($this->server, uv_ip4_addr("0.0.0.0", $port));
        uv_listen($this->server, 511, array($this, "onConnect"));

        uv_run(uv_default_loop());
    }
}
