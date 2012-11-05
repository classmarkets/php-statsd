<?php
namespace {
$socketSendtoArgs = array();
}

namespace Classmarkets\Statsd {
function socket_sendto($socket, $message, $messageLength, $flags, $host, $port)
{
    global $socketSendtoArgs;
    $socketSendtoArgs = func_get_args();

    return $messageLength;
}
}

namespace Classmarkets\Tests\Statsd {

use Classmarkets\Statsd\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testCounting()
    {
        $this->client->counting('something.countable');
        $this->expect('something.countable:1|c', 23);
    }

    public function testIncrementByTwo()
    {
        $this->client->counting('something.countable', 2);
        $this->expect('something.countable:2|c', 23);
    }

    public function testSampledCounting()
    {
        $this->client->counting('something.countable', 2, .5);
        $this->expect('something.countable:2|c|@0.5', 28);
    }

    public function testTiming()
    {
        $this->client->timing('something.that.took.time', 5);
        $this->expect('something.that.took.time:5|ms', 29);
    }

    public function testTimingCallback()
    {
        // Not exactly sure about this. usleep() is precise enough on my system
        // to make this test pass, but that may not be the case on all platforms
        $this->client->timeThis('callback.that.took.time', function() {
            usleep(100000);
        });
        $this->expect('callback.that.took.time:100|ms', 30);
    }

    private $client;

    public function setUp()
    {
        global $socketSendtoArgs;
        $socketSendtoArgs = array();
        $this->client = new Client('localhost', 8125, new \StdClass);
    }

    private function expect($expectedMessage, $expectedMessageLength)
    {
        global $socketSendtoArgs;
        list (, $actualMessage, $actualMessageLength) = $socketSendtoArgs;

        $this->assertEquals($expectedMessage, $actualMessage);
        $this->assertEquals($expectedMessageLength, $actualMessageLength);
    }
}
}
