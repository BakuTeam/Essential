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
