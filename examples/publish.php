<?php

use React\Socket\ConnectionInterface;

require_once __DIR__ . '/mqtt.php';

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