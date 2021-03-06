<?php
namespace Chatwork\Server\Provider;
use Chatwork\Server\ControllerCollection;
use Chatwork\Server\Kernel;

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
class SendMessageProvider
{
    protected $client;
    protected $stat;

    public function __construct($container)
    {
        $this->client = $container['chatwork'];
    }

    public function connect(Kernel $kernel)
    {
        $container = $kernel->getContainer();
        $this->stat = $container['stat'];

        $collection = new ControllerCollection();
        $collection->get("/", function($request, $params) use ($kernel){
            $container = $kernel->getContainer();
            $queue = $container['queue'];
            $config = $container['config'];

            if (isset($params['room_id']) && isset($params['msg'])) {
                if (count($queue) > $config['MAX_QUEUE_COUNT']) {
                    throw new \RuntimeException("Too many queues. please retry");
                }

                $queue->enqueue(array(
                    "room_id"     => $params['room_id'],
                    "msg"         => $params['msg'],
                    "address"     => $request['peer']['address'],
                ));
                $this->stat->increment('message.enqueue', 1);
            } else {
                throw new \Exception("usage: /?room_id=ROOM_ID&msg=MESSAGE");
            }
            return "QUEUED";
        });

        return $collection;
    }
}
