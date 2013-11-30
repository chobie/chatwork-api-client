<?php
namespace Chatwork\Strategy;

use Chatwork\Authentication;
use \Chatwork\Exception\UnsupportedFeatureException;
use \Chatwork\Strategy;
use \Chatwork\Driver;

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
abstract class BaseStrategy
    implements Strategy
{

    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_PUT    = 'PUT';

    const PARAM_ENDPOINT     = 'endpoint';
    const PARAM_TOKEN        = "token";
    const PARAM_DRIVER       = "driver";
    const PARAM_AUTHENTICATE = 'authenticate';

    public function initiate()
    {
    }

    public function setAuthentication(Authentication $authentication)
    {
        $this->params['authentication'] = $authentication;
    }

    public function setDriver(Driver $driver)
    {
        $this->params['driver'] = $driver;
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