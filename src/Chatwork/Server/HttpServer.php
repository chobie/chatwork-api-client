<?php
namespace Chatwork\Server;

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
