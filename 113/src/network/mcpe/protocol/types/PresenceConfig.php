<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class PresenceConfig{
	public function __construct(
		private string $experienceName,
		private string $worldName
	){}

	public function getExperienceName() : string{ return $this->experienceName; }

	public function getWorldName() : string{ return $this->worldName; }

	public static function read(PacketSerializer $in) : self{
		return new self($in->getString(), $in->getString());
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->experienceName);
		$out->putString($this->worldName);
	}
}
