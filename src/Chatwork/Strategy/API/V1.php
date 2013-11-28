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

    public function getContacts()
    {
        $res = $this->driver->request('GET', $this->params['endpoint'], '/v1/contacts', array());
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function getRooms()
    {
        $res = $this->driver->request('GET', $this->params['endpoint'], '/v1/rooms', array());
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function createRoom($name, $members_admin_ids, $params = array())
    {
        $res = $this->driver->request('POST', $this->params['endpoint'], '/v1/rooms', array(), $params);
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function getRoomById($room_id)
    {
        $res = $this->driver->request('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array());
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function updateRoomInfo($room_id, $params = array())
    {
        $res = $this->driver->request('PUT', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), $params);
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function deleteRoom($room_id)
    {
        $res = $this->driver->request('DELETE', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), array(
            "action_type" => "delete",
        ));
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }

    public function leaveRoom($room_id)
    {
        $res = $this->driver->request('DELETE', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), array(
            "action_type" => "leave",
        ));
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