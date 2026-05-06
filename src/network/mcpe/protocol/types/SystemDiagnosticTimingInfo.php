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

final class SystemDiagnosticTimingInfo{

	public function __construct(
		private string $displayName,
		private int $systemIndex,
		private int $timeInNS,
		private int $percentOfTotal,
	){}

	public function getDisplayName() : string{ return $this->displayName; }

	public function getSystemIndex() : int{ return $this->systemIndex; }

	public function getTimeInNS() : int{ return $this->timeInNS; }

	public function getPercentOfTotal() : int{ return $this->percentOfTotal; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->getString(),
			$in->getLLong(),
			$in->getLLong(),
			$in->getByte()
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->displayName);
		$out->putLLong($this->systemIndex);
		$out->putLLong($this->timeInNS);
		$out->putByte($this->percentOfTotal);
	}
}
