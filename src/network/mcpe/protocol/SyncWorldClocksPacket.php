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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\SyncWorldClocksAddTimeMarker;
use pocketmine\network\mcpe\protocol\types\SyncWorldClocksInitializeRegistry;
use pocketmine\network\mcpe\protocol\types\SyncWorldClocksPayload;
use pocketmine\network\mcpe\protocol\types\SyncWorldClocksRemoveTimeMarker;
use pocketmine\network\mcpe\protocol\types\SyncWorldClocksSyncState;

class SyncWorldClocksPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SYNC_WORLD_CLOCKS_PACKET;

	private SyncWorldClocksPayload $payload;

	/**
	 * @generate-create-func
	 */
	public static function create(SyncWorldClocksPayload $payload) : self{
		$result = new self();
		$result->payload = $payload;
		return $result;
	}

	public function getPayload() : SyncWorldClocksPayload{ return $this->payload; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->payload = match($in->getUnsignedVarInt()){
			SyncWorldClocksSyncState::ID => SyncWorldClocksSyncState::read($in),
			SyncWorldClocksInitializeRegistry::ID => SyncWorldClocksInitializeRegistry::read($in),
			SyncWorldClocksAddTimeMarker::ID => SyncWorldClocksAddTimeMarker::read($in),
			SyncWorldClocksRemoveTimeMarker::ID => SyncWorldClocksRemoveTimeMarker::read($in),
			default => throw new PacketDecodeException("Unknown SyncWorldClocks type")
		};
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->payload->getTypeId());
		$this->payload->write($out);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSyncWorldClocks($this);
	}
}
