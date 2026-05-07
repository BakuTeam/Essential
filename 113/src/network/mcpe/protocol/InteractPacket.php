<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

class InteractPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::INTERACT_PACKET;

	public const ACTION_LEAVE_VEHICLE = 3;
	public const ACTION_MOUSEOVER = 4;
	public const ACTION_OPEN_NPC = 5;
	public const ACTION_OPEN_INVENTORY = 6;

	public int $action;
	public int $targetActorRuntimeId;
	public float $x;
	public float $y;
	public float $z;
	public ?Vector3 $position = null;

	protected function decodePayload(PacketSerializer $in) : void{
		$this->action = $in->getByte();
		$this->targetActorRuntimeId = $in->getActorRuntimeId();

		if ($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130) {
			$this->position = $in->readOptional(fn() => $in->getVector3());
			if($this->position !== null){
				$this->x = $this->position->x;
				$this->y = $this->position->y;
				$this->z = $this->position->z;
			}
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0 && ($this->action === self::ACTION_MOUSEOVER || $this->action === self::ACTION_LEAVE_VEHICLE)){
			$this->x = $in->getLFloat();
			$this->y = $in->getLFloat();
			$this->z = $in->getLFloat();
			$this->position = new Vector3($this->x, $this->y, $this->z);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->action);
		$out->putActorRuntimeId($this->targetActorRuntimeId);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$position = $this->position ?? (isset($this->x, $this->y, $this->z) ? new Vector3($this->x, $this->y, $this->z) : null);
			$out->writeOptional($position, fn(Vector3 $v) => $out->putVector3($v));
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0 && ($this->action === self::ACTION_MOUSEOVER || $this->action === self::ACTION_LEAVE_VEHICLE)){
			$out->putLFloat($this->x);
			$out->putLFloat($this->y);
			$out->putLFloat($this->z);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInteract($this);
	}
}
