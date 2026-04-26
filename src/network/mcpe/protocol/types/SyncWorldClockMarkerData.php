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

final class SyncWorldClockMarkerData{
	public function __construct(
		private int $id,
		private string $name,
		private int $time,
		private ?int $period
	){}

	public function getId() : int{ return $this->id; }

	public function getName() : string{ return $this->name; }

	public function getTime() : int{ return $this->time; }

	public function getPeriod() : ?int{ return $this->period; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->getUnsignedVarLong(),
			$in->getString(),
			$in->getVarInt(),
			$in->readOptional(fn() => $in->getLInt())
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarLong($this->id);
		$out->putString($this->name);
		$out->putVarInt($this->time);
		$out->writeOptional($this->period, fn(int $v) => $out->putLInt($v));
	}
}
