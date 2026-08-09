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

namespace pocketmine\network\mcpe\protocol\serializer;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

/**
 * Contains information for a packet serializer specific to a given game session needed for packet encoding and decoding,
 * such as a dictionary of item runtime IDs to their internal string IDs.
 */
final class PacketSerializerContext{
	public function __construct(
		private ItemTypeDictionary $itemDictionary,
		private int $protocolId = ProtocolInfo::CURRENT_PROTOCOL
	){}

	public function getItemDictionary() : ItemTypeDictionary{ return $this->itemDictionary; }

	public function getProtocolId() : int{ return $this->protocolId; }
}
