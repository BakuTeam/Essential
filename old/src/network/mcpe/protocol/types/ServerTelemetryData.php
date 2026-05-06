<?php

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
