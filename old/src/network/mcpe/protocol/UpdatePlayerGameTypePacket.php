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

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\GameMode;

class UpdatePlayerGameTypePacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_PLAYER_GAME_TYPE_PACKET;

	/** @see GameMode */
	private int $gameMode;
	private int $playerActorUniqueId;
	private int $tick;

	/**
	 * @generate-create-func
	 */
	public static function create(int $gameMode, int $playerActorUniqueId, int $tick) : self{
		$result = new self;
		$result->gameMode = $gameMode;
		$result->playerActorUniqueId = $playerActorUniqueId;
		$result->tick = $tick;
		return $result;
	}

	public function getGameMode() : int{ return $this->gameMode; }

	public function getPlayerActorUniqueId() : int{ return $this->playerActorUniqueId; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->gameMode = $in->getVarInt();
		$this->playerActorUniqueId = $in->getActorUniqueId();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
			$this->tick = $in->getUnsignedVarLong();
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_80){
			$this->tick = $in->getUnsignedVarInt();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putVarInt($this->gameMode);
		$out->putActorUniqueId($this->playerActorUniqueId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
			$out->putUnsignedVarLong($this->tick);
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_80){
			$out->putUnsignedVarInt($this->tick);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleUpdatePlayerGameType($this);
	}
}
