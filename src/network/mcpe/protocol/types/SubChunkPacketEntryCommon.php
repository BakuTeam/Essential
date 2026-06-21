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

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class SubChunkPacketEntryCommon{

	public function __construct(
		private SubChunkPositionOffset $offset,
		private int $requestResult,
		private string $terrainData,
		private ?SubChunkPacketHeightMapInfo $heightMap,
		private ?SubChunkPacketHeightMapInfo $renderHeightMap
	){}

	public function getOffset() : SubChunkPositionOffset{ return $this->offset; }

	public function getRequestResult() : int{ return $this->requestResult; }

	public function getTerrainData() : string{ return $this->terrainData; }

	public function getHeightMap() : ?SubChunkPacketHeightMapInfo{ return $this->heightMap; }

	public function getRenderHeightMap() : ?SubChunkPacketHeightMapInfo{ return $this->renderHeightMap; }

	public static function read(PacketSerializer $in, bool $cacheEnabled) : self{

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_10){
			$offset = SubChunkPositionOffset::read($in);

			$requestResult = $in->getByte();

			$data = !$cacheEnabled || $requestResult !== SubChunkRequestResult::SUCCESS_ALL_AIR ? $in->getString() : "";
		} else {
			$offset = new SubChunkPositionOffset(0, 0, 0);

			$data = $in->getString();

			$requestResult = $in->getVarInt();
		}

		$heightMapDataType = $in->getByte();
		$heightMapData = match ($heightMapDataType) {
			SubChunkPacketHeightMapType::NO_DATA => null,
			SubChunkPacketHeightMapType::DATA => SubChunkPacketHeightMapInfo::read($in),
			SubChunkPacketHeightMapType::ALL_TOO_HIGH => SubChunkPacketHeightMapInfo::allTooHigh(),
			SubChunkPacketHeightMapType::ALL_TOO_LOW => SubChunkPacketHeightMapInfo::allTooLow(),
			default => throw new PacketDecodeException("Unknown heightmap data type $heightMapDataType")
		};

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_90){
			$renderHeightMapDataType = $in->getByte();
			$renderHeightMapData = match ($renderHeightMapDataType) {
				SubChunkPacketHeightMapType::NO_DATA => null,
				SubChunkPacketHeightMapType::DATA => SubChunkPacketHeightMapInfo::read($in),
				SubChunkPacketHeightMapType::ALL_TOO_HIGH => SubChunkPacketHeightMapInfo::allTooHigh(),
				SubChunkPacketHeightMapType::ALL_TOO_LOW => SubChunkPacketHeightMapInfo::allTooLow(),
				SubChunkPacketHeightMapType::ALL_COPIED => $heightMapData,
				default => throw new PacketDecodeException("Unknown render heightmap data type $renderHeightMapDataType")
			};
		}

		return new self(
			$offset,
			$requestResult,
			$data,
			$heightMapData,
			$renderHeightMapData ?? null,
		);
	}

	public function write(PacketSerializer $out, bool $cacheEnabled) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_10){
			$this->offset->write($out);

			$out->putByte($this->requestResult);

			if(!$cacheEnabled || $this->requestResult !== SubChunkRequestResult::SUCCESS_ALL_AIR){
				$out->putString($this->terrainData);
			}
		} else {
			$out->putString($this->terrainData);
			$out->putVarInt($this->requestResult);
		}

		if($this->heightMap === null){
			$out->putByte(SubChunkPacketHeightMapType::NO_DATA);
		}elseif($this->heightMap->isAllTooLow()){
			$out->putByte(SubChunkPacketHeightMapType::ALL_TOO_LOW);
		}elseif($this->heightMap->isAllTooHigh()){
			$out->putByte(SubChunkPacketHeightMapType::ALL_TOO_HIGH);
		}else{
			$heightMapData = $this->heightMap; //avoid PHPStan purity issue
			$out->putByte(SubChunkPacketHeightMapType::DATA);
			$heightMapData->write($out);
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_90){
			if($this->renderHeightMap === null){
				$out->putByte(SubChunkPacketHeightMapType::ALL_COPIED);
			}elseif($this->renderHeightMap->isAllTooLow()){
				$out->putByte(SubChunkPacketHeightMapType::ALL_TOO_LOW);
			}elseif($this->renderHeightMap->isAllTooHigh()){
				$out->putByte(SubChunkPacketHeightMapType::ALL_TOO_HIGH);
			}else{
				$renderHeightMapData = $this->renderHeightMap; //avoid PHPStan purity issue
				$out->putByte(SubChunkPacketHeightMapType::DATA);
				$renderHeightMapData->write($out);
			}
		}
	}
}
