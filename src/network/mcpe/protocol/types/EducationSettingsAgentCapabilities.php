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

final class EducationSettingsAgentCapabilities{
	public function __construct(
		private ?bool $canModifyBlocks
	){}

	public function getCanModifyBlocks() : ?bool{ return $this->canModifyBlocks; }

	public static function read(PacketSerializer $in) : self{
		$canModifyBlocks = $in->readOptional(\Closure::fromCallable([$in, 'getBool']));
		return new self($canModifyBlocks);
	}

	public function write(PacketSerializer $out) : void{
		$out->writeOptional($this->canModifyBlocks, \Closure::fromCallable([$out, 'putBool']));
	}
}
