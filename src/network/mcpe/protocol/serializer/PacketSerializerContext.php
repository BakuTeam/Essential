<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
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
