<?php
namespace Chatwork\Server;

use \RuntimeException;
use \Exception;

class QueueProcessor
{
    protected $loop;
    protected $timer;
    protected $queue;
    protected $chatwork;
    protected $plugins = array();
    protected $stat;

    public function __construct($loop, $timer, $queue)
    {
        $this->loop = $loop;
        $this->timer = $timer;
        $this->queue = $queue;
        $this->plugins = array();
        $this->i = 0;
    }

    public function addPlugin($plugin)
    {
        $this->plugins[] = $plugin;
    }

    public function process()
    {
        printf("# (%d) memory usage: %d bytes, Queue count:%d\n", $this->i, memory_get_usage(true), count($this->queue));

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

        $this->i++;
    }
}