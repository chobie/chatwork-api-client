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
        $driver_class = $this->params[self::PARAM_DRIVER];
        if ($driver_class instanceof Driver) {
            $this->driver = $driver_class;
        } else {
            $this->driver = new $driver_class();
        }
    }

    /**
     * send a message to specify room id
     *
     * @param string $room_id
     * @param string $message
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#POST-rooms-room_id-messages
     */
    public function sendMessage($room_id, $message)
    {
        return $this->api(self::HTTP_METHOD_POST,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/messages', $room_id),
            array(), array(
                "body" => $message,
        ));
    }

    /**
     * get own information
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_me.html#GET-me
     */
    public function me()
    {
        return $this->api(self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            '/v1/me',
            array()
        );
    }

    /**
     * get own statics information
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_my.html#GET-my-status
     */
    public function getMyStatus()
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            '/v1/my/status',
            array()
        );
    }

    /**
     * get own task information
     *
     * @param array $params accepts `assigned_by_account_id` and `status` option.
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_my.html#GET-my-tasks
     */
    public function getMyTasks($params = array())
    {
        return $this->api(self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            '/v1/my/status',
            $params
        );
    }

    /**
     * get contact list
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_contacts.html#GET-contacts
     */
    public function getContacts()
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            '/v1/contacts',
            array()
        );
    }

    /**
     * get own room list
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms
     */
    public function getRooms()
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            '/v1/rooms',
            array()
        );
    }

    /**
     * create new room
     *
     * @param       $name
     * @param array $members_admin_ids
     * @param array $params
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#POST-rooms
     */
    public function createRoom($name, $members_admin_ids = array(), $params = array())
    {
        return $this->api(
            self::HTTP_METHOD_POST,
            $this->params[self::PARAM_ENDPOINT],
            '/v1/rooms',
            array(),
            $params
        );
    }

    /**
     * get specified room information
     *
     * @param string $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id
     */
    public function getRoomById($room_id)
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d', $room_id),
            array()
        );
    }

    /**
     * update room meta information
     *
     * @param       $room_id
     * @param array $params
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#PUT-rooms-room_id
     */
    public function updateRoomInfo($room_id, $params = array())
    {
        return $this->api(
            self::HTTP_METHOD_PUT,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d', $room_id),
            array(),
            $params
        );
    }

    /**
     * delete room
     *
     * @param $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#DELETE-rooms-room_id
     */
    public function deleteRoom($room_id)
    {
        return $this->api(
            self::HTTP_METHOD_DELETE,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d', $room_id),
            array(),
            array(
                "action_type" => "delete",
            )
        );
    }

    /**
     * leave room
     *
     * @param $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#DELETE-rooms-room_id
     */
    public function leaveRoom($room_id)
    {
        return $this->api(
            self::HTTP_METHOD_DELETE,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d', $room_id),
            array(),
            array(
                "action_type" => "leave",
            )
        );
    }

    /**
     * batch update current room members
     *
     * @param       $room_id
     * @param array $members_admin_ids
     * @param array $params
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#PUT-rooms-room_id-members
     */
    public function updateRoomMembers($room_id, $members_admin_ids = array(), $params = array())
    {
        $parmas = array_merge(array(
            "members_admin_ids" => $members_admin_ids,
        ), $params);

        return $this->api(
            self::HTTP_METHOD_PUT,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d', $room_id),
            array(),
            $params
        );
    }

    /**
     * get room messages
     *
     * @param $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-messages
     */
    public function getRoomMessage($room_id)
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d', $room_id),
            array()
        );
    }

    /**
     * get specified message
     *
     * @param $room_id
     * @param $message_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-messages-message_id
     */
    public function getRoomMessageByMessageId($room_id, $message_id)
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/messages/%d', $room_id, $message_id),
            array()
        );
    }

    /**
     * get room tasks
     *
     * @param       $room_id
     * @param array $params
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-tasks
     */
    public function getRoomTasks($room_id, $params = array())
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/tasks', $room_id),
            $params
        );
    }

    /**
     * get room task
     *
     * @param $room_id
     * @param $task_id
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-tasks-task_id
     */
    public function getRoomTaskById($room_id, $task_id)
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/tasks/%d', $room_id, $task_id),
            array()
        );
    }

    /**
     * get room files
     *
     * @param       $room_id
     * @param array $params
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-files
     */
    public function getRoomFiles($room_id, $params = array())
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/files', $room_id),
            $params
        );
    }

    /**
     * get room file by id
     *
     * @param      $room_id
     * @param      $file_id
     * @param bool $create_download_url
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-files-file_id
     */
    public function getRoomFileById($room_id, $file_id, $create_download_url = false)
    {
        return $this->api(
            self::HTTP_METHOD_GET,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/files/%d', $room_id, $file_id),
            array(
                "create_download_url" => ($create_download_url) ? "true" : "false"
            )
        );
    }

    /**
     * add new task
     *
     * @param       $room_id
     * @param array $to_ids
     * @param       $body
     * @param null  $limit
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#POST-rooms-room_id-tasks
     */
    public function addTask($room_id, $to_ids = array(), $body, $limit = null)
    {
        $params = array(
            "to_ids" => $to_ids,
            "body"   => $body,
            "limit"  => $limit,
        );

        return $this->api(
            self::HTTP_METHOD_POST,
            $this->params[self::PARAM_ENDPOINT],
            sprintf('/v1/rooms/%d/tasks', $room_id),
            array(),
            $params
        );
    }

    protected function api($http_method = "GET", $endpoint, $query, $params, $post_field = array())
    {
        $res = $this->driver->request($http_method, $this->params[self::PARAM_ENDPOINT], $query, $params, $post_field);
        if ($res[0]['HTTP_CODE'] == 401) {
            $response = json_decode($res[1], true);
            throw new UnauthorizedException("errors: " . join(PHP_EOL, $response['errors']));
        }

        return json_decode($res[1], true);
    }


    protected function getDefaultParams()
    {
        return array(
            self::PARAM_TOKEN    => null,
            self::PARAM_DRIVER   => "Chatwork\\Driver\\CurlDriver",
            self::PARAM_ENDPOINT => 'https://api.chatwork.com/',
        );
    }
}