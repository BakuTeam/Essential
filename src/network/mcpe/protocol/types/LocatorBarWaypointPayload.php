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
use Ramsey\Uuid\UuidInterface;

final class LocatorBarWaypointPayload{
	public function __construct(
		private UuidInterface $group,
		private LocatorBarWaypoint $waypoint,
		private int $action
	){}

	public function getGroup() : UuidInterface{ return $this->group; }

	public function getWaypoint() : LocatorBarWaypoint{ return $this->waypoint; }

	public function getAction() : int{ return $this->action; }

	public static function read(PacketSerializer $in) : self{
		return new self($in->getUUID(), LocatorBarWaypoint::read($in), $in->getByte());
	}

	public function write(PacketSerializer $out) : void{
		$out->putUUID($this->group);
		$this->waypoint->write($out);
		$out->putByte($this->action);
	}
}
