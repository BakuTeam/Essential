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
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackType;

class ResourcePackDataInfoPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_DATA_INFO_PACKET;

	public string $packId;
	public int $maxChunkSize;
	public int $chunkCount;
	public int $compressedPackSize;
	public string $sha256;
	public bool $isPremium = false;
	public int $packType = ResourcePackType::RESOURCES; //TODO: check the values for this

	/**
	 * @generate-create-func
	 */
	public static function create(
		string $packId,
		int $maxChunkSize,
		int $chunkCount,
		int $compressedPackSize,
		string $sha256,
		bool $isPremium,
		int $packType,
	) : self{
		$result = new self();
		$result->packId = $packId;
		$result->maxChunkSize = $maxChunkSize;
		$result->chunkCount = $chunkCount;
		$result->compressedPackSize = $compressedPackSize;
		$result->sha256 = $sha256;
		$result->isPremium = $isPremium;
		$result->packType = $packType;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->packId = $in->getString();
		$this->maxChunkSize = $in->getLInt();
		$this->chunkCount = $in->getLInt();
		$this->compressedPackSize = $in->getLLong();
		$this->sha256 = $in->getString();
		$this->isPremium = $in->getBool();
		$this->packType = $in->getByte();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->packId);
		$out->putLInt($this->maxChunkSize);
		$out->putLInt($this->chunkCount);
		$out->putLLong($this->compressedPackSize);
		$out->putString($this->sha256);
		$out->putBool($this->isPremium);
		$out->putByte($this->packType);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleResourcePackDataInfo($this);
	}
}
