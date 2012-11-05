<?php
namespace {
$socketSendtoArgs = array();
}

namespace Classmarkets {
function socket_sendto($socket, $message, $messageLength, $flags, $host, $port)
{
    global $socketSendtoArgs;
    $socketSendtoArgs = func_get_args();

    return $messageLength;
}
}

namespace Classmarkets\Tests {

use Classmarkets\Statsd;

class StatsdTest extends \PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        $this->client->count('something.countable');
        $this->expect('something.countable:1|c', 23);
    }

    public function testIncrementByTwo()
    {
        $this->client->count('something.countable', 2);
        $this->expect('something.countable:2|c', 23);
    }

    public function testSampledCounting()
    {
        $this->client->count('something.countable', 2, .5);
        $this->expect('something.countable:2|c|@0.5', 28);
    }

    public function testTiming()
    {
        $this->client->timing('something.that.took.time', 5);
        $this->expect('something.that.took.time:5|ms', 29);
    }

    public function testGauge()
    {
        $this->client->gauge('some.arbitrary.metric', M_PI);
        $this->expect('some.arbitrary.metric:3.1415926535898|g', 39);
    }

    public function testSet()
    {
        $this->client->gauge('some.uniqueness.metric', 101);
        $this->expect('some.uniqueness.metric:101|g', 28);
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
        $this->client = new Statsd('localhost', 8125, new \StdClass);
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
