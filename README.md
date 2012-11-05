# PHP StatsD Client

This is a PHP [statsd](https://github.com/etsy/statsd.git) client based on 
[work by John Crepezzi](https://github.com/seejohnrun/php-statsd).
We refactored the library into a composer module and opened the API a bit to improve flexibility.

## Installation
`composer.json`
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/classmarkets/php-statsd"
        }
    ],
    "require": {
        "classmarkets/php-statsd": "master-dev"
    }
}
```

## Usage
### General

```php
$statsd = new \Classmarkets\Statsd;
$statsd->send("my.favorite.numbers", 73); // see what I did there?
```

### Counting

To count things:

```php
$statsd = new \Classmarkets\Statsd;
$statsd->count('sheep', 3);
```

### Timing

Record timings:

``` php
$statsd = new \Classmarkets\Statsd;
$statsd->timing('critical.query', 18);
```

Timings are given in milliseconds, see https://github.com/etsy/statsd#timing

### Timing Closures

And a convenience mechanism for timing:

```php
$statsd = new \Classmarkets\Statsd;
$statsd->timeThis('critical.query', function() use ($db) {
    $db->executeCriticalQuery();
});
```

## Configuration

### Host and Port

```php
$statsd = new \Classmarkets\Statsd('localhost', 7000); // default localhost:8125
```

If called like this, Statsd will create a default UDP socket. 
For more control you can also pass a socket as the third argument:
```php
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
$statsd = new \Classmarkets\Statsd('localhost', 7000, $socket); // default localhost:8125
```

### Sample Rate

Any of the methods descriped in the usage section can take an optional third argument `$rate`, which is the sample rate:

```php
$statsd = new \Classmarkets\Statsd;
$stats->count('numpoints', 123, 0.1);
```

## Changes to the original library

- __BC break__ CodeIgniter support has been removed. We are not familiar with CI and thus can't guarantee for compatibility. 
  Support may be re-added in the future.
- __BC break__ `Statsd` has been moved to the `\Classmarkets` namespace.
- __BC break__ Codestyle has been changed to PSR-1. `Statsd::time_this()` is now called `Statsd::timeThis()`. 
- __BC break__ `Statsd::counting()` has been renamed to `Statsd::count()` for consistency
- `Statsd::send()` is now `public`, allowing to send arbitrary messages to statsd,
  like sending a batch of newline separated messages in one go.
- Support for [gauges](https://github.com/etsy/statsd#gauges) and [sets](https://github.com/etsy/statsd#sets) has been added.
- All messages are sent over one socket per `Statsd` instance, instead of creating a new one for each message,
  giving a significant speed improvement when reusing the same instance.
- The constructor now accepts an optional socket as its third argument.
- All methods return the result of `socket_sendto()`, so client code can deal with errors if desired.

## Authors

- John Crepezzi <john.crepezzi@gmail.com>
- Peter Schultz <peter.schultz@classmarkets.com>

## License

(The MIT License)

Copyright © 2012 John Crepezzi

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the ‘Software’), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED ‘AS IS’, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. MIT License.  See attached LICENSE
