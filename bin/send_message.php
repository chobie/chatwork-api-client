<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array("src", "Chatwork", "Autoloader.php"));

Chatwork\Autoloader::register();

$token    = getenv("CW_TOKEN");
$room_id = null;
$message = "";

if (isset($_SERVER['argv'][1])) {
    $room_id = $_SERVER['argv'][1];
}
if (isset($_SERVER['argv'][2])) {
    $message = $_SERVER['argv'][2];
}

if (empty($token)) {
    die("please set CW_TOKEN\n");
}
if (empty($room_id) || empty($message)) {
    die("# send_message.php\n# Usage:\n\nexport CW_TOKEN=************\nphp send_message.php <room_id> <message>\n");
}

$client = \Chatwork\APIFactory::createInstance(array(
    "token" => $token,
));

$client->sendMessage($room_id, $message);
