<?php
namespace Chatwork\Plugin\Message;

use \Chatwork\Plugin;

class SurroundInfoPlugin
{
    protected $use_title = true;

    public function __construct($use_title = true)
    {
        $this->use_title = $use_title;
    }

    public function getType()
    {
        return Plugin::PLUGIN_TYPE_SEND_MESSAGE;
    }

    public function filter(&$room_id, &$message)
    {
        list($title, $description) = explode("\n", $message, 2);

        $msg = "[info]";
        if ($this->use_title) {
            $msg .= "[title]" . $title . "[/title]";
        } else {
            $msg .= $title . PHP_EOL;
        }

        $msg .= $description;
        $msg .= "[/info]";

        $message = $msg;
    }
}