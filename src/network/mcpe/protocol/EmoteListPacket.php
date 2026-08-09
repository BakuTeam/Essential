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
use Ramsey\Uuid\UuidInterface;
use function count;

class EmoteListPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::EMOTE_LIST_PACKET;

	private int $playerActorRuntimeId;
	/** @var UuidInterface[] */
	private array $emoteIds;

	/**
	 * @generate-create-func
	 * @param UuidInterface[] $emoteIds
	 */
	public static function create(int $playerActorRuntimeId, array $emoteIds) : self{
		$result = new self();
		$result->playerActorRuntimeId = $playerActorRuntimeId;
		$result->emoteIds = $emoteIds;
		return $result;
	}

	public function getPlayerActorRuntimeId() : int{ return $this->playerActorRuntimeId; }

	/** @return UuidInterface[] */
	public function getEmoteIds() : array{ return $this->emoteIds; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->playerActorRuntimeId = $in->getActorRuntimeId();
		$this->emoteIds = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$this->emoteIds[] = $in->getUUID();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorRuntimeId($this->playerActorRuntimeId);
		$out->putUnsignedVarInt(count($this->emoteIds));
		foreach($this->emoteIds as $emoteId){
			$out->putUUID($emoteId);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleEmoteList($this);
	}
}
