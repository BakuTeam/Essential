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

use pocketmine\color\Color;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use function count;

class PlayerListPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;

	public int $type;
	/** @var PlayerListEntry[] */
	public array $entries = [];

	/**
	 * @generate-create-func
	 * @param PlayerListEntry[] $entries
	 */
	private static function create(int $type, array $entries) : self{
		$result = new self();
		$result->type = $type;
		$result->entries = $entries;
		return $result;
	}

	/**
	 * @param PlayerListEntry[] $entries
	 */
	public static function add(array $entries) : self{
		return self::create(self::TYPE_ADD, $entries);
	}

	/**
	 * @param PlayerListEntry[] $entries
	 */
	public static function remove(array $entries) : self{
		return self::create(self::TYPE_REMOVE, $entries);
	}

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$count = $in->getUnsignedVarInt();
			for($i = 0; $i < $count; ++$i){
				$wireType = $in->getUnsignedVarInt(); //0 = remove, 1 = add
				$innerType = $in->getByte(); //0 = add, 1 = remove
				$expectedInnerType = $wireType === 1 ? 0 : 1;
				if(($wireType !== 0 && $wireType !== 1) || $innerType !== $expectedInnerType){
					throw new PacketDecodeException("Invalid 1.26.40 player-list entry type $wireType/$innerType");
				}
				$entry = new PlayerListEntry();
				if($wireType === 1){
					$this->type = self::TYPE_ADD;
					$entry->uuid = $in->getUUID();
					$entry->actorUniqueId = $in->getActorUniqueId();
					$entry->username = $in->getString();
					$entry->xboxUserId = $in->getString();
					$entry->platformChatId = $in->getString();
					$entry->buildPlatform = $in->getLInt();
					$entry->skinData = $in->getSkin();
					$entry->isTeacher = $in->getBool();
					$entry->isHost = $in->getBool();
					$entry->isSubClient = $in->getBool();
					$entry->color = Color::fromARGB($in->getLInt());
				}else{
					$this->type = self::TYPE_REMOVE;
					$entry->uuid = $in->getUUID();
				}
				$this->entries[] = $entry;
			}
			return;
		}
		$this->type = $in->getByte();
		$count = $in->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$entry = new PlayerListEntry();

			if($this->type === self::TYPE_ADD){
				$entry->uuid = $in->getUUID();
				$entry->actorUniqueId = $in->getActorUniqueId();
				$entry->username = $in->getString();
				$entry->xboxUserId = $in->getString();
				$entry->platformChatId = $in->getString();
				$entry->buildPlatform = $in->getLInt();
				$entry->skinData = $in->getSkin();
				$entry->isTeacher = $in->getBool();
				$entry->isHost = $in->getBool();
				if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
					$entry->isSubClient = $in->getBool();
					if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_80){
						$entry->color = Color::fromARGB($in->getLInt());
					}
				}
			}else{
				$entry->uuid = $in->getUUID();
			}

			$this->entries[$i] = $entry;
		}
		if($this->type === self::TYPE_ADD){
			for($i = 0; $i < $count; ++$i){
				$this->entries[$i]->skinData->setVerified($in->getBool());
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$out->putUnsignedVarInt(count($this->entries));
			foreach($this->entries as $entry){
				$isAdd = $this->type === self::TYPE_ADD;
				$out->putUnsignedVarInt($isAdd ? 1 : 0);
				$out->putByte($isAdd ? 0 : 1);
				if($isAdd){
					$out->putUUID($entry->uuid);
					$out->putActorUniqueId($entry->actorUniqueId);
					$out->putString($entry->username);
					$out->putString($entry->xboxUserId);
					$out->putString($entry->platformChatId);
					$out->putLInt($entry->buildPlatform);
					$out->putSkin($entry->skinData);
					$out->putBool($entry->isTeacher);
					$out->putBool($entry->isHost);
					$out->putBool($entry->isSubClient);
					$out->putLInt(($entry->color ?? new Color(255, 255, 255))->toARGB());
				}else{
					$out->putUUID($entry->uuid);
				}
			}
			return;
		}
		$out->putByte($this->type);
		$out->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			if($this->type === self::TYPE_ADD){
				$out->putUUID($entry->uuid);
				$out->putActorUniqueId($entry->actorUniqueId);
				$out->putString($entry->username);
				$out->putString($entry->xboxUserId);
				$out->putString($entry->platformChatId);
				$out->putLInt($entry->buildPlatform);
				$out->putSkin($entry->skinData);
				$out->putBool($entry->isTeacher);
				$out->putBool($entry->isHost);
				if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
					$out->putBool($entry->isSubClient);
					if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_80){
						$out->putLInt(($entry->color ?? new Color(255, 255, 255))->toARGB());
					}
				}
			}else{
				$out->putUUID($entry->uuid);
			}
		}
		if($this->type === self::TYPE_ADD){
			foreach($this->entries as $entry){
				$out->putBool($entry->skinData->isVerified());
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerList($this);
	}
}
