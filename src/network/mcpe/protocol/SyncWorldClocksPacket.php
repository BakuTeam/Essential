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
