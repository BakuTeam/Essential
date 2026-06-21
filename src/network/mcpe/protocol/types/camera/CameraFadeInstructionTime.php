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

final class CameraFadeInstructionTime{

	public function __construct(
		private float $fadeInTime,
		private float $stayTime,
		private float $fadeOutTime
	){}

	public function getFadeInTime() : float{ return $this->fadeInTime; }

	public function getStayTime() : float{ return $this->stayTime; }

	public function getFadeOutTime() : float{ return $this->fadeOutTime; }

	public static function read(PacketSerializer $in) : self{
		$fadeInTime = $in->getLFloat();
		$stayTime = $in->getLFloat();
		$fadeOutTime = $in->getLFloat();
		return new self($fadeInTime, $stayTime, $fadeOutTime);
	}

	public static function fromNBT(CompoundTag $nbt) : self{
		$fadeInTime = $nbt->getFloat("fadeIn");
		$stayTime = $nbt->getFloat("hold");
		$fadeOutTime = $nbt->getFloat("fadeOut");
		return new self($fadeInTime, $stayTime, $fadeOutTime);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->fadeInTime);
		$out->putLFloat($this->stayTime);
		$out->putLFloat($this->fadeOutTime);
	}

	public function toNBT() : CompoundTag{
		return CompoundTag::create()
			->setFloat("fadeIn", $this->fadeInTime)
			->setFloat("hold", $this->stayTime)
			->setFloat("fadeOut", $this->fadeOutTime);
	}
}
