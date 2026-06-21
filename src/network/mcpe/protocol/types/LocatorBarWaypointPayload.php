<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
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

	public static function read(PacketSerializer $in, ?int $protocolId = null) : self{
		$protocolId ??= $in->getProtocolId();
		return new self($in->getUUID(), LocatorBarWaypoint::read($in, $protocolId), $in->getByte());
	}

	public function write(PacketSerializer $out, ?int $protocolId = null) : void{
		$protocolId ??= $out->getProtocolId();
		$out->putUUID($this->group);
		$this->waypoint->write($out, $protocolId);
		$out->putByte($this->action);
	}
}
