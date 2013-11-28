<?php
namespace Chatwork;

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