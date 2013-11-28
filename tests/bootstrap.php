<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), "src", "Chatwork", "Autoloader.php"));
Chatwork\Autoloader::register();

require_once join(DIRECTORY_SEPARATOR, array("Chatwork", "StrategyTestCase.php"));

date_default_timezone_set("UTC");

define("CHATWORK_FIXTURE_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR);