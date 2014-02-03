<?php
namespace Chatwork\Strategy;

use \Chatwork\Authentication\NothingAuthentication;
use \Chatwork\Authentication\HeadlessAuthentication;
use \Exception;
use \Chatwork\Strategy;
use \Chatwork\Driver;
use \Chatwork\API\RequestBuilder;
use \Chatwork\API\Request;

class HeadlessStrategy
    extends Strategy\BaseStrategy
{
    const CLIENT_VERSION = '1.80a';

    protected $params = array();

    /** @var Driver $driver */
    protected $driver;

    /** @var  string $myid */
    protected $myid;

    /** @var  string $cwssid session id */
    protected $cwssid;

    /** @var  string $access_token */
    protected $access_token;

    protected $last_id = 0;

    public function __construct($params = array())
    {
        $this->params = array_merge($this->getDefaultParams(), $params);
    }

    public function initiate()
    {
        if (!($this->params['authentication'] instanceof HeadlessAuthentication)) {
            throw new \InvalidArgumentException(
                sprintf("headless strategy requires Chatwork\\Authentication\\HeadlessAuthentication %s given",
                    get_class($this->params['authentication']
                    )
                ));
        }

        $driver_class = $this->params['driver'];
        if ($driver_class instanceof Driver) {
            $this->driver = $driver_class;
        } else {
            $this->driver = new $driver_class();
        }

        try {
            $this->doLogin();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
        $this->initiated = true;
    }

    public function initChat()
    {
        if (!$this->initiated) {
            $this->initiate();
        }

        $query = array(
            "cmd"  => "init_load",
            "myid" => $this->myid,
            "_v"   => self::CLIENT_VERSION,
            "_av"  => 4,
            "_t"   => $this->access_token,
            "ln"   => "en",
            "new" => 1
        );

        $builder = new RequestBuilder();
        $builder->setRequestMethod("GET");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/gateway.php");
        $builder->setQueryParams($query);
        $request = $builder->build($this, $this->driver);

        $res = $this->driver->request($request);

        if (strpos($res[0]['Content-Type'], "json") !== false) {
            return $res[1];
        } else {
            return json_decode($res[1], true);
        }
    }

    public function loadChat($room_id)
    {
        if (!$this->initiated) {
            $this->initiate();
        }

        $query = array(
            "cmd"  => "load_chat",
            "myid" => $this->myid,
            "_v"   => self::CLIENT_VERSION,
            "_av"  => 4,
            "_t"   => $this->access_token,
            "ln"   => "en",
            "room_id" => $room_id,
            "last_chat_id" => $this->last_id,
            "jump_to_chat_id" => 0,
            "unread_num" => 0,
        );

        $builder = new RequestBuilder();
        $builder->setRequestMethod("GET");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/gateway.php");
        $builder->setQueryParams($query);
        $request = $builder->build($this, $this->driver);

        $res = $this->driver->request($request);

        if (strpos($res[0]['Content-Type'], "json") !== false) {
            return $res[1];
        } else {
            return json_decode($res[1], true);
        }
    }

    public function getUpdate()
    {
        if (!$this->initiated) {
            $this->initiate();
        }

        $query = array(
            "cmd"  => "get_update",
            "myid" => $this->myid,
            "_v"   => self::CLIENT_VERSION,
            "_av"  => 4,
            "_t"   => $this->access_token,
            "account_id" => $this->myid,
            "last_id" => $this->last_id,
            "ver" => self::CLIENT_VERSION,
            "new" => 1,
            "ln"   => "en",
            "_" => time() * 1000,
        );

        $builder = new RequestBuilder();
        $builder->setRequestMethod("GET");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/gateway.php");
        $builder->setQueryParams($query);
        $request = $builder->build($this, $this->driver);

        $res = $this->driver->request($request);

        if (strpos($res[0]['Content-Type'], "json") !== false) {
            return $res[1];
        } else {
            return json_decode($res[1], true);
        }
    }

    public function getCometTokenKey()
    {
        if (!$this->initiated) {
            $this->initiate();
        }

        $query = array(
            "cmd"  => "get_comet_token_key",
            "myid" => $this->myid,
            "_v"   => self::CLIENT_VERSION,
            "_av"  => 4,
            "_t"   => $this->access_token,
            "ln"   => "en"
        );

        $builder = new RequestBuilder();
        $builder->setRequestMethod("GET");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/gateway.php");
        $builder->setQueryParams($query);
        $request = $builder->build($this, $this->driver);

        $res = $this->driver->request($request);

        if (strpos($res[0]['Content-Type'], "json") !== false) {
            return $res[1];
        } else {
            $json = json_decode($res[1], true);
            return $json['result']['token_key'];
        }
    }

    public function sendMessage($room_id, $message)
    {
        if (!$this->initiated) {
            $this->initiate();
        }

        $query = array(
            "cmd"  => "send_chat",
            "myid" => $this->myid,
            "_v"   => self::CLIENT_VERSION,
            "_av"  => 4,
            "_t"   => $this->access_token,
            "ln"   => "en"
        );

        $post = array(
            "pdata" => json_encode(array(
                "text"         => $message,
                "room_id"      => (string)$room_id,
                "last_chat_id" => 0,
                "read"         => 1,
                "edit_id"      => 0,
            ))
        );

        $builder = new RequestBuilder();
        $builder->setRequestMethod("POST");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/gateway.php");
        $builder->setQueryParams($query);
        $builder->setPostField($post);
        $request = $builder->build($this, $this->driver);

        $res = $this->driver->request($request);

        if (strpos($res[0]['Content-Type'], "json") !== false) {
            return $res[1];
        } else {
            return json_decode($res[1], true);
        }
    }

    protected function doLogin()
    {
        $builder = new RequestBuilder();
        $builder->setRequestMethod("POST");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/login.php");
        $builder->setQueryParams(array(
            "s"       => $this->params['s'],
            "lang"    => "en",
            "package" => "chatwork",
            "args"    => "",
        ));
        $builder->setAuthentication($this->params['authentication']);

        if (!$this->params['authentication'] instanceof HeadlessAuthentication) {
            throw new \InvalidArgumentException("headless strategy requires headless authentication");
        }
        if (!($this->driver instanceof Driver\CurlDriver)) {
            throw new \InvalidArgumentException("headless strategy expects curl driver");
        }

        $request = $builder->build($this, $this->driver);
        $raw_result = $this->driver->request($request);
        $cookies = array();
        foreach ($raw_result[0] as $key => $value) {
            if ('Set-Cookie' == $key) {
                parse_str($value, $tmp);
                $cookies += $tmp;
            }
        }

        $this->cwssid = $cookies['cwssid'];
        if (preg_match("/var\s+myid\s+=\s+'([a-zA-Z0-9]++)'/", $raw_result[1], $match)) {
            $this->myid = $match[1];
        }

        if (preg_match("/var\s+ACCESS_TOKEN\s+=\s+'([a-zA-Z0-9]+)'/", $raw_result[1], $match)) {
            $this->access_token = $match[1];
        }

        if (empty($this->access_token)) {
            throw new Exception("could not logged in to chatwork");
        }
   }


    protected function getDefaultParams()
    {
        return array(
            "s"              => null,
            "authentication" => new NothingAuthentication(),
            "driver"         => "Chatwork\\Driver\\CurlDriver",
            "endpoint"       => 'https://www.chatwork.com',
        );
    }
}