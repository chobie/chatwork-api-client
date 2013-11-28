<?php
namespace Chatwork\Strategy\API;

use Chatwork\Exception\UnauthorizedException;
use Chatwork\Strategy;
use Chatwork\Driver;

class V1
    extends Strategy\Base
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
        return $this->api('POST', $this->params['endpoint'], sprintf('/v1/rooms/%d/messages', $room_id), array(), array(
            "body" => $message,
        ));
    }

    public function me()
    {
        return $this->api('GET', $this->params['endpoint'], '/v1/me', array());
    }

    public function getMyStatus()
    {
        return $this->api('GET', $this->params['endpoint'], '/v1/my/status', array());
    }

    public function getMyTasks($params = array())
    {
        return $this->api('GET', $this->params['endpoint'], '/v1/my/status', $params);
    }

    public function getContacts()
    {
        return $this->api('GET', $this->params['endpoint'], '/v1/contacts', array());
    }

    public function getRooms()
    {
        return $this->api('GET', $this->params['endpoint'], '/v1/rooms', array());
    }

    public function createRoom($name, $members_admin_ids, $params = array())
    {
        return $this->api('POST', $this->params['endpoint'], '/v1/rooms', array(), $params);
    }

    public function getRoomById($room_id)
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array());
    }

    public function updateRoomInfo($room_id, $params = array())
    {
        return $this->api('PUT', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), $params);
    }

    public function deleteRoom($room_id)
    {
        return $this->api('DELETE', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), array(
            "action_type" => "delete",
        ));
    }

    public function leaveRoom($room_id)
    {
        return $this->api('DELETE', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), array(
            "action_type" => "leave",
        ));
    }

    public function updateRoomMembers($room_id, $members_admin_ids = array(), $params = array())
    {
        $parmas = array_merge(array(
            "members_admin_ids" => $members_admin_ids,
        ), $params);

        return $this->api('PUT', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array(), $params);
    }

    public function getRoomMessage($room_id)
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d', $room_id), array());
    }

    public function getRoomMessageByMessageId($room_id, $message_id)
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d/messages/%d', $room_id, $message_id), array());
    }

    public function getRoomTasks($room_id, $params = array())
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d/tasks', $room_id), $params);
    }

    public function getRoomTaskById($room_id, $task_id)
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d/tasks/%d', $room_id, $task_id), array());
    }

    public function getRoomFiles($room_id, $params = array())
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d/files', $room_id), $params);
    }

    public function getRoomFileById($room_id, $file_id, $create_download_url = false)
    {
        return $this->api('GET', $this->params['endpoint'], sprintf('/v1/rooms/%d/files/%d', $room_id, $file_id), array(
            "create_download_url" => ($create_download_url) ? "true" : "false"
        ));
    }

    public function addTask($room_id, $to_ids = array(), $body, $limit = null)
    {
        $params = array(
            "to_ids" => $to_ids,
            "body" => $body,
            "limit" => $limit,
        );

        return $this->api('POST', $this->params['endpoint'], sprintf('/v1/rooms/%d/tasks', $room_id), array(), $params);
    }

    protected function api($http_method = "GET", $endpoint, $query, $params, $post_field = array())
    {
        $res = $this->driver->request($http_method, $this->params['endpoint'], $query, $params, $post_field);
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
            "endpoint" => 'https://api.chatwork.com/',
        );
    }
}