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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class SubChunkPacketEntryWithCache{

	public function __construct(
		private SubChunkPacketEntryCommon $base,
		private int $usedBlobHash
	){}

	public function getBase() : SubChunkPacketEntryCommon{ return $this->base; }

	public function getUsedBlobHash() : int{ return $this->usedBlobHash; }

	public static function read(PacketSerializer $in) : self{
		$base = SubChunkPacketEntryCommon::read($in, true);
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 && !$in->getBool()){
			throw new PacketDecodeException("Expected a blob hash for a cache-enabled subchunk entry");
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_0){
			$usedBlobHash = ($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_10 || $in->getBool()) ? $in->getLLong() : -1;
		}

		return new self($base, $usedBlobHash ?? -1);
	}

	public function write(PacketSerializer $out) : void{
		$this->base->write($out, true);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$out->putBool(true);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_0){
			if($out->getProtocolId() === ProtocolInfo::PROTOCOL_1_18_0){
				$out->putBool(true);
			}
			$out->putLLong($this->usedBlobHash);
		}
	}
}
