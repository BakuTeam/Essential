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
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use function count;

class SetScorePacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SCORE_PACKET;

	public const TYPE_CHANGE = 0;
	public const TYPE_REMOVE = 1;

	//1.26.40+ per-entry action ordinals and their string discriminators
	private const ACTION_REMOVE = 0;
	private const ACTION_CHANGE_PLAYER = 1;
	private const ACTION_CHANGE_ENTITY = 2;
	private const ACTION_CHANGE_FAKE_PLAYER = 3;
	private const ACTION_STRINGS = [
		self::ACTION_REMOVE => "remove",
		self::ACTION_CHANGE_PLAYER => "changeplayer",
		self::ACTION_CHANGE_ENTITY => "changeentity",
		self::ACTION_CHANGE_FAKE_PLAYER => "changefakeplayer",
	];

	public int $type;
	/** @var ScorePacketEntry[] */
	public array $entries = [];

	/**
	 * @generate-create-func
	 * @param ScorePacketEntry[] $entries
	 */
	public static function create(int $type, array $entries) : self{
		$result = new self();
		$result->type = $type;
		$result->entries = $entries;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			//1.26.40 dropped the top-level change/remove byte in favour of a per-entry action (ordinal + matching
			//string), made the objective name optional, and only writes the score for non-remove actions.
			for($i = 0, $i2 = $in->getUnsignedVarInt(); $i < $i2; ++$i){
				$actionOrd = $in->getUnsignedVarInt();
				$actionStr = $in->getString();
				$expectedStr = self::ACTION_STRINGS[$actionOrd] ?? null;
				if($expectedStr === null || $actionStr !== $expectedStr){
					throw new PacketDecodeException("Unexpected score packet entry action $actionOrd/$actionStr");
				}
				$entry = new ScorePacketEntry();
				$entry->scoreboardId = $in->getVarLong();
				if($actionOrd === self::ACTION_REMOVE){
					$this->type = self::TYPE_REMOVE;
					$entry->objectiveName = $in->readOptional(fn() => $in->getString()) ?? "";
				}else{
					$this->type = self::TYPE_CHANGE;
					$entry->objectiveName = $in->getString();
					$entry->score = $in->getLInt();
					if($actionOrd === self::ACTION_CHANGE_FAKE_PLAYER){
						$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
						$entry->customName = $in->getString();
					}else{
						$entry->type = $actionOrd === self::ACTION_CHANGE_PLAYER ? ScorePacketEntry::TYPE_PLAYER : ScorePacketEntry::TYPE_ENTITY;
						$entry->actorUniqueId = $in->getActorUniqueId();
					}
				}
				$this->entries[] = $entry;
			}
			return;
		}
		$this->type = $in->getByte();
		for($i = 0, $i2 = $in->getUnsignedVarInt(); $i < $i2; ++$i){
			$entry = new ScorePacketEntry();
			$entry->scoreboardId = $in->getVarLong();
			$entry->objectiveName = $in->getString();
			$entry->score = $in->getLInt();
			if($this->type !== self::TYPE_REMOVE){
				$entry->type = $in->getByte();
				switch($entry->type){
					case ScorePacketEntry::TYPE_PLAYER:
					case ScorePacketEntry::TYPE_ENTITY:
						$entry->actorUniqueId = $in->getActorUniqueId();
						break;
					case ScorePacketEntry::TYPE_FAKE_PLAYER:
						$entry->customName = $in->getString();
						break;
					default:
						throw new PacketDecodeException("Unknown entry type $entry->type");
				}
			}
			$this->entries[] = $entry;
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$out->putUnsignedVarInt(count($this->entries));
			foreach($this->entries as $entry){
				if($this->type === self::TYPE_REMOVE){
					$actionOrd = self::ACTION_REMOVE;
				}else{
					$actionOrd = match($entry->type){
						ScorePacketEntry::TYPE_PLAYER => self::ACTION_CHANGE_PLAYER,
						ScorePacketEntry::TYPE_ENTITY => self::ACTION_CHANGE_ENTITY,
						ScorePacketEntry::TYPE_FAKE_PLAYER => self::ACTION_CHANGE_FAKE_PLAYER,
						default => throw new \InvalidArgumentException("Unknown entry type $entry->type"),
					};
				}
				$out->putUnsignedVarInt($actionOrd);
				$out->putString(self::ACTION_STRINGS[$actionOrd]);
				$out->putVarLong($entry->scoreboardId);
				if($actionOrd === self::ACTION_REMOVE){
					$out->writeOptional($entry->objectiveName, fn(string $name) => $out->putString($name));
				}else{
					$out->putString($entry->objectiveName);
					$out->putLInt($entry->score);
					if($actionOrd === self::ACTION_CHANGE_FAKE_PLAYER){
						$out->putString($entry->customName ?? throw new \InvalidArgumentException("customName must be set for fake player entries"));
					}else{
						$out->putActorUniqueId($entry->actorUniqueId);
					}
				}
			}
			return;
		}
		$out->putByte($this->type);
		$out->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$out->putVarLong($entry->scoreboardId);
			$out->putString($entry->objectiveName);
			$out->putLInt($entry->score);
			if($this->type !== self::TYPE_REMOVE){
				$out->putByte($entry->type);
				switch($entry->type){
					case ScorePacketEntry::TYPE_PLAYER:
					case ScorePacketEntry::TYPE_ENTITY:
						$out->putActorUniqueId($entry->actorUniqueId);
						break;
					case ScorePacketEntry::TYPE_FAKE_PLAYER:
						$out->putString($entry->customName);
						break;
					default:
						throw new \InvalidArgumentException("Unknown entry type $entry->type");
				}
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSetScore($this);
	}
}
