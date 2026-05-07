<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class GatheringJoinInfo{
	private UuidInterface $targetId;

	public function __construct(
		private string $experienceId,
		private string $experienceName,
		private string $experienceWorldId,
		private string $experienceWorldName,
		private string $creatorId,
		private string $storeId,
		?UuidInterface $targetId = null,
		private string $scenarioId = "",
		private string $serverId = "",
		private string $storeName = "",
		private bool $presenceConfiguration = false
	){
		$this->targetId = $targetId ?? Uuid::uuid4();
	}

	public function getExperienceId() : string{ return $this->experienceId; }

	public function getExperienceName() : string{ return $this->experienceName; }

	public function getExperienceWorldId() : string{ return $this->experienceWorldId; }

	public function getExperienceWorldName() : string{ return $this->experienceWorldName; }

	public function getCreatorId() : string{ return $this->creatorId; }

	public function getTargetId() : UuidInterface{ return $this->targetId; }

	public function getScenarioId() : string{ return $this->scenarioId; }

	public function getServerId() : string{ return $this->serverId; }

	public function getStoreId() : string{ return $this->storeId; }

	public function getStoreName() : string{ return $this->storeName; }

	public function isPresenceConfiguration() : bool{ return $this->presenceConfiguration; }

	public static function read(PacketSerializer $in, ?int $protocolId = null) : self{
		$protocolId ??= $in->getProtocolId();
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_10){
			$experienceId = $in->getString();
			$experienceName = $in->getString();
			$experienceWorldId = $in->getString();
			$experienceWorldName = $in->getString();
			$creatorId = $in->getString();
			$targetId = $in->getUUID();
			$scenarioId = $in->getString();
			$serverId = $in->getString();
			$storeId = $in->getString();
			$storeName = $in->getString();
		}

		$presenceConfiguration = $in->getBool();

		return new self(
			$experienceId ?? "",
			$experienceName ?? "",
			$experienceWorldId ?? "",
			$experienceWorldName ?? "",
			$creatorId ?? "",
			$storeId ?? "",
			$targetId ?? null,
			$scenarioId ?? "",
			$serverId ?? "",
			$storeName ?? "",
			$presenceConfiguration
		);
	}

	public function write(PacketSerializer $out, ?int $protocolId = null) : void{
		$protocolId ??= $out->getProtocolId();
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_10){
			$out->putString($this->experienceId);
			$out->putString($this->experienceName);
			$out->putString($this->experienceWorldId);
			$out->putString($this->experienceWorldName);
			$out->putString($this->creatorId);
			$out->putUUID($this->targetId);
			$out->putString($this->scenarioId);
			$out->putString($this->serverId);
			$out->putString($this->storeId);
			$out->putString($this->storeName);
		}

		$out->putBool($this->presenceConfiguration);
	}
}
