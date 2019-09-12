<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;

use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * The CONNACK Packet is the packet sent by the Server in response to
 * a CONNECT Packet received from a Client.
 */
class ConnectionAck extends ControlPacket
{
    const EVENT = 'CONNECTION_ACK';

    const CONNECTION_SUCCESS = 0;
    const CONNECTION_UNACCEPTABLE_PROTOCOL_VERSION = 1;
    const CONNECTION_IDENTIFIER_REJECTED = 2;
    const CONNECTION_SERVER_UNAVAILABLE = 3;
    const CONNECTION_BAD_CREDENTIALS = 4;
    const CONNECTION_AUTH_ERROR = 5;

    /**
     * @var bool
     */
    protected $connected;

    /**
     * @var int
     */
    protected $statusCode;

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_CONNACK;
    }

    public function setConnected(bool $status)
    {
        $this->connected = $status;
    }

    public function getConnected(): bool
    {
        return $this->connected;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function parse(VersionInterface $version, $rawInput)
    {
        $packet = new static($version);

        $statusCode = ord(substr($rawInput, 3));
        $packet->setStatusCode($statusCode);
        if ($statusCode === self::CONNECTION_SUCCESS) {
            $packet->setConnected(true);
        } else {
            $packet->setConnected(false);
        }

        return $packet;
    }
}