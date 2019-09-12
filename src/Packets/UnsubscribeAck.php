<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * The UNSUBACK Packet is sent by the Server to the Client to confirm
 * receipt of an UNSUBSCRIBE Packet.
 */
class UnsubscribeAck extends ControlPacket
{
    const EVENT = 'UNSUBSCRIBE_ACK';

    /**
     * @var int
     */
    protected $packetId;

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_UNSUBACK;
    }

    /**
     * @param VersionInterface $version
     * @param string $rawInput
     * @return ConnectionAck|ControlPacket
     */
    public static function parse(VersionInterface $version, $rawInput)
    {
        $packet = new static($version);

        $length = ord($rawInput{1});
        $message = substr($rawInput, 2);
        $data = unpack("n*", $message);
        $packet->setPacketId($data[1]);

        return $packet;
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