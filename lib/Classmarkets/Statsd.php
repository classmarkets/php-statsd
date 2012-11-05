<?php
namespace Classmarkets;

class Statsd
{
    private $host;
    private $port;
    private $socket;

    public function __construct($host = 'localhost', $port = 8125, $socket = null)
    {
        $this->host = $host;
        $this->port = $port;

        $this->socket = $socket ?: socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->messageLength = extension_loaded('mbstring') ? 'mb_strlen' : 'strlen';
    }

    public function timing($metric, $time, $rate = 1)
    {
        return $this->send("$metric:$time|ms", $rate);
    }

    public function timeThis($metric, callable $callback, $rate = 1)
    {
        $begin = microtime(true);
        $callback();
        $time = floor((microtime(true) - $begin) * 1000);

        return $this->timing($metric, $time, $rate);
    }

    public function count($metric, $amount = 1, $rate = 1)
    {
        return $this->send("$metric:$amount|c", $rate);
    }

    public function send($metric, $rate)
    {
        $message       = $this->formatMessage($metric, $rate);
        $messageLength = strlen($message);

        return socket_sendto($this->socket, $message, $messageLength, 0, $this->host, $this->port);
    }

    private function formatMessage($metric, $rate)
    {
        $message = $metric;

        $rate = (float) $rate;
        if (1.0 !== $rate && .0 !== $rate) {
            $message .= "|@$rate";
        }

        return $message;
    }
}
