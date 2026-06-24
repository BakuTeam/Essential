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

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class BiomeNoiseBlockSpecifier{

	public function __construct(
		private string $noise,
		private float $threshold,
		private float $min,
		private float $max,
		private int $block,
	){}

	public function getNoise() : string{ return $this->noise; }

	public function getThreshold() : float{ return $this->threshold; }

	public function getMin() : float{ return $this->min; }

	public function getMax() : float{ return $this->max; }

	public function getBlock() : int{ return $this->block; }

	public static function read(PacketSerializer $in) : self{
		$noise = $in->getString();
		$threshold = $in->getLFloat();
		$min = $in->getLFloat();
		$max = $in->getLFloat();
		$block = $in->getLInt();

		return new self(
			$noise,
			$threshold,
			$min,
			$max,
			$block
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->noise);
		$out->putLFloat($this->threshold);
		$out->putLFloat($this->min);
		$out->putLFloat($this->max);
		$out->putLInt($this->block);
	}
}
