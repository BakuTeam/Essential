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

final class CameraSetInstructionEase{

	/**
	 * @see CameraSetInstructionEaseType
	 */
	public function __construct(
		private int $type,
		private float $duration
	){}

	/**
	 * @see CameraSetInstructionEaseType
	 */
	public function getType() : int{ return $this->type; }

	public function getDuration() : float{ return $this->duration; }

	public static function read(PacketSerializer $in) : self{
		$type = $in->getByte();
		$duration = $in->getLFloat();
		return new self($type, $duration);
	}

	public static function fromNBT(CompoundTag $nbt) : self{
		$typeName = $nbt->getString("type");
		$type = CameraSetInstructionEaseType::fromString($typeName) ?? throw new \InvalidArgumentException("Invalid type tag");
		$duration = $nbt->getFloat("time");
		return new self($type, $duration);
	}

	public function write(PacketSerializer $out) : void{
		$out->putByte($this->type);
		$out->putLFloat($this->duration);
	}

	public function toNBT() : CompoundTag{
		return CompoundTag::create()
			->setString("type", CameraSetInstructionEaseType::toString($this->type) ?? throw new \InvalidArgumentException("Invalid type"))
			->setFloat("time", $this->duration);
	}
}
