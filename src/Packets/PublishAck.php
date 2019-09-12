<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * A PUBACK Packet is the response to a PUBLISH Packet with QoS level 1.
 */
class PublishAck extends ControlPacket
{
    const EVENT = 'PUBLISH_ASC';

    /**
     * @var int
     */
    protected $packetId;

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_PUBACK;
    }

    public static function parse(VersionInterface $version, $rawInput)
    {
        $packet = new static($version);

        $data = unpack('n', substr($rawInput, 2));
        $packet->setPacketId($data[1]);

        return $packet;
    }

    /**
     * @param $messageId
     */
    public function setPacketId($messageId)
    {
        $this->packetId = $messageId;
    }

    /**
     * @return int
     */
    public function getPacketId(): int
    {
        return $this->packetId;
    }
}