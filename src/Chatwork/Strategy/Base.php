<?php
namespace Chatwork\Strategy;

use Chatwork\Exception\UnsupportedFeatureException;
use \Chatwork\Strategy;
use \Chatwork\Driver;

abstract class Base
    implements Strategy
{

    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_PUT    = 'PUT';

    const PARAM_ENDPOINT = 'endpoint';
    const PARAM_TOKEN    = "token";
    const PARAM_DRIVER   = "driver";


    public function initiate()
    {
    }

    public function sendMessage($room_id, $message)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function me()
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getMyStatus()
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getMyTasks($params = array())
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getContacts()
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRooms()
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function createRoom($name, $members_admin_ids, $params = array())
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomById($room_id)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function updateRoomInfo($room_id, $params = array())
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function deleteRoom($room_id)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function leaveRoom($room_id)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function updateRoomMembers($room_id, $members_admin_ids = array(), $params = array())
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomMessage($room_id)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomMessageByMessageId($room_id, $message_id)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomTasks($room_id, $params)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function addTask($room_id, $to_ids = array(), $body, $limit = null)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomTaskById($room_id, $task_id)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomFiles($room_id, $params)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

    public function getRoomFileById($room_id, $file_id, $create_download_url)
    {
        throw new UnsupportedFeatureException(sprintf("Strategy class %s does not support %s method yet", __CLASS__, __METHOD__));
    }

}