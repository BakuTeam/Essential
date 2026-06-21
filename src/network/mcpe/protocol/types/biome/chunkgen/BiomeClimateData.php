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

namespace pocketmine\network\mcpe\protocol\types\biome\chunkgen;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class BiomeClimateData{

	public function __construct(
		private float $temperature,
		private float $downfall,
		private float $redSporeDensity,
		private float $blueSporeDensity,
		private float $ashDensity,
		private float $whiteAshDensity,
		private float $snowAccumulationMin,
		private float $snowAccumulationMax,
	){}

	public function getTemperature() : float{ return $this->temperature; }

	public function getDownfall() : float{ return $this->downfall; }

	public function getRedSporeDensity() : float{ return $this->redSporeDensity; }

	public function getBlueSporeDensity() : float{ return $this->blueSporeDensity; }

	public function getAshDensity() : float{ return $this->ashDensity; }

	public function getWhiteAshDensity() : float{ return $this->whiteAshDensity; }

	public function getSnowAccumulationMin() : float{ return $this->snowAccumulationMin; }

	public function getSnowAccumulationMax() : float{ return $this->snowAccumulationMax; }

	public static function read(PacketSerializer $in) : self{
		$temperature = $in->getLFloat();
		$downfall = $in->getLFloat();
		if($in->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_111){
			$redSporeDensity = $in->getLFloat();
			$blueSporeDensity = $in->getLFloat();
			$ashDensity = $in->getLFloat();
			$whiteAshDensity = $in->getLFloat();
		}
		$snowAccumulationMin = $in->getLFloat();
		$snowAccumulationMax = $in->getLFloat();

		return new self(
			$temperature,
			$downfall,
			$redSporeDensity ?? 0.0,
			$blueSporeDensity ?? 0.0,
			$ashDensity ?? 0.0,
			$whiteAshDensity ?? 0.0,
			$snowAccumulationMin,
			$snowAccumulationMax
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->temperature);
		$out->putLFloat($this->downfall);
		if($out->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_111){
			$out->putLFloat($this->redSporeDensity);
			$out->putLFloat($this->blueSporeDensity);
			$out->putLFloat($this->ashDensity);
			$out->putLFloat($this->whiteAshDensity);
		}
		$out->putLFloat($this->snowAccumulationMin);
		$out->putLFloat($this->snowAccumulationMax);
	}
}
