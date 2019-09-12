<?php

use Morbo\React\Mqtt\Packets;
use React\Socket\ConnectionInterface;

require_once __DIR__ . '/mqtt.php';

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