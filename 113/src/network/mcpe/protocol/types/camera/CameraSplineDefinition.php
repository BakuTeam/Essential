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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class CameraSplineDefinition{
	public function __construct(
		private string $name,
		private CameraSplineInstruction $instruction
	){}

	public function getName() : string{ return $this->name; }

	public function getInstruction() : CameraSplineInstruction{ return $this->instruction; }

	public static function read(PacketSerializer $in) : self{
		return new self($in->getString(), CameraSplineInstruction::read($in));
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->name);
		$this->instruction->write($out);
	}
}
