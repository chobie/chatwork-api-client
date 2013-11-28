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

}