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
use pocketmine\network\mcpe\protocol\types\SubChunkPacketEntryWithCache as EntryWithBlobHash;
use pocketmine\network\mcpe\protocol\types\SubChunkPacketEntryWithCacheList as ListWithBlobHashes;
use pocketmine\network\mcpe\protocol\types\SubChunkPacketEntryWithoutCache as EntryWithoutBlobHash;
use pocketmine\network\mcpe\protocol\types\SubChunkPacketEntryWithoutCacheList as ListWithoutBlobHashes;
use pocketmine\network\mcpe\protocol\types\SubChunkPosition;
use function count;

class SubChunkPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SUB_CHUNK_PACKET;

	private int $dimension;
	private SubChunkPosition $baseSubChunkPosition;
	private ListWithBlobHashes|ListWithoutBlobHashes $entries;

	/**
	 * @generate-create-func
	 */
	public static function create(int $dimension, SubChunkPosition $baseSubChunkPosition, ListWithBlobHashes|ListWithoutBlobHashes $entries) : self{
		$result = new self();
		$result->dimension = $dimension;
		$result->baseSubChunkPosition = $baseSubChunkPosition;
		$result->entries = $entries;
		return $result;
	}

	public function isCacheEnabled() : bool{ return $this->entries instanceof ListWithBlobHashes; }

	public function getDimension() : int{ return $this->dimension; }

	public function getBaseSubChunkPosition() : SubChunkPosition{ return $this->baseSubChunkPosition; }

	public function getEntries() : ListWithBlobHashes|ListWithoutBlobHashes{ return $this->entries; }

	protected function decodePayload(PacketSerializer $in) : void{
		$cacheEnabled = $in->getBool();
		$this->dimension = $in->getVarInt();
		$this->baseSubChunkPosition = SubChunkPosition::read($in, $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40);

		$count = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? $in->getUnsignedVarInt() : $in->getLInt();
		if($cacheEnabled){
			$entries = [];
			for($i = 0; $i < $count; $i++){
				$entries[] = EntryWithBlobHash::read($in);
			}
			$this->entries = new ListWithBlobHashes($entries);
		}else{
			$entries = [];
			for($i = 0; $i < $count; $i++){
				$entries[] = EntryWithoutBlobHash::read($in);
			}
			$this->entries = new ListWithoutBlobHashes($entries);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBool($this->entries instanceof ListWithBlobHashes);
		$out->putVarInt($this->dimension);
		$this->baseSubChunkPosition->write($out, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$out->putUnsignedVarInt(count($this->entries->getEntries()));
		}else{
			$out->putLInt(count($this->entries->getEntries()));
		}

		foreach($this->entries->getEntries() as $entry){
			$entry->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSubChunk($this);
	}
}
