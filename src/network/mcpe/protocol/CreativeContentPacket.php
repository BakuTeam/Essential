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
use pocketmine\network\mcpe\protocol\types\inventory\CreativeGroupEntry;
use pocketmine\network\mcpe\protocol\types\inventory\CreativeItemEntry;
use function count;

class CreativeContentPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CREATIVE_CONTENT_PACKET;

	public const CATEGORY_CONSTRUCTION = 1;
	public const CATEGORY_NATURE = 2;
	public const CATEGORY_EQUIPMENT = 3;
	public const CATEGORY_ITEMS = 4;

	/** @var CreativeGroupEntry[] */
	private array $groups;
	/** @var CreativeItemEntry[] */
	private array $items;

	/**
	 * @generate-create-func
	 * @param CreativeGroupEntry[] $groups
	 * @param CreativeItemEntry[]  $items
	 */
	public static function create(array $groups, array $items) : self{
		$result = new self();
		$result->groups = $groups;
		$result->items = $items;
		return $result;
	}

	/** @return CreativeGroupEntry[] */
	public function getGroups() : array{ return $this->groups; }

	/** @return CreativeItemEntry[] */
	public function getItems() : array{ return $this->items; }

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_60){
			$this->groups = [];
			for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
				$this->groups[] = CreativeGroupEntry::read($in);
			}
		}

		$this->items = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$this->items[] = CreativeItemEntry::read($in);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_60){
			$out->putUnsignedVarInt(count($this->groups));
			foreach($this->groups as $entry){
				$entry->write($out);
			}
		}

		$out->putUnsignedVarInt(count($this->items));
		foreach($this->items as $entry){
			$entry->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCreativeContent($this);
	}
}
