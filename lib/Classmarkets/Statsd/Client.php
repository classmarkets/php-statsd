<?php
namespace Classmarkets\Statsd;

class Client implements ClientInterface
{
    private $host;
    private $port;
    private $socket;

    private $messageLength;

    public function __construct($host = 'localhost', $port = 8125, $socket = null)
    {
        $this->host = $host;
        $this->port = $port;

        $this->socket = $socket ?: socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->messageLength = extension_loaded('mbstring') ? 'mb_strlen' : 'strlen';
    }

    public function timing($key, $time, $rate = 1)
    {
        return $this->send("$key:$time|ms", $rate);
    }

    public function timeThis($key, callable $callback, $rate = 1)
    {
        $begin = microtime(true);
        $callback();
        $time = floor((microtime(true) - $begin) * 1000);

        return $this->timing($key, $time, $rate);
    }

    public function counting($key, $amount = 1, $rate = 1)
    {
        return $this->send("$key:$amount|c", $rate);
    }

    public function send($value, $rate)
    {
        $message = sprintf("%s|@%f", $value, $rate);
        $messageLength = call_user_func($this->messageLength, $message);

        return socket_sendto($this->socket, $message, $messageLength, 0, $this->host, $this->port);
    }
}
