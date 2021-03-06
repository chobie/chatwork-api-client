# Chatwork API Client

unofficial, but glittering chatwork api client.

[![Build Status](https://secure.travis-ci.org/chobie/chatwork-api-client.png)](http://travis-ci.org/chobie/chatwork-api-client)

STATUS
--------------------------

alpha development

USAGE
--------------------------

composer.json

````
{
  "require": {
      "chobie/chatwork-api-client": "dev-master"
   }
}
````

Example

````php
<?php
require __DIR__ . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array("vendor", "autoload.php"));
$room_id = "123456768";

use Chatwork\APIFactory;

$client = APIFactory::createInstance(array(
    "token" => "**********",
));

$client->sendMessage($room_id, "Hello World");
````

Requirements
------------

* php5.3 higher

* openssl extension

* curl extension

NOTE for self building users: chatwork api requires SSL connection. this mean you must compile PHP
with `--with-openssl` option. please check this article http://us1.php.net/manual/ja/openssl.installation.php

you can check with `php --ri openssl` command.

````
 php --ri openssl

openssl

OpenSSL support => enabled
OpenSSL Library Version => OpenSSL 0.9.8o 01 Jun 2010
OpenSSL HeaderAuthentication Version => OpenSSL 0.9.8o 01 Jun 2010
````

* (optional) libuv extension

LICENSE
-------

MIT LICENSE
