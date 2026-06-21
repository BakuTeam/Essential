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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class CameraFadeInstructionColor{

	public function __construct(
		private float $red,
		private float $green,
		private float $blue,
	){}

	public function getRed() : float{ return $this->red; }

	public function getGreen() : float{ return $this->green; }

	public function getBlue() : float{ return $this->blue; }

	public static function read(PacketSerializer $in) : self{
		$red = $in->getLFloat();
		$green = $in->getLFloat();
		$blue = $in->getLFloat();
		return new self($red, $green, $blue);
	}

	public static function fromNBT(CompoundTag $nbt) : self{
		$red = $nbt->getFloat("red");
		$green = $nbt->getFloat("green");
		$blue = $nbt->getFloat("blue");
		return new self($red, $green, $blue);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->red);
		$out->putLFloat($this->green);
		$out->putLFloat($this->blue);
	}

	public function toNBT() : CompoundTag{
		return CompoundTag::create()
			->setFloat("r", $this->red)
			->setFloat("g", $this->green)
			->setFloat("b", $this->blue);
	}
}
