# ReactPHP MQTT Client

**react-mqtt** is an MQTT client library for PHP.

Its based on the reactPHP socket-client and added the MQTT protocol
specific functions.
Also based on https://github.com/oliverlorenz/phpMqttClient

## Goal

Goal of this project is easy to use MQTT client for PHP in a modern architecture without using any php modules.
Currently, only protocol version 4 (mqtt 3.1.1) is implemented.
* Protocol specifications: http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/csprd02/mqtt-v3.1.1-csprd02.html

## Example library initial
```php
// mqtt.php

use Morbo\React\Mqtt\Client;
use Morbo\React\Mqtt\ConnectionOptions;
use Morbo\React\Mqtt\Protocols\Version4;

require_once __DIR__ . '/vendor/autoload.php';

// Creating Event Loop
$loop = React\EventLoop\Factory::create();

// Connection configuration
$config = [
    'host' => 'localhost',
    'port' => 1883,
//    'options' => new ConnectionOptions([
//        'username' => 'auth_user',
//        'password' => 'auth_password',
//        'clientId' => 'react_client', // default is 'react-'.uniqid()
//        'cleanSession' => true, // default is true
//        'cleanSession' => true, // default is true
// .      'willTopic' => '',
// .      'willMessage' => '',
// .      'willQos' => '',
// .      'willRetain' => '',
// .      'keepAlive' => 60, // default is 60
//    ])
];

$mqtt = new Client($loop, new Version4());

```


## Example publish

```php
use React\Socket\ConnectionInterface;

require 'mqtt.php';

$connection = $mqtt->connect($config['host'], $config['port'], $config['options']);

$connection->then(function (ConnectionInterface $stream) use ($mqtt, $loop) {
    /**
     * Stop loop, when client disconnected from mqtt server
     */
    $stream->on('end', function () use ($loop) {
        $loop->stop();
    });

    $data = [
        'foo' => 'bar',
        'bar' => 'baz',
        'time' => time(),
    ];

    $qos = Morbo\React\Mqtt\Packets\QoS\Levels::AT_MOST_ONCE_DELIVERY;  // 0

    $mqtt->publish($stream, 'foo/bar', json_encode($data), $qos)->then(function (ConnectionInterface $stream) use ($mqtt) {
        /**
         * Disconnect when published
         */
        $mqtt->disconnect($stream);
    });
});


$loop->run();
```

## Example subscribe


```php
use Morbo\React\Mqtt\Packets;
use React\Socket\ConnectionInterface;

require 'mqtt.php';

$connection = $mqtt->connect($config['host'], $config['port'], $config['options']);

$connection->then(function (ConnectionInterface $stream) use ($mqtt) {
    $qos = Morbo\React\Mqtt\Packets\QoS\Levels::AT_MOST_ONCE_DELIVERY;  // 0
    $mqtt->subscribe($stream, 'foo/bar', $qos)->then(function (ConnectionInterface $stream) use ($qos) {
        // Success subscription
        $stream->on(Packets\Publish::EVENT, function(Packets\Publish $publish) {
            var_dump($publish);
        });
    }, function ($error) {
        // Subscription error
    });
});

$loop->run();
```

## Avaiable methods
Currently works:
* connect (clean session, will options, keepalive, connection authorization)
* disconnect
* publish
* subscribe