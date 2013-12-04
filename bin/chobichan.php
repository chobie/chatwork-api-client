<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array("src", "Chatwork", "Autoloader.php"));
Chatwork\Autoloader::register();

date_default_timezone_set("Asia/Tokyo");
define("SECONDS_MS", 1000);

$token    = getenv("CW_TOKEN");
if (isset($_SERVER['argv'][1])) {
    $port = (int)$_SERVER['argv'][1];
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
            '\Chatwork\Server\Plugin\SendMessagePlugin',
        ),
        'providers' => array(
            '\Chatwork\Server\Provider\SendMessageProvider',
            '\Chatwork\Server\Provider\StatisticsProvider',
            '\Chatwork\Server\Provider\FaviconProvider',
        )
    );
}

$client = \Chatwork\APIFactory::createInstance(array(
    "token"   => $token,
    "plugins" => array('Chatwork\Plugin\Message\SurroundInfoPlugin'),
));

$timer = uv_timer_init();
$loop  = uv_default_loop();

$queue     = new SplQueue();
$processor = new Chatwork\Server\QueueProcessor($loop, $timer, $queue);

$stat = new \Chatwork\Server\Statistics();
$container = array(
    "chatwork" => $client,
    "queue"    => $queue,
    "config"   => $config,
    "stat"     => $stat,
);

foreach ((array)$config['plugins'] as $plugin_class) {
    $plugin_klass = new $plugin_class($container);
    $processor->addPlugin($plugin_klass);
    unset($plugin_klass);
}

$kernel = new Chatwork\Server\Kernel();
$kernel->setRouter(new \Chatwork\Server\Router());
$kernel->setContainer($container);

foreach ((array)$config['providers'] as $provider_class) {
    $provider_klass = new $provider_class($container);
    $kernel->registerProviders(array($provider_klass));
}

uv_timer_start($timer, SECONDS_MS, SECONDS_MS, array($processor, "process"));
createServer(function($request = array(), \Chatwork\Server\HttpResponse $response, $client) use ($kernel, $stat){
    parse_str(ltrim($request['QUERY_STRING'], "/?"), $params);

    $peer = uv_tcp_getpeername($client);
    $request['peer'] = $peer;

    try {
        $result = $kernel->process($request, $params);
        $response->writeHead(200, array(
            "Content-Type" => "text/plain",
        ));
        $response->write($result);
        $response->end();
        $stat->increment("http.200");
    } catch (\Chatwork\Exception\RouteNotFoundException $e) {
        $response->writeHead(404, array(
            "Content-Type" => "text/html",
        ));
        $response->write("<h3>Not Found</h3>");
        $response->write("<div>".$e->getMessage()."</div>");
        $response->end();
        $stat->increment("http.404");
    } catch (Exception $e) {
        $response->writeHead(500, array(
            "Content-Type" => "text/html",
        ));
        $response->write("<h3>Server Error</h3>");
        $response->write($e->getMessage());
        $response->end();
        $stat->increment("http.500");
    }
})->listen($port);
uv_run();

function createServer(Closure $closure)
{
    $server = new \Chatwork\Server\HttpServer();
    $server->addListener($closure);

    return $server;
}

exit(0);