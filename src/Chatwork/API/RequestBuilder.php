<?php
namespace Chatwork\API;

use \Chatwork\Authentication;
use \Chatwork\API\Request;
use \Chatwork\Authentication\HeaderAuthentication;
use \Chatwork\Authentication\HeadlessAuthentication;
use Chatwork\Driver;
use Chatwork\Strategy;

class RequestBuilder
{
    /** @var string $request_method */
    protected $request_method = 'GET';

    /** @var  string $endpoint */
    protected $endpoint;

    /** @var  string $query */
    protected $query;

    /** @var array $query_params */
    protected $query_params = array();

    /** @var array $post_field */
    protected $post_field = array();

    /** @var string $version HTTP version */
    protected $http_version = '1.0';

    /** @var  string $api_version */
    protected $api_version = 'v1';

    /** @var  Authentication $authentication */
    protected $authentication;

    /** @var  string $user_agent */
    protected $user_agent;

    /** @var  string $proxy */
    protected $proxy;

    public function __construct()
    {
    }

    public function setRequestMethod($http_method)
    {
        $this->request_method = $http_method;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function setQueryParams($params)
    {
        $this->query_params = $params;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function setPostField($post_field)
    {
        $this->post_field = $post_field;
    }

    public function setAuthentication(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    public function build(Strategy $strategy, Driver $driver)
    {
        $data = null;
        $headers = array();
        $authentication = $this->authentication;

        if ($this->authentication && $authentication instanceof HeaderAuthentication) {
            /* @var HeaderAuthentication $authentication */
            $headers[$authentication->getHeaderKey()] = $authentication->getToken();
        }

        if ($this->query_params) {
            $url = sprintf("%s%s", $this->endpoint, $this->query . "?" . http_build_query($this->query_params));
        } else {
            $url = sprintf("%s%s", $this->endpoint, $this->query);
        }

        if ($authentication) {
            if ($driver instanceof Driver\CurlDriver) {
                if ($authentication instanceof HeaderAuthentication) {
                    /* Nothing to do. */
                } else if ($authentication instanceof HeadlessAuthentication) {
                    if ($this->query == "/login.php") {
                        $this->post_field = array(
                            'email'      => $authentication->getLogin(),
                            'password'   => $authentication->getPassword(),
                            'auto_login' => "on",
                            'login'      => 'Login',
                        );
                    }
                } else {
                    throw new \RuntimeException(sprintf("CurlDriver does not support %s authentication", get_class($authentication)));
                }
            } else if ($driver instanceof Driver\StreamSocketDriver) {
                /* Nothing to do. */
            }
        }

        if ($this->request_method != "GET") {
            $data = http_build_query($this->post_field);
            $headers['Content-Type'] = "application/x-www-form-urlencoded";
            $headers['Content-Length'] = strlen($data);
        }

        $request = new Request(array(
            "request_method" => $this->request_method,
            "query"          => $this->query,
            "query_params"   => $this->query_params,
            "endpoint"       => $this->endpoint,
            "post_field"     => $this->post_field,
            "authentication" => $this->authentication,
            "content_body"   => $data,
            "proxy"          => null,
            "headers"        => $headers,
            "url"            => $url,
        ));

        return $request;
    }
}