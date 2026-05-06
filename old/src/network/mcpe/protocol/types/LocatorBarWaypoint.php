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

use pocketmine\color\Color;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class LocatorBarWaypoint{
	public function __construct(
		private int $updateFlag,
		private ?bool $visible,
		private ?WorldPosition $worldPosition,
		private ?int $textureId,
		private ?Color $color,
		private ?bool $clientPositionAuthority,
		private ?int $actorUniqueId
	){}

	public function getUpdateFlag() : int{ return $this->updateFlag; }

	public function getVisible() : ?bool{ return $this->visible; }

	public function getWorldPosition() : ?WorldPosition{ return $this->worldPosition; }

	public function getTextureId() : ?int{ return $this->textureId; }

	public function getColor() : ?Color{ return $this->color; }

	public function getClientPositionAuthority() : ?bool{ return $this->clientPositionAuthority; }

	public function getActorUniqueId() : ?int{ return $this->actorUniqueId; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->getLInt(),
			$in->readOptional(fn() => $in->getBool()),
			$in->readOptional(fn() => WorldPosition::read($in)),
			$in->readOptional(fn() => $in->getLInt()),
			$in->readOptional(fn() => Color::fromARGB($in->getLInt())),
			$in->readOptional(fn() => $in->getBool()),
			$in->readOptional(fn() => $in->getActorUniqueId())
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLInt($this->updateFlag);
		$out->writeOptional($this->visible, fn(bool $v) => $out->putBool($v));
		$out->writeOptional($this->worldPosition, fn(WorldPosition $v) => $v->write($out));
		$out->writeOptional($this->textureId, fn(int $v) => $out->putLInt($v));
		$out->writeOptional($this->color, fn(Color $v) => $out->putLInt($v->toARGB()));
		$out->writeOptional($this->clientPositionAuthority, fn(bool $v) => $out->putBool($v));
		$out->writeOptional($this->actorUniqueId, fn(int $v) => $out->putActorUniqueId($v));
	}
}
