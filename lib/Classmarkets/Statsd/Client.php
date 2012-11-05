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

    public function send($value, $rate)
    {
        $message = sprintf("%s|@%f", $value, $rate);
        $messageLength = call_user_func($this->messageLength, $message);

        return socket_sendto($this->socket, $message, $messageLength, 0, $this->host, $this->port);
    }
}
