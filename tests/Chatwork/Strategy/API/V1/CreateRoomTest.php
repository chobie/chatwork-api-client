<?php
namespace Chatwork\Strategy\API\V1;

use \Chatwork\Exception\UnauthorizedException;
use \Chatwork\StrategyTestCase;

class CreateRoomsTest extends StrategyTestCase
{
    public function testExecuteWhenHTTPOK()
    {
        $fixture = $this->loadFixture("create_room.json");
        $decoded_fixture = json_decode($fixture, true);

        $client = new \Chatwork\API\Client();
        $strategy = new \Chatwork\Strategy\API\V1(array(
            "driver" => $this->getMockDriver(array(
                array(
                    "HTTP_CODE"    => 200,
                    "Content-Type" => "application/json",
                ),
                $fixture,
            ))
        ));
        $client->setStrategy($strategy);

        $result = $client->createRoom("Group Chat Name", array(1));
        $this->assertEquals($result, $decoded_fixture);
    }

    /**
     * @expectedException \Chatwork\Exception\UnauthorizedException
     */
    public function testExecuteWhenHTTPFailed()
    {
        $fixture = $this->loadFixture("errors.json");
        $decoded_fixture = json_decode($fixture, true);

        $client = new \Chatwork\API\Client();
        $strategy = new \Chatwork\Strategy\API\V1(array(
            "driver" => $this->getMockDriver(array(
                array(
                    "HTTP_CODE"    => 401,
                    "Content-Type" => "application/json",
                ),
                $fixture,
            ))
        ));
        $client->setStrategy($strategy);
        $result = $client->createRoom("Group Chat Name", array(1));
    }

}