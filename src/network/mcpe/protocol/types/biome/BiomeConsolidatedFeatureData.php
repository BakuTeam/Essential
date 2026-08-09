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

namespace pocketmine\network\mcpe\protocol\types\biome;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class BiomeConsolidatedFeatureData{

	public function __construct(
		private BiomeScatterParamData $scatter,
		private int $feature,
		private int $identifier,
		private int $pass,
		private bool $useInternal
	){}

	public function getScatter() : BiomeScatterParamData{ return $this->scatter; }

	public function getFeature() : int{ return $this->feature; }

	public function getIdentifier() : int{ return $this->identifier; }

	public function getPass() : int{ return $this->pass; }

	public function canUseInternal() : bool{ return $this->useInternal; }

	public static function read(PacketSerializer $in) : self{
		$scatter = BiomeScatterParamData::read($in);
		$feature = $in->getLShort();
		$identifier = $in->getLShort();
		$pass = $in->getUnsignedVarInt();
		$useInternal = $in->getBool();

		return new self(
			$scatter,
			$feature,
			$identifier,
			$pass,
			$useInternal
		);
	}

	public function write(PacketSerializer $out) : void{
		$this->scatter->write($out);
		$out->putLShort($this->feature);
		$out->putLShort($this->identifier);
		$out->putUnsignedVarInt($this->pass);
		$out->putBool($this->useInternal);
	}
}
