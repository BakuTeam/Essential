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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\utils\Binary;
use pocketmine\utils\Limits;

final class SubChunkPositionOffset{

	public function __construct(
		private int $xOffset,
		private int $yOffset,
		private int $zOffset,
	){
		self::clampOffset($this->xOffset);
		self::clampOffset($this->yOffset);
		self::clampOffset($this->zOffset);
	}

	private static function clampOffset(int $v) : void{
		if($v < Limits::INT8_MIN || $v > Limits::INT8_MAX){
			throw new \InvalidArgumentException("Offsets must be within the range of a byte (" . Limits::INT8_MIN . " ... " . Limits::INT8_MAX . ")");
		}
	}

	public function getXOffset() : int{ return $this->xOffset; }

	public function getYOffset() : int{ return $this->yOffset; }

	public function getZOffset() : int{ return $this->zOffset; }

	public static function read(PacketSerializer $in) : self{
		$xOffset = Binary::signByte($in->getByte());
		$yOffset = Binary::signByte($in->getByte());
		$zOffset = Binary::signByte($in->getByte());

		return new self($xOffset, $yOffset, $zOffset);
	}

	public function write(PacketSerializer $out) : void{
		$out->putByte($this->xOffset);
		$out->putByte($this->yOffset);
		$out->putByte($this->zOffset);
	}
}
