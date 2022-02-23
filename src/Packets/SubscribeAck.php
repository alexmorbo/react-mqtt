<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;

use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * A SUBACK Packet is sent by the Server to the Client to confirm receipt and processing of a SUBSCRIBE Packet.
 */
class SubscribeAck extends ControlPacket
{
    const EVENT = 'SUBSCRIBE_ACK';

    /**
     * @var int
     */
    protected $qos;

    /**
     * @var int
     */
    protected $packetId;

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_SUBACK;
    }

    /**
     * @param VersionInterface $version
     * @param string $rawInput
     * @return ConnectionAck|ControlPacket
     */
    public static function parse(VersionInterface $version, $rawInput)
    {
        $packet = new static($version);

        $length = ord($rawInput[1]);
        $message = substr($rawInput, 2, $length - 1);
        $data = unpack("n*", $message);
        $packet->setPacketId($data[1]);
        $packet->setQos(ord(substr($rawInput, $length+1)));

        return $packet;
    }

    public function setQoS(int $qos)
    {
        $this->qos = $qos;
    }

    public function getQos(): int
    {
        return $this->qos;
    }

    public function setPacketId(int $packetId)
    {
        $this->packetId = $packetId;
    }

    public function getPacketId(): int
    {
        return $this->packetId;
    }
}