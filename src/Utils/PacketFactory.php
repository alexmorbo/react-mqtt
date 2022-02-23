<?php

declare(strict_types=1);

namespace Morbo\React\Mqtt\Utils;


use Morbo\React\Mqtt\Packets\ConnectionAck;
use Morbo\React\Mqtt\Packets\ControlPacketType;
use Morbo\React\Mqtt\Packets\Disconnect;
use Morbo\React\Mqtt\Packets\PingResponse;
use Morbo\React\Mqtt\Packets\Publish;
use Morbo\React\Mqtt\Packets\PublishAck;
use Morbo\React\Mqtt\Packets\PublishComplete;
use Morbo\React\Mqtt\Packets\PublishReceived;
use Morbo\React\Mqtt\Packets\PublishRelease;
use Morbo\React\Mqtt\Packets\SubscribeAck;
use Morbo\React\Mqtt\Packets\UnsubscribeAck;
use Morbo\React\Mqtt\Protocols\VersionInterface;
use Morbo\React\Mqtt\Protocols\VersionViolation;

class PacketFactory
{
    public static function getNextPacket(VersionInterface $version, $remainingData)
    {
        while (isset($remainingData{1})) {
            $bytesRead = 1;
            $remainingLength = 0;
            $multiplier = 1;
            do {
                if ($bytesRead > 4) {
                    return false;
                }
                $str = $remainingData[$bytesRead];
                if ($str === false || strlen($str) != 1) {
                    return false;
                }
                $byte = ord($str[0]);
                $remainingLength += ($byte & 0x7f) * $multiplier;
                $isContinued = ($byte & 0x80);
                if ($isContinued) {
                    $multiplier *= 128;
                }
                $bytesRead++;
            } while ($isContinued);

            $packetLength = 2 + $remainingLength;
            $nextPacketData = substr($remainingData, 0);
            $remainingData = substr($remainingData, $packetLength);

            yield self::getByMessage($version, $bytesRead, $nextPacketData);
        }
    }

    private static function getByMessage(VersionInterface $version, $bytesRead, $input)
    {
        $controlPacketType = ord($input[0]);

        switch ($controlPacketType) {
            case ControlPacketType::MQTT_CONNACK:
                return ConnectionAck::parse($version, $input);
            case ControlPacketType::MQTT_PINGRESP:
                return PingResponse::parse($version, $input);
            case ControlPacketType::MQTT_SUBACK:
                return SubscribeAck::parse($version, $input);
            case ControlPacketType::MQTT_UNSUBACK:
                return UnsubscribeAck::parse($version, $input);
            case ControlPacketType::MQTT_DISCONNECT:
                return Disconnect::parse($version, $input);
            case ControlPacketType::MQTT_PUBLISH:        // QoS - 0
            case ControlPacketType::MQTT_PUBLISH + 0x02: // QoS - 1
            case ControlPacketType::MQTT_PUBLISH + 0x04: // QoS - 2
                return Publish::parse($version, $input, $bytesRead);
            case ControlPacketType::MQTT_PUBACK:
                return PublishAck::parse($version, $input);
            case ControlPacketType::MQTT_PUBREC:
                return PublishReceived::parse($version, $input);
            case ControlPacketType::MQTT_PUBREL:
                return PublishRelease::parse($version, $input);
            case ControlPacketType::MQTT_PUBCOMP:
                return PublishComplete::parse($version, $input);

        }

        throw new VersionViolation('Unexpected packet type: ' . $controlPacketType);
    }
}