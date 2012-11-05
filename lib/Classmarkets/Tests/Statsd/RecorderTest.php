<?php
namespace Classmarkets\Tests\Statsd;

use Classmarkets\Statsd\Recorder;
use Classmarkets\Statsd\ClientInterface;

class RecorderTest extends \PHPUnit_Framework_TestCase
{
    public function testCounting()
    {
        $metric = 'something.countable';
        $this->client->expects($this->once())
                     ->method('send')
                     ->with(
                         $this->equalTo('something.countable:1|c'),
                         $this->equalTo(1)
                     );

        $this->recorder->counting('something.countable');
    }

    public function testIncrementByTwo()
    {
        $metric = 'something.countable';
        $this->client->expects($this->once())
                     ->method('send')
                     ->with(
                         $this->equalTo('something.countable:2|c'),
                         $this->equalTo(1)
                     );

        $this->recorder->counting('something.countable', 2);
    }

    public function testSampledCounting()
    {
        $metric = 'something.countable';
        $this->client->expects($this->once())
                     ->method('send')
                     ->with(
                         $this->equalTo('something.countable:2|c'),
                         $this->equalTo(.5)
                     );

        $this->recorder->counting('something.countable', 2, .5);
    }

    public function testTiming()
    {
        $metric = 'something.that.took.time';
        $this->client->expects($this->once())
                     ->method('send')
                     ->with(
                         $this->equalTo('something.that.took.time:5|ms'),
                         $this->equalTo(1)
                     );

        $this->recorder->timing('something.that.took.time', 5);
    }

    public function testTimingCallback()
    {
        $metric = 'callback.that.took.time';

        $this->client->expects($this->once())
                     ->method('send')
                     ->with(
                         $this->equalTo('callback.that.took.time:100|ms'),
                         $this->equalTo(1)
                     );

        // Not exactly sure about this. usleep() is precise enough on my system
        // to make this test pass, but that may be the case on all platforms
        $this->recorder->timeThis('callback.that.took.time', function() {
            usleep(100000);
        });
    }

    private $recorder;
    private $client;

    public function setUp()
    {
        $this->client = $this->getMock('Classmarkets\\Statsd\\ClientInterface');
        $this->recorder = new Recorder($this->client);
    }
}
