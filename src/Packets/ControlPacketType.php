<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


class ControlPacketType
{
    // MQTT control packet types (here left shifted 4 bits)
    const MQTT_CONNECT     = 0x10; // Client request to connect to Server
    const MQTT_CONNACK     = 0x20; // Connect acknowledgment
    const MQTT_PUBLISH     = 0x30; // Publish message
    const MQTT_PUBACK      = 0x40; // Publish acknowledgment
    const MQTT_PUBREC      = 0x50; // Publish received (assured delivery part 1)
    const MQTT_PUBREL      = 0x62; // Publish release (assured delivery part 2)
    const MQTT_PUBCOMP     = 0x70; // Publish complete (assured delivery part 3)
    const MQTT_SUBSCRIBE   = 0x80; // Client subscribe request
    const MQTT_SUBACK      = 0x90; // Subscribe acknowledgment
    const MQTT_UNSUBSCRIBE = 0xa0; // Unsubscribe request
    const MQTT_UNSUBACK    = 0xb0; // Unsubscribe acknowledgment
    const MQTT_PINGREQ     = 0xc0; // PING request
    const MQTT_PINGRESP    = 0xd0; // PING response
    const MQTT_DISCONNECT  = 0xe0; // Client is disconnecting

    const MOST_SIGNIFICANT_BYTE = 0x00;
}