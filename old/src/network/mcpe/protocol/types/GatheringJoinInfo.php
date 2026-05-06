<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class GatheringJoinInfo{
	public function __construct(
		private string $experienceId,
		private string $experienceName,
		private string $experienceWorldId,
		private string $experienceWorldName,
		private string $creatorId,
		private string $storeId,
	){}

	public function getExperienceId() : string{ return $this->experienceId; }

	public function getExperienceName() : string{ return $this->experienceName; }

	public function getExperienceWorldId() : string{ return $this->experienceWorldId; }

	public function getExperienceWorldName() : string{ return $this->experienceWorldName; }

	public function getCreatorId() : string{ return $this->creatorId; }

	public function getStoreId() : string{ return $this->storeId; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->getString(),
			$in->getString(),
			$in->getString(),
			$in->getString(),
			$in->getString(),
			$in->getString()
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->experienceId);
		$out->putString($this->experienceName);
		$out->putString($this->experienceWorldId);
		$out->putString($this->experienceWorldName);
		$out->putString($this->creatorId);
		$out->putString($this->storeId);
	}
}
