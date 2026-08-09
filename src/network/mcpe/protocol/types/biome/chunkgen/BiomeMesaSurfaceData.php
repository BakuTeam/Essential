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

final class BiomeMesaSurfaceData{

	public function __construct(
		private int $clayMaterial,
		private int $hardClayMaterial,
		private bool $brycePillars,
		private bool $forest,
	){}

	public function getClayMaterial() : int{ return $this->clayMaterial; }

	public function getHardClayMaterial() : int{ return $this->hardClayMaterial; }

	public function hasBrycePillars() : bool{ return $this->brycePillars; }

	public function hasForest() : bool{ return $this->forest; }

	public static function read(PacketSerializer $in) : self{
		$clayMaterial = $in->getLInt();
		$hardClayMaterial = $in->getLInt();
		$brycePillars = $in->getBool();
		$forest = $in->getBool();

		return new self(
			$clayMaterial,
			$hardClayMaterial,
			$brycePillars,
			$forest
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLInt($this->clayMaterial);
		$out->putLInt($this->hardClayMaterial);
		$out->putBool($this->brycePillars);
		$out->putBool($this->forest);
	}
}
