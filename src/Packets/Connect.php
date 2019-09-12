<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Packets;


use Morbo\React\Mqtt\Protocols\VersionInterface;

/**
 * After a Network Connection is established by a Client to a Server, the
 * first Packet sent from the Client to the Server MUST be a CONNECT Packet.
 */
class Connect extends ControlPacket
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var null|string
     */
    protected $username = null;

    /**
     * @var null|string
     */
    protected $password = null;

    /**
     * @var bool
     */
    protected $cleanSession = true;

    /**
     * @var array
     */
    protected $will = [
        'active' => false,
        'message' => null,
        'topic' => null,
        'qos' => null,
        'retain' => null,
    ];

    /**
     * @var null|string
     */
    protected $willTopic;

    /**
     * @var null|string
     */
    protected $willMessage;

    /**
     * @var bool|null
     */
    protected $willQos;

    /**
     * @var null
     */
    protected $willRetain;

    /**
     * @var int
     */
    private $keepAlive;

    public function __construct(
        VersionInterface $version,
        $username = null,
        $password = null,
        $clientId = null,
        $cleanSession = true,
        $will = [],
        $keepAlive = 0
    )
    {
        parent::__construct($version);
        $this->clientId = $clientId;
        $this->username = $username;
        $this->password = $password;
        $this->cleanSession = (bool) $cleanSession;
        if ($will) {
            $this->will = $will;
        }
        $this->keepAlive = $keepAlive;
    }

    /**
     * @return int
     */
    public function getControlPacketType()
    {
        return ControlPacketType::MQTT_CONNECT;
    }

    protected function buildPayload()
    {
        // Byte 1 - MSB
        $this->addRawToPayLoad(chr(ControlPacketType::MOST_SIGNIFICANT_BYTE));
        // Byte 2 - LSB length
        $this->addRawToPayLoad(chr($this->version->getProtocolVersion()));
        // Byte 3,4,5,6 - Identifier
        $this->addRawToPayLoad($this->version->getProtocolIdentifierString());
        // Byte 7 - Protocol level
        $this->addRawToPayLoad(chr($this->version->getProtocolVersion()));

        $connectFlags = 0;
        if ($this->cleanSession) {
            $connectFlags += 0x02;
        }
        if ($this->will['active']) {
            $connectFlags += 0x04;
            if ($this->will['qos']) {
                $connectFlags += ($this->will['active'] << 3);
            }
            if ($this->will['retain']) {
                $connectFlags += 0x20;
            }
        }
        if ($this->username) {
            $connectFlags += 0x80;
            if ($this->password) {
                $connectFlags += 0x40;
            }
        }
        // Connect flags
        $this->addRawToPayLoad(chr($connectFlags));
        // Keepalive (MSB)
        $this->addRawToPayLoad(chr($this->keepAlive >> 8));
        // Keepalive (LSB)
        $this->addRawToPayLoad(chr($this->keepAlive & 0xff));
        if (empty($this->clientId)) {
            $this->clientId = 'react-'.uniqid();
        }
        if ($this->clientId) {
            $this->addRawToPayLoad(
                $this->createPayload($this->clientId)
            );
        }
        if ($this->will['active']) {
            $this->addRawToPayLoad(
                $this->createPayload($this->will['topic'])
            );
            $this->addRawToPayLoad(
                $this->createPayload($this->will['message'])
            );
        }
        if ($this->username) {
            $this->addRawToPayLoad(
                $this->createPayload($this->username)
            );

            if ($this->password) {
                $this->addRawToPayLoad(
                    $this->createPayload($this->password)
                );
            }
        }

        return $this->payload;
    }
}