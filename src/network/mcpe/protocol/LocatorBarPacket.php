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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\LocatorBarWaypointPayload;
use function count;

class LocatorBarPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LOCATOR_BAR_PACKET;

	/**
	 * @var LocatorBarWaypointPayload[]
	 * @phpstan-var list<LocatorBarWaypointPayload>
	 */
	private array $waypoints;

	/**
	 * @generate-create-func
	 * @param LocatorBarWaypointPayload[] $waypoints
	 * @phpstan-param list<LocatorBarWaypointPayload> $waypoints
	 */
	public static function create(array $waypoints) : self{
		$result = new self();
		$result->waypoints = $waypoints;
		return $result;
	}

	/**
	 * @return LocatorBarWaypointPayload[]
	 * @phpstan-return list<LocatorBarWaypointPayload>
	 */
	public function getWaypoints() : array{ return $this->waypoints; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->waypoints = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$this->waypoints[] = LocatorBarWaypointPayload::read($in, $in->getProtocolId());
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->waypoints));
		foreach($this->waypoints as $waypoint){
			$waypoint->write($out, $out->getProtocolId());
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleLocatorBar($this);
	}
}
