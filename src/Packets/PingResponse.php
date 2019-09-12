<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * A PINGRESP Packet is sent by the Server to the Client in response to a PINGREQ Packet. It indicates that the Server is alive.
 */
class PingResponse extends ControlPacket
{
    const EVENT = 'PING_RESPONSE';

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_PINGRESP;
    }

    public static function parse(VersionInterface $version, $rawInput)
    {
        $packet = new static($version);

        return $packet;
    }
}