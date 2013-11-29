<?php
namespace Chatwork\Plugin\Message;

use \Chatwork\Plugin;

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
class SurroundInfoPlugin
    extends Plugin
{
    protected $use_title = true;

    protected $type = Plugin::PLUGIN_TYPE_SEND_MESSAGE;

    public function __construct($use_title = true)
    {
        $this->use_title = $use_title;
    }

    public function filter(&$room_id, &$message)
    {
        list($title, $description) = explode("\n", $message, 2);

        $msg = "[info]";
        if ($this->use_title && !empty($description)) {
            $msg .= "[title]" . $title . "[/title]";
        } else {
            $msg .= $title . PHP_EOL;
        }

        $msg .= $description;
        $msg .= "[/info]";

        $message = $msg;
    }
}