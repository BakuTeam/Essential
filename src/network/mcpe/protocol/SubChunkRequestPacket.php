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
use pocketmine\network\mcpe\protocol\types\SubChunkPosition;
use pocketmine\network\mcpe\protocol\types\SubChunkPositionOffset;
use function count;

class SubChunkRequestPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::SUB_CHUNK_REQUEST_PACKET;

	private int $dimension;
	private SubChunkPosition $basePosition;
	/**
	 * @var SubChunkPositionOffset[]
	 * @phpstan-var list<SubChunkPositionOffset>
	 */
	private array $entries;

	/**
	 * @generate-create-func
	 * @param SubChunkPositionOffset[] $entries
	 * @phpstan-param list<SubChunkPositionOffset> $entries
	 */
	public static function create(int $dimension, SubChunkPosition $basePosition, array $entries) : self{
		$result = new self();
		$result->dimension = $dimension;
		$result->basePosition = $basePosition;
		$result->entries = $entries;
		return $result;
	}

	public function getDimension() : int{ return $this->dimension; }

	public function getBasePosition() : SubChunkPosition{ return $this->basePosition; }

	/**
	 * @return SubChunkPositionOffset[]
	 * @phpstan-return list<SubChunkPositionOffset>
	 */
	public function getEntries() : array{ return $this->entries; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->dimension = $in->getVarInt();

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$this->entries = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; $i++){
				$this->entries[] = SubChunkPositionOffset::read($in);
			}
			$this->basePosition = SubChunkPosition::read($in, true);
			return;
		}

		$this->basePosition = SubChunkPosition::read($in);

		$this->entries = [];
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_10){
			for($i = 0, $count = $in->getLInt(); $i < $count; $i++){
				$this->entries[] = SubChunkPositionOffset::read($in);
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putVarInt($this->dimension);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$out->putUnsignedVarInt(count($this->entries));
			foreach($this->entries as $entry){
				$entry->write($out);
			}
			$this->basePosition->write($out, true);
			return;
		}

		$this->basePosition->write($out);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_10){
			$out->putLInt(count($this->entries));
			foreach($this->entries as $entry){
				$entry->write($out);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSubChunkRequest($this);
	}
}
