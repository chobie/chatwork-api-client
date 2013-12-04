<?php
namespace Chatwork\Server;

use \RuntimeException;
use \Exception;

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
class QueueProcessor
{
    protected $loop;
    protected $timer;
    protected $queue;
    protected $plugins = array();
    protected $stat;

    public function __construct($loop, $timer, $queue)
    {
        $this->loop = $loop;
        $this->timer = $timer;
        $this->queue = $queue;
        $this->plugins = array();
    }

    public function addPlugin($plugin)
    {
        $this->plugins[] = $plugin;
    }

    public function process()
    {
        if (count($this->queue)) {
            try {
                $begin = microtime(true);
                $last  = $begin;

                for ($x = 0; $x < count($this->queue) || $x < 10; $x++) {
                    if (($last - $begin) >= 1) {
                        throw new RuntimeException("abort");
                    }

                    $params = $this->queue->dequeue();
                    foreach ($this->plugins as $plugin) {
                        try {
                            $plugin->execute($params['room_id'], $params['msg'], $params);
                        } catch (\Exception $e) {
                            var_dump($e->getMessage());
                            var_dump($e->getTraceAsString());
                        }
                    }

                    $now = microtime(true);

                    if ($now - $last < 0.2) {
                        $elapsed = sprintf("%6f", $now - $last);
                        $sleep = (0.2 - $elapsed) * 1000000;
                        usleep($sleep);
                    }

                    unset($params);
                    $last = microtime(true);
                }

                unset($last);
                unset($begin);
            } catch (\RuntimeException $e) {
                // do nothing.
                unset($e);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                var_dump($e->getTraceAsString());
                unset($e);
            }
        }
    }
}