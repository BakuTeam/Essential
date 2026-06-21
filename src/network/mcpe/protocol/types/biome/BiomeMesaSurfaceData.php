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
