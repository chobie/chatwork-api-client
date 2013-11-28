<?php
namespace Chatwork\Server\Http;

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