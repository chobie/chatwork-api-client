<?php
namespace Chatwork\Driver;

use \Chatwork\Driver;

class CurlDriver
    implements Driver
{
    protected $curl;

    public function __construct()
    {
        $ch = curl_init();
        $cookie_file = ".chatwork";

        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch ,CURLOPT_HTTPHEADER, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        $this->curl = $ch;
    }

    public function request($http_method = "GET", $endpoint, $query, $params, $post_field = array())
    {
        $curl = cury_copy_handle($this->curl);
        $url = $endpoint . $query;
        if (!empty($params)) {
            $url .= "?" . http_build_query($params);
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        if ($http_method == "POST") {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($post_field) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_field);
            }
        } else if ($http_method == "GET") {
            curl_setopt($curl, CURLOPT_GET, 1);
        }

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        $header = substr($response, 0, $info['header_size']);
        $output = substr($response, $info['header_size']);

        return array(
            $header,
            $output
        );

    }
}