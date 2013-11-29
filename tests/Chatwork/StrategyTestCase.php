<?php
namespace Chatwork;

class StrategyTestCase extends \PHPUnit_Framework_TestCase
{
    public function loadFixture($name)
    {
        return file_get_contents(join(
                DIRECTORY_SEPARATOR,
                array(CHATWORK_FIXTURE_PATH, $name))
        );
    }

    public function getMockDriver($result)
    {
        $driver = $this->getMockForAbstractClass('\Chatwork\Driver');
        $driver->expects($this->any())
            ->method('request')
            ->will($this->returnValue($result)
        );

        return $driver;
    }
}