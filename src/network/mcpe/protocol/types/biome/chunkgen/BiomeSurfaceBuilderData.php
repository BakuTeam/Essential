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

namespace pocketmine\network\mcpe\protocol\types\biome\chunkgen;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class BiomeSurfaceBuilderData{

	public function __construct(
		private ?BiomeSurfaceMaterialData $surfaceMaterial,
		private bool $defaultOverworldSurface,
		private bool $swampSurface,
		private bool $frozenOceanSurface,
		private bool $theEndSurface,
		private ?BiomeMesaSurfaceData $mesaSurface,
		private ?BiomeCappedSurfaceData $cappedSurface,
		private ?BiomeNoiseGradientSurfaceData $noiseGradientSurface,
	){}

	public function getSurfaceMaterial() : ?BiomeSurfaceMaterialData{ return $this->surfaceMaterial; }

	public function hasDefaultOverworldSurface() : bool{ return $this->defaultOverworldSurface; }

	public function hasSwampSurface() : bool{ return $this->swampSurface; }

	public function hasFrozenOceanSurface() : bool{ return $this->frozenOceanSurface; }

	public function hasTheEndSurface() : bool{ return $this->theEndSurface; }

	public function getMesaSurface() : ?BiomeMesaSurfaceData{ return $this->mesaSurface; }

	public function getCappedSurface() : ?BiomeCappedSurfaceData{ return $this->cappedSurface; }

	public function getNoiseGradientSurface() : ?BiomeNoiseGradientSurfaceData{ return $this->noiseGradientSurface; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->readOptional(fn() => BiomeSurfaceMaterialData::read($in)),
			$in->getBool(),
			$in->getBool(),
			$in->getBool(),
			$in->getBool(),
			$in->readOptional(fn() => BiomeMesaSurfaceData::read($in)),
			$in->readOptional(fn() => BiomeCappedSurfaceData::read($in)),
			$in->readOptional(fn() => BiomeNoiseGradientSurfaceData::read($in))
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->writeOptional($this->surfaceMaterial, fn(BiomeSurfaceMaterialData $v) => $v->write($out));
		$out->putBool($this->defaultOverworldSurface);
		$out->putBool($this->swampSurface);
		$out->putBool($this->frozenOceanSurface);
		$out->putBool($this->theEndSurface);
		$out->writeOptional($this->mesaSurface, fn(BiomeMesaSurfaceData $v) => $v->write($out));
		$out->writeOptional($this->cappedSurface, fn(BiomeCappedSurfaceData $v) => $v->write($out));
		$out->writeOptional($this->noiseGradientSurface, fn(BiomeNoiseGradientSurfaceData $v) => $v->write($out));
	}
}
