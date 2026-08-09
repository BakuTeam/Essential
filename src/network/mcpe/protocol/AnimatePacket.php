<?php

/*
 *
 *  _____                    _   _       _
 * | ____|___ ___  ___ _ __ | |_(_) __ _| |
 * |  _| / __/ __|/ _ \ '_ \| __| |/ _` | |
 * | |___\__ \__ \  __/ | | | |_| | (_| | |
 * |_____|___/___/\___|_| |_|\__|_|\__,_|_|
 *
 * Essential — PocketMine-MP Fork
 * Supported MCPE/Bedrock versions: 1.12, 1.16 - 1.26.x
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Essential Team
 * @link https://github.com/BakuTeam/Essential
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

class AnimatePacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::ANIMATE_PACKET;

	public const ACTION_SWING_ARM = 1;

	public const ACTION_STOP_SLEEP = 3;
	public const ACTION_CRITICAL_HIT = 4;
	public const ACTION_MAGICAL_CRITICAL_HIT = 5;
	public const ACTION_ROW_RIGHT = 128;
	public const ACTION_ROW_LEFT = 129;

	public int $action;
	public int $actorRuntimeId;
	public float $data = 0.0;
	public ?string $swingSource = null;

	public static function create(int $actorRuntimeId, int $actionId, float $data = 0.0, ?string $swingSource = null) : self{
		$result = new self();
		$result->actorRuntimeId = $actorRuntimeId;
		$result->action = $actionId;
		$result->data = $data;
		$result->swingSource = $swingSource;
		return $result;
	}

	public static function boatHack(int $actorRuntimeId, int $actionId, float $data) : self{

		if($actionId !== self::ACTION_ROW_LEFT && $actionId !== self::ACTION_ROW_RIGHT){
			throw new \InvalidArgumentException("Invalid actionId for boatHack: $actionId");
		}

		$result = self::create($actorRuntimeId, $actionId);
		$result->data = $data;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->action = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130 ? $in->getByte() : $in->getVarInt();
		$this->actorRuntimeId = $in->getActorRuntimeId();

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_120 || ($this->action === self::ACTION_ROW_LEFT || $this->action === self::ACTION_ROW_RIGHT)){
			$this->data = $in->getLFloat();
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->swingSource = $in->readOptional($in->getString(...));
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putByte($this->action);
		}else{
			$out->putVarInt($this->action);
		}
		$out->putActorRuntimeId($this->actorRuntimeId);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_120 || ($this->action === self::ACTION_ROW_LEFT || $this->action === self::ACTION_ROW_RIGHT)){
			$out->putLFloat($this->data);
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->writeOptional($this->swingSource, $out->putString(...));
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleAnimate($this);
	}
}
