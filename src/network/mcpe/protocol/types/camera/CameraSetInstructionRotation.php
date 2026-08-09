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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class CameraSetInstructionRotation{

	public function __construct(
		private float $pitch,
		private float $yaw,
	){}

	public function getPitch() : float{ return $this->pitch; }

	public function getYaw() : float{ return $this->yaw; }

	public static function read(PacketSerializer $in) : self{
		$pitch = $in->getLFloat();
		$yaw = $in->getLFloat();
		return new self($pitch, $yaw);
	}

	public static function fromNBT(CompoundTag $nbt) : self{
		$pitch = $nbt->getFloat("x");
		$yaw = $nbt->getFloat("y");
		return new self($pitch, $yaw);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->pitch);
		$out->putLFloat($this->yaw);
	}

	public function toNBT() : CompoundTag{
		return CompoundTag::create()
			->setFloat("x", $this->pitch)
			->setFloat("y", $this->yaw);
	}
}
