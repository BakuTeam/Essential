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
