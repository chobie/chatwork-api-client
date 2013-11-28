<?php
namespace Chatwork\Strategy\API;

use Chatwork\Exception\UnauthorizedException;
use Chatwork\Strategy;
use Chatwork\Driver;

class V1
    implements Strategy
{
    protected $params = array();

    /** @var  Driver $driver */
    protected $driver;

    public function __construct($params = array())
    {
        $this->params = array_merge($this->getDefaultParams(), $params);
    }

    public function initiate()
    {
        $driver_class = $this->params['driver'];
        if ($driver_class instanceof Driver) {
            $this->driver = $driver_class;
        } else {
            $this->driver = new $driver_class();
        }
    }

    public function sendMessage($room_id, $message)
    {
        throw UnsupportedFeatureException("not implemeneted yet");
    }

    public function me()
    {
        $res = $this->driver->request('GET', $this->params['endpoint'], '/v1/me', array());
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function getMyStatus()
    {
        $res = $this->driver->request('GET', $this->params['endpoint'], '/v1/my/status', array());
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function getMyTasks($params = array())
    {
        $res = $this->driver->request('GET', $this->params['endpoint'], '/v1/my/status', $params);
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }


    protected function getDefaultParams()
    {
        return array(
            "token"    => null,
            "driver"   => "Chatwork\\Driver\\CurlDriver",
            "endpoint" => 'https://chatwork.com/',
        );
    }
}