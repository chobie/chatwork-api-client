<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array("src", "Chatwork", "Autoloader.php"));

Chatwork\Autoloader::register();

$token    = getenv("CW_TOKEN");
if (empty($token)) {
    die("please set CW_TOKEN\n");
}

$client = \Chatwork\APIFactory::createInstance(array(
    "token" => $token,
));

var_dump($client->me());
