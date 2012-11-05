<?php
namespace Classmarkets\Statsd;

class Recorder
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function timing($key, $time, $rate = 1)
    {
        $this->client->send("$key:$time|ms", $rate);
    }

    public function timeThis($key, callable $callback, $rate = 1)
    {
        $begin = microtime(true);
        $callback();
        $time = floor((microtime(true) - $begin) * 1000);

        $this->timing($key, $time, $rate);
    }

    public function counting($key, $amount = 1, $rate = 1)
    {
        $this->client->send("$key:$amount|c", $rate);
    }
}
