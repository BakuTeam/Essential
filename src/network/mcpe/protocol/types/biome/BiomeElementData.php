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

final class BiomeElementData{

	public function __construct(
		private float $noiseFrequencyScale,
		private float $noiseLowerBound,
		private float $noiseUpperBound,
		private int $heightMinType,
		private int $heightMin,
		private int $heightMaxType,
		private int $heightMax,
		private BiomeSurfaceMaterialData $surfaceMaterial,
	){}

	public function getNoiseFrequencyScale() : float{ return $this->noiseFrequencyScale; }

	public function getNoiseLowerBound() : float{ return $this->noiseLowerBound; }

	public function getNoiseUpperBound() : float{ return $this->noiseUpperBound; }

	public function getHeightMinType() : int{ return $this->heightMinType; }

	public function getHeightMin() : int{ return $this->heightMin; }

	public function getHeightMaxType() : int{ return $this->heightMaxType; }

	public function getHeightMax() : int{ return $this->heightMax; }

	public function getSurfaceMaterial() : BiomeSurfaceMaterialData{ return $this->surfaceMaterial; }

	public static function read(PacketSerializer $in) : self{
		$noiseFrequencyScale = $in->getLFloat();
		$noiseLowerBound = $in->getLFloat();
		$noiseUpperBound = $in->getLFloat();
		$heightMinType = $in->getVarInt();
		$heightMin = $in->getLShort();
		$heightMaxType = $in->getVarInt();
		$heightMax = $in->getLShort();
		$surfaceMaterial = BiomeSurfaceMaterialData::read($in);

		return new self(
			$noiseFrequencyScale,
			$noiseLowerBound,
			$noiseUpperBound,
			$heightMinType,
			$heightMin,
			$heightMaxType,
			$heightMax,
			$surfaceMaterial
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->noiseFrequencyScale);
		$out->putLFloat($this->noiseLowerBound);
		$out->putLFloat($this->noiseUpperBound);
		$out->putVarInt($this->heightMinType);
		$out->putLShort($this->heightMin);
		$out->putVarInt($this->heightMaxType);
		$out->putLShort($this->heightMax);
		$this->surfaceMaterial->write($out);
	}
}
