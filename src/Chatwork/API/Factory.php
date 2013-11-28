<?php
namespace Chatwork\API;

class Factory
{
    public function getInstance()
    {
        $client = new Client();
        return $client;
    }
}