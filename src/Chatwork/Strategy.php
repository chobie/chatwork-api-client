<?php
namespace Chatwork;

use \Chatwork\Exception\UnauthorizedException;

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
interface Strategy
{
    public function initiate();

    /**
     * send a message to specify room id
     *
     * @param string $room_id
     * @param string $message
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#POST-rooms-room_id-messages
     */
    public function sendMessage($room_id, $message);

    /**
     * get own information
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_me.html#GET-me
     */
    public function me();

    /**
     * get own statics information
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_my.html#GET-my-status
     */
    public function getMyStatus();

    /**
     * get own task information
     *
     * @param array $params accepts `assigned_by_account_id` and `status` option.
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_my.html#GET-my-tasks
     */
    public function getMyTasks($params = array());

    /**
     * get contact list
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_contacts.html#GET-contacts
     */
    public function getContacts();

    /**
     * get own room list
     *
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms
     */
    public function getRooms();

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
    public function createRoom($name, $members_admin_ids, $params = array());

    /**
     * get specified room information
     *
     * @param string $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id
     */
    public function getRoomById($room_id);

    /**
     * update room meta information
     *
     * @param       $room_id
     * @param array $params
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#PUT-rooms-room_id
     */
    public function updateRoomInfo($room_id, $params = array());

    /**
     * delete room
     *
     * @param $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#DELETE-rooms-room_id
     */
    public function deleteRoom($room_id);

    /**
     * leave room
     *
     * @param $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#DELETE-rooms-room_id
     */
    public function leaveRoom($room_id);

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
    public function updateRoomMembers($room_id, $members_admin_ids = array(), $params = array());

    /**
     * get room messages
     *
     * @param $room_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-messages
     */
    public function getRoomMessage($room_id);

    /**
     * get specified message
     *
     * @param $room_id
     * @param $message_id
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-messages-message_id
     */
    public function getRoomMessageByMessageId($room_id, $message_id);

    /**
     * get room tasks
     *
     * @param       $room_id
     * @param array $params
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-tasks
     */
    public function getRoomTasks($room_id, $params);

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
    public function addTask($room_id, $to_ids = array(), $body, $limit = null);

    /**
     * get room task
     *
     * @param $room_id
     * @param $task_id
     * @return mixed|void
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-tasks-task_id
     */
    public function getRoomTaskById($room_id, $task_id);

    /**
     * get room files
     *
     * @param       $room_id
     * @param array $params
     * @return array
     * @throws UnauthorizedException
     * @see http://developer.chatwork.com/ja/endpoint_rooms.html#GET-rooms-room_id-files
     */
    public function getRoomFiles($room_id, $params);

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
    public function getRoomFileById($room_id, $file_id, $create_download_url);

    public function setAuthentication(Authentication $authentication);

    public function setDriver(Driver $driver);

}