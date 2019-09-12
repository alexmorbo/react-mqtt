<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


/**
 * The DISCONNECT Packet is the final Control Packet sent from the Client
 * to the Server. It indicates that the Client is disconnecting cleanly.
 */
class Disconnect extends ControlPacket
{
    const EVENT = 'DISCONNECT';

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_DISCONNECT;
    }

    public function buildPayload()
    {
        return $this->payload;
    }
}