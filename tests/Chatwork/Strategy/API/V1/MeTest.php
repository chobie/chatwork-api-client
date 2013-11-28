<?php
namespace Chatwork\Strategy\API\V1;

use \Chatwork\Exception\UnauthorizedException;

class MeTest extends \PHPUnit_Framework_TestCase
{
    public function loadFixture($name)
    {
        return file_get_contents(join(
                DIRECTORY_SEPARATOR,
                array(CHATWORK_FIXTURE_PATH, $name))
        );
    }

    public function testExecuteWhenHTTPOK()
    {
        $fixture = $this->loadFixture("me.json");
        $client = new \Chatwork\API\Client();

        $decoded_fixture = json_decode($fixture, true);
        $driver = $this->getMockForAbstractClass('\Chatwork\Driver');
        $driver->expects($this->any())
            ->method('request')
            ->will($this->returnValue(array(
                array(
                    "HTTP_CODE"    => 200,
                    "Content-Type" => "application/json",
                ),
                $fixture,
            ))
        );
        $strategy = new \Chatwork\Strategy\API\V1(array(
            "driver" => $driver
        ));
        $client->setStrategy($strategy);

        $result = $client->me();
        $this->assertEquals($result, $decoded_fixture);
    }

    /**
     * @expectedException \Chatwork\Exception\UnauthorizedException
     */
    public function testExecuteWhenHTTPFailed()
    {
        $fixture = $this->loadFixture("errors.json");
        $client = new \Chatwork\API\Client();

        $decoded_fixture = json_decode($fixture, true);
        $driver = $this->getMockForAbstractClass('\Chatwork\Driver');
        $driver->expects($this->any())
            ->method('request')
            ->will($this->returnValue(array(
                    array(
                        "HTTP_CODE"    => 401,
                        "Content-Type" => "application/json",
                    ),
                    $fixture,
                ))
            );
        $strategy = new \Chatwork\Strategy\API\V1(array(
            "driver" => $driver
        ));
        $client->setStrategy($strategy);
        $result = $client->me();
    }

}