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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

final class UpdateSubChunkBlocksPacketEntry{
	public function __construct(
		private BlockPosition $blockPosition,
		private int $blockRuntimeId,
		private int $flags,
		//These two fields are useless 99.9% of the time; they are here to allow this packet to provide UpdateBlockSyncedPacket functionality.
		private int $syncedUpdateActorUniqueId,
		private int $syncedUpdateType
	){}

	public static function simple(BlockPosition $blockPosition, int $blockRuntimeId) : self{
		return new self($blockPosition, $blockRuntimeId, UpdateBlockPacket::FLAG_NETWORK, 0, 0);
	}

	public function getBlockPosition() : BlockPosition{ return $this->blockPosition; }

	public function getBlockRuntimeId() : int{ return $this->blockRuntimeId; }

	public function getFlags() : int{ return $this->flags; }

	public function getSyncedUpdateActorUniqueId() : int{ return $this->syncedUpdateActorUniqueId; }

	public function getSyncedUpdateType() : int{ return $this->syncedUpdateType; }

	public static function read(PacketSerializer $in) : self{
		$blockPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$blockRuntimeId = $in->getUnsignedVarInt();
		$updateFlags = $in->getUnsignedVarInt();
		$syncedUpdateActorUniqueId = $in->getUnsignedVarLong(); //this can't use the standard method because it's unsigned as opposed to the usual signed... !!!!!!
		$syncedUpdateType = $in->getUnsignedVarInt(); //this isn't even consistent with UpdateBlockSyncedPacket?!

		return new self($blockPosition, $blockRuntimeId, $updateFlags, $syncedUpdateActorUniqueId, $syncedUpdateType);
	}

	public function write(PacketSerializer $out) : void{
		$out->putBlockPosition($this->blockPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$out->putUnsignedVarInt($this->blockRuntimeId);
		$out->putUnsignedVarInt($this->flags);
		$out->putUnsignedVarLong($this->syncedUpdateActorUniqueId);
		$out->putUnsignedVarInt($this->syncedUpdateType);
	}
}
