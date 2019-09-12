<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;

/**
 * The SUBSCRIBE Packet is sent from the Client to the Server to create
 * one or more Subscriptions.
 */
class Subscribe extends ControlPacket
{
    const EVENT = 'SUBSCRIBE';

    /**
     * @var int
     */
    protected $packetId;

    /**
     * @var string
     */
    protected $topic;

    /**
     * @var int
     */
    protected $qos;

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_SUBSCRIBE + 0x02;
    }

    public function buildPayload()
    {
        $this->packetId = $this->version->getNextPacketId();
        $this->addRawToPayLoad(
            $this->version->getPacketIdPayload($this->packetId)
        );

        // Topic
        $this->addRawToPayLoad(
            $this->createPayload($this->topic)
        );

        // QoS
        $this->addRawToPayLoad(
            chr($this->qos)
        );

        return $this->payload;
    }

    /**
     * @param string $topic
     * @param int $qos
     */
    public function addSubscription(string $topic, int $qos = 0)
    {
        $this->topic = $topic;
        $this->qos = $qos;
    }

    public function getPacketId(): int
    {
        return $this->packetId;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getQoS(): int
    {
        return $this->qos;
    }
}