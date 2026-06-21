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

final class ChunkPosition{

	public function __construct(
		private int $x,
		private int $z
	){}

	public function getX() : int{ return $this->x; }

	public function getZ() : int{ return $this->z; }

	public static function read(PacketSerializer $in) : self{
		$x = $in->getVarInt();
		$z = $in->getVarInt();

		return new self($x, $z);
	}

	public function write(PacketSerializer $out) : void{
		$out->putVarInt($this->x);
		$out->putVarInt($this->z);
	}
}
