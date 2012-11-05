<?php
namespace Classmarkets\Statsd;

class Client implements ClientInterface
{
    private $host;
    private $port;

    public function __construct($host = 'localhost', $port = 8125)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function send($value, $rate)
    {
        $fp = fsockopen('udp://' . $this->host, $this->port, $errno, $errstr);
        if (false !== $fp) {
            fwrite($fp, "$value|@$rate");
            fclose($fp);
        }
    }
}
