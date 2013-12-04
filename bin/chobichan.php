<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array("src", "Chatwork", "Autoloader.php"));
Chatwork\Autoloader::register();

date_default_timezone_set("Asia/Tokyo");

define("HTTP_STATUS_OK", 200);
define("HTTP_STATUS_FORBIDDEN", 403);
define("SECONDS_MS", 1000);

$token    = getenv("CW_TOKEN");

if (isset($_SERVER['argv'][1])) {
    $port     = (int)$_SERVER['argv'][1];
}
if (empty($token)) {
    die("please set CW_TOKEN\n");
}
if (empty($port)) {
    $port = 8888;
}

if (is_file(".config")) {
    $config = json_decode(file_get_contents(".config"), true);
} else {
    $config = array(
        'MAX_QUEUE_COUNT' => 40,
        'plugins' => array(
            'Chatwork\Server\Plugin\SendMessagePlugin',
        )
    );
}

$client = \Chatwork\APIFactory::createInstance(array(
    "token"   => $token,
    "plugins" => array('Chatwork\Plugin\Message\SurroundInfoPlugin'),
));

$timer = uv_timer_init();
$loop = uv_default_loop();

$queue     = new SplQueue();
$processor = new Chatwork\Server\QueueProcessor($loop, $timer, $queue);

$container = array(
    "chatwork" => $client,
    "queue"    => $queue,
    "config"   => $config,
);

foreach ($config['plugins'] as $plugin_class) {
    $plugin_klass = new $plugin_class($container);
    $processor->addPlugin($plugin_klass);

    unset($plugin_klass);
}

uv_timer_start($timer, SECONDS_MS, SECONDS_MS, array($processor, "process"));

function createServer(Closure $closure)
{
    $server = new \Chatwork\Server\HttpServer();
    $server->addListener($closure);

    return $server;
}

/* やっつけ */
createServer(function($request, \Chatwork\Server\HttpResponse $response, $client) use($queue, $config){
    parse_str(ltrim($request['QUERY_STRING'], "/?"), $params);
    if (trim($request['QUERY_STRING'], "/") == "favicon.ico") {
        $response->writeHead(HTTP_STATUS_FORBIDDEN, array(
            "Content-Type" => "text/plain",
        ));
        $response->write("Not Found");
        $response->end();
        return;
    } else {
        try {
            /**
             * send message api
             *
             * expected request: /?room_id={room_id}&msg={msg}
             */
            if (isset($params['room_id']) && isset($params['msg'])) {
                if (count($queue) > $config['MAX_QUEUE_COUNT']) {
                    throw new \RuntimeException("Too many queues. please retry");
                }

                $response->writeHead(HTTP_STATUS_OK, array(
                    "Content-Type" => "text/plain"
                ));
                $response->write("Queued");
                $response->end();

                $queue->enqueue(array(
                    "room_id"     => $params['room_id'],
                    "msg"         => $params['msg'],
                ));
            } else {
                throw new \RuntimeException("Forbidden");
            }
        } catch (\RuntimeException $e) {
            $response->writeHead(HTTP_STATUS_FORBIDDEN, array(
                "Content-Type" => "text/plain"
            ));
            $response->write($e->getMessage());
            $response->end();
        }
    }
})->listen($port);
uv_run();
