<?php
namespace Chatwork\API;

use Chatwork\Plugin;
use \Chatwork\Strategy;
use \Chatwork\Exception\UnsupportedFeatureException;

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
class Client
{
    /** @var  Strategy */
    protected $strategy;

    /** @var bool initiated flag */
    protected $initiated = false;

    /** @var array plugins */
    protected $plugins = array();

    public function __construct()
    {
    }

    public function registerPlugins(array $plugins)
    {
        foreach ($plugins as $plugin) {
            $this->plugins[] = $plugin;
        }
    }

    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * send message to specified room id
     *
     * @param $room_id
     * @param $message
     *
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#POST-rooms-room_id-messages
     */
    public function sendMessage($room_id, $message)
    {
        $this->initiateStrategy();

        // TODO: improve this block
        foreach ($this->plugins as $plugin) {
            if ($plugin->getType() == Plugin::PLUGIN_TYPE_SEND_MESSAGE) {
                $plugin->filter($room_id, $message);
            }
        }

        $result = $this->getStrategy()->sendMessage($room_id, $message);

        return $result;
    }

    public function me()
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->me();

        return $result;
    }

    public function getMyStatus()
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getMyStatus();

        return $result;
    }

    public function getMyTasks($params = array())
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getMyTasks($params);

        return $result;
    }

    public function getContacts()
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getContacts();

        return $result;
    }

    public function getRooms()
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getRooms();

        return $result;
    }

    public function createRoom($name, $members_admin_ids, $params = array())
    {
        /**
         * accepted params
         *
         * icon_preset
         * members_member_ids
         * members_readonly_ids
         * description
         */
        $this->initiateStrategy();
        $result = $this->getStrategy()->createRoom($name, $members_admin_ids, $params);

        return $result;
    }

    public function getRoomById($room_id)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getRoomById($room_id);

        return $result;
    }

    public function updateRoomInfo($room_id, $params = array())
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->updateRoomInfo($room_id, $params);

        return $result;
    }

    public function deleteRoom($room_id)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->deleteRoom($room_id);

        return $result;
    }

    public function leaveRoom($room_id)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->leaveRoom($room_id);

        return $result;
    }

    public function updateRoomMembers($room_id, $members_admin_ids, $params = array())
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->updateRoomMembers($params);

        return $result;
    }

    public function getRoomMessage($room_id)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getRoomMessage($room_id);

        return $result;
    }

    public function getRoomMessageByMessageId($room_id, $message_id)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getRoomMessageByMessageId($room_id, $message_id);

        return $result;
    }

    public function getRoomTasks($room_id, $params = array())
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getRoomTasks($room_id, $params);

        return $result;
    }

    public function getRoomTaskById($room_id, $task_id)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->getRoomTaskById($room_id, $task_id);

        return $result;
    }



    public function loadChat()
    {
        throw new UnsupportedFeatureException(sprintf("%s method does not support yet", __METHOD__));
    }

    public function addTask($room_id, $to_ids = array(), $body, $limit = null)
    {
        $this->initiateStrategy();
        $result = $this->getStrategy()->addTask($room_id, $to_ids, $body, $limit);

        return $result;
    }

    public function checkTask()
    {
        throw new UnsupportedFeatureException(sprintf("%s method does not support yet", __METHOD__));
    }

    public function addRoom()
    {
        throw new UnsupportedFeatureException(sprintf("%s method does not support yet", __METHOD__));
    }

    protected function initiateStrategy()
    {
        if (!$this->initiated) {
            $this->strategy->initiate();
        }
    }
}