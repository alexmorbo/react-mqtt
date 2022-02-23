<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * A PUBLISH Control Packet is sent from a Client to a Server or from
 * Server to a Client to transport an Application Message.
 */
class Publish extends ControlPacket
{
    const EVENT = 'PUBLISH';

    /**
     * @var int
     */
    protected $packetId;

    /**
     * @var string
     */
    protected $topic = '';

    /**
     * @var int
     */
    protected $qos = 0;

    /**
     * @var bool
     */
    protected $dup = false;

    /**
     * @var bool
     */
    protected $retain = false;

    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_PUBLISH + ($this->dup << 3) + ($this->qos << 1) + $this->retain;
    }

    public function buildPayload()
    {
        $this->packetId = $this->version->getNextPacketId();

        // Topic
        $this->addRawToPayLoad(
            $this->createPayload($this->topic)
        );

        if ($this->qos >= QoS\Levels::AT_LEAST_ONCE_DELIVERY) {
            $this->addRawToPayLoad(
                $this->version->getPacketIdPayload($this->packetId)
            );
        }

//        $this->addRawToPayLoad(
//            $this->message
//        );

        return $this->payload;
    }

    public static function parse(VersionInterface $version, $rawInput, $bytesRead)
    {
        $packet = new static($version);

        $flags = ord($rawInput[0]) & 0x0f;
        $packet->setDup($flags == 0x80);
        $packet->setRetain($flags == 0x01);
        $packet->setQos(($flags >> 1) & 0x03);

        $topicLength = (ord($rawInput[$bytesRead]) << 8) + ord($rawInput[$bytesRead + 1]);
        $packet->setTopic(substr($rawInput, 2 + $bytesRead, $topicLength));
        $payload = substr($rawInput, $bytesRead + 2 + $topicLength);

        if ($packet->getQos() == QoS\Levels::AT_MOST_ONCE_DELIVERY) {
            // no packet id for QoS 0, the payload is the message
            $packet->setPayload($payload);
        } else {
            if (strlen($payload) >= 2) {
                $packet->setPacketId((ord($payload[0]) << 8) + ord($payload[1]));
                // skip packet id (2 bytes) for QoS 1 and 2
                $packet->setPayload(substr($payload, 2));
            }
        }

        return $packet;
    }

    /**
     * @param $topic
     * @return $this
     */
    public function setTopic(string $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @param string $payload
     * @return $this
     */
    public function setPayload(string $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @param int $packetId
     */
    public function setPacketId(int $packetId)
    {
        $this->packetId = $packetId;
    }

    /**
     * @param int $qos 0,1,2
     */
    public function setQos($qos)
    {
        $this->qos = $qos;
    }

    /**
     * @param bool $dup
     */
    public function setDup($dup)
    {
        $this->dup = $dup;
    }

    /**
     * @param bool $retain
     */
    public function setRetain($retain)
    {
        $this->retain = $retain;

        return $this;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @return int
     */
    public function getQos(): int
    {
        return $this->qos;
    }

    /**
     * @return int
     */
    public function getPacketId(): int
    {
        return $this->packetId;
    }
}