<?php
namespace Chatwork;

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

    public function sendMessage($room_id, $message);

    public function me();

    public function getMyStatus();

    public function getMyTasks($params = array());

    public function getContacts();

    public function getRooms();

    public function createRoom($name, $members_admin_ids, $params = array());

    public function getRoomById($room_id);

    public function updateRoomInfo($room_id, $params = array());

    public function deleteRoom($room_id);

    public function leaveRoom($room_id);

    public function updateRoomMembers($room_id, $members_admin_ids = array(), $params = array());

    public function getRoomMessage($room_id);

    public function getRoomMessageByMessageId($room_id, $message_id);

    public function getRoomTasks($room_id, $params);

    public function addTask($room_id, $to_ids = array(), $body, $limit = null);

    public function getRoomTaskById($room_id, $task_id);

    public function getRoomFiles($room_id, $params);

    public function getRoomFileById($room_id, $file_id, $create_download_url);

}