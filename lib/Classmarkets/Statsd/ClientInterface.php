<?php
namespace Classmarkets\Statsd;

interface ClientInterface
{
    function send($value, $rate);
}
