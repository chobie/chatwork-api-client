<?php
namespace Chatwork\API;

class Request
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

    public function __construct($params = array())
    {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->api_version;
    }

    /**
     * @return \Chatwork\API\Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->http_version;
    }

    /**
     * @return array
     */
    public function getPostField()
    {
        return $this->post_field;
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->query_params;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->request_method;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

}