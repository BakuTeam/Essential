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
use pocketmine\network\mcpe\protocol\types\ScoreboardIdentityPacketEntry;
use function count;

class SetScoreboardIdentityPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SCOREBOARD_IDENTITY_PACKET;

	public const TYPE_REGISTER_IDENTITY = 0;
	public const TYPE_CLEAR_IDENTITY = 1;

	public int $type;
	/** @var ScoreboardIdentityPacketEntry[] */
	public array $entries = [];

	/**
	 * @generate-create-func
	 * @param ScoreboardIdentityPacketEntry[] $entries
	 */
	public static function create(int $type, array $entries) : self{
		$result = new self();
		$result->type = $type;
		$result->entries = $entries;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->type = $in->getByte();
		$since1_26_40 = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40;
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$entry = new ScoreboardIdentityPacketEntry();
			$entry->scoreboardId = $in->getVarLong();
			if($since1_26_40){
				//1.26.40 writes the actor unique id as an optional regardless of the register/clear type
				$entry->actorUniqueId = $in->readOptional(fn() => $in->getActorUniqueId());
			}elseif($this->type === self::TYPE_REGISTER_IDENTITY){
				$entry->actorUniqueId = $in->getActorUniqueId();
			}

			$this->entries[] = $entry;
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->type);
		$since1_26_40 = $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40;
		$out->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$out->putVarLong($entry->scoreboardId);
			if($since1_26_40){
				$out->writeOptional($this->type === self::TYPE_REGISTER_IDENTITY ? $entry->actorUniqueId : null, fn(int $id) => $out->putActorUniqueId($id));
			}elseif($this->type === self::TYPE_REGISTER_IDENTITY){
				$out->putActorUniqueId($entry->actorUniqueId);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSetScoreboardIdentity($this);
	}
}
