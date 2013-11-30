<?php
namespace Chatwork\Strategy;

use \Chatwork\Authentication\NothingAuthentication;
use \Chatwork\Authentication\HeadlessAuthentication as Authentication_Headless;
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

    public function __construct($params = array())
    {
        $this->params = array_merge($this->getDefaultParams(), $params);

        if (!($this->params['authentication'] instanceof Authentication_Headless)) {
            throw new \InvalidArgumentException(
                sprintf("headless strategy requires Chatwork\\Authentication\\HeadlessAuthentication %s given",
                    get_class($this->params['authentication']
                )
            ));
        }
    }

    public function initiate()
    {
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
    }

    public function sendMessage($room_id, $message)
    {
        $query = array(
            "cmd"  => "send_chat",
            "myid" => $this->myid,
            "_v"   => self::CLIENT_VERSION,
            "_av"  => 4,
            "_t"   => $this->access_token,
            "ln"   => "en"
        );

        $post = http_build_query(array(
            "pdata" => json_encode(array(
                "text"         => $message,
                "room_id"      => (string)$room_id,
                "last_chat_id" => 0,
                "read"         => 1,
                "edit_id"      => 0,
            ))
        ));

        $builder = new RequestBuilder();
        $builder->setRequestMethod("POST");
        $builder->setEndpoint($this->params['endpoint']);
        $builder->setQuery("/gateway.php");
        $builder->setQueryParams($query);
        $builder->setPostField($post);
        $request = $builder->build();

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
        $builder->setPostField(http_build_query(array(
                'email'      => $this->params['authentication']->getLogin(),
                'password'   => $this->params['authentication']->getPassword(),
                'auto_login' => "on",
                'login'      => 'Login',
            )
        ));
        $request = $builder->build();
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
            "endpoint"       => 'https://www.chatwork.com/',
        );
    }
}