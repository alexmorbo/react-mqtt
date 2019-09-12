<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Protocols;


class Version4 implements VersionInterface
{
    /**
     * @var int
     */
    protected $packetId;

    public function __construct()
    {
        // Reduce risk of creating duplicate ids in sequential sessions
        $this->packetId = rand(1, 100) * 100;
    }

    public function getProtocolIdentifierString(): string
    {
        return 'MQTT';
    }

    public function getProtocolVersion(): int
    {
        return 0x04;
    }

    public function getNextPacketId(): int
    {
        return ($this->packetId = ($this->packetId + 1) & 0xffff);
    }

    public function getPacketIdPayload(int $packetId) {
        return chr(($packetId & 0xff00)>>8) . chr($packetId & 0xff);
    }
}