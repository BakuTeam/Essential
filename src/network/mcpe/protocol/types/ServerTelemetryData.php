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

final class ServerTelemetryData{
	public function __construct(
		private string $serverId,
		private string $scenarioId,
		private string $worldId,
		private string $ownerId,
	){}

	public function getServerId() : string{ return $this->serverId; }

	public function getScenarioId() : string{ return $this->scenarioId; }

	public function getWorldId() : string{ return $this->worldId; }

	public function getOwnerId() : string{ return $this->ownerId; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->getString(),
			$in->getString(),
			$in->getString(),
			$in->getString()
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->serverId);
		$out->putString($this->scenarioId);
		$out->putString($this->worldId);
		$out->putString($this->ownerId);
	}
}
