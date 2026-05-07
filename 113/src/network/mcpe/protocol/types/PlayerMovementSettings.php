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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class PlayerMovementSettings{
	public function __construct(
		private ServerAuthMovementMode $movementType,
		private int $rewindHistorySize,
		private bool $serverAuthoritativeBlockBreaking
	){}

	public function getMovementType() : ServerAuthMovementMode{ return $this->movementType; }

	public function getRewindHistorySize() : int{ return $this->rewindHistorySize; }

	public function isServerAuthoritativeBlockBreaking() : bool{ return $this->serverAuthoritativeBlockBreaking; }

	public static function read(PacketSerializer $in) : self{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
			if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_80){
				$movementType = ServerAuthMovementMode::fromPacket($in->getVarInt());
			}
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_210){
				$rewindHistorySize = $in->getVarInt();
				$serverAuthBlockBreaking = $in->getBool();
			}
		}else{
			$movementType = $in->getBool() ? ServerAuthMovementMode::SERVER_AUTHORITATIVE_V2 : 0;
		}
		
		return new self($movementType, $rewindHistorySize ?? 0, $serverAuthBlockBreaking ?? false);
	}

	public function write(PacketSerializer $out) : void{
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_80){
			$out->putVarInt($this->movementType->value);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_210){
			$out->putVarInt($this->rewindHistorySize);
			$out->putBool($this->serverAuthoritativeBlockBreaking);
		}
	}
}
