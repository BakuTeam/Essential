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

final class BiomeSurfaceMaterialData{

	public function __construct(
		private int $topBlock,
		private int $midBlock,
		private int $seaFloorBlock,
		private int $foundationBlock,
		private int $seaBlock,
		private int $seaFloorDepth
	){}

	public function getTopBlock() : int{ return $this->topBlock; }

	public function getMidBlock() : int{ return $this->midBlock; }

	public function getSeaFloorBlock() : int{ return $this->seaFloorBlock; }

	public function getFoundationBlock() : int{ return $this->foundationBlock; }

	public function getSeaBlock() : int{ return $this->seaBlock; }

	public function getSeaFloorDepth() : int{ return $this->seaFloorDepth; }

	public static function read(PacketSerializer $in) : self{
		$topBlock = $in->getLInt();
		$midBlock = $in->getLInt();
		$seaFloorBlock = $in->getLInt();
		$foundationBlock = $in->getLInt();
		$seaBlock = $in->getLInt();
		$seaFloorDepth = $in->getLInt();

		return new self(
			$topBlock,
			$midBlock,
			$seaFloorBlock,
			$foundationBlock,
			$seaBlock,
			$seaFloorDepth
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLInt($this->topBlock);
		$out->putLInt($this->midBlock);
		$out->putLInt($this->seaFloorBlock);
		$out->putLInt($this->foundationBlock);
		$out->putLInt($this->seaBlock);
		$out->putLInt($this->seaFloorDepth);
	}
}
