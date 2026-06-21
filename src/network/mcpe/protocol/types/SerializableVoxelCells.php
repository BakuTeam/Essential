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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class SerializableVoxelCells{
	/**
	 * @param int[] $storage
	 * @phpstan-param list<int> $storage
	 */
	public function __construct(
		private int $xSize,
		private int $ySize,
		private int $zSize,
		private array $storage
	){}

	public function getXSize() : int{ return $this->xSize; }

	public function getYSize() : int{ return $this->ySize; }

	public function getZSize() : int{ return $this->zSize; }

	/**
	 * @return int[]
	 * @phpstan-return list<int>
	 */
	public function getStorage() : array{ return $this->storage; }

	public static function read(PacketSerializer $in) : self{
		$xSize = $in->getByte();
		$ySize = $in->getByte();
		$zSize = $in->getByte();
		$storage = [];
		for($i = 0, $storageCount = $in->getUnsignedVarInt(); $i < $storageCount; ++$i){
			$storage[] = $in->getByte();
		}

		return new self($xSize, $ySize, $zSize, $storage);
	}

	public function write(PacketSerializer $out) : void{
		$out->putByte($this->xSize);
		$out->putByte($this->ySize);
		$out->putByte($this->zSize);
		$out->putUnsignedVarInt(count($this->storage));
		foreach($this->storage as $value){
			$out->putByte($value);
		}
	}
}
