<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class DimensionData{

	public function __construct(
		private int $maxHeight,
		private int $minHeight,
		private int $generator,
		private int $dimensionType = DimensionIds::OVERWORLD
	){}

	public function getMaxHeight() : int{ return $this->maxHeight; }

	public function getMinHeight() : int{ return $this->minHeight; }

	public function getGenerator() : int{ return $this->generator; }

	public function getDimensionType() : int{ return $this->dimensionType; }

	public static function read(PacketSerializer $in, ?int $protocolId = null) : self{
		$protocolId ??= $in->getProtocolId();
		$maxHeight = $in->getVarInt();
		$minHeight = $in->getVarInt();
		$generator = $in->getVarInt();
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_20){
			$dimensionType = $in->getVarInt();
		}

		return new self($maxHeight, $minHeight, $generator, $dimensionType ?? DimensionIds::OVERWORLD);
	}

	public function write(PacketSerializer $out, ?int $protocolId = null) : void{
		$protocolId ??= $out->getProtocolId();
		$out->putVarInt($this->maxHeight);
		$out->putVarInt($this->minHeight);
		$out->putVarInt($this->generator);
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->putVarInt($this->dimensionType);
		}
	}
}
