<?php
namespace Chatwork\Strategy\API\V1;

use \Chatwork\Exception\UnauthorizedException;
use \Chatwork\StrategyTestCase;

class GetRoomTasksTest extends StrategyTestCase
{
    public function testExecuteWhenHTTPOK()
    {
        $fixture = $this->loadFixture("get_room_tasks.json");
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

        $result = $client->getRoomTasks(5);
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
        $result = $client->getRoomTasks(5);
    }

}