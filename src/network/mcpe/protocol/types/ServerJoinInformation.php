<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class ServerJoinInformation{
	public function __construct(
		private ?GatheringJoinInfo $gatheringJoinInfo,
	){}

	public function getGatheringJoinInfo() : ?GatheringJoinInfo{ return $this->gatheringJoinInfo; }

	public static function read(PacketSerializer $in) : self{
		return new self(
			$in->readOptional(fn() => GatheringJoinInfo::read($in))
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->writeOptional($this->gatheringJoinInfo, fn(GatheringJoinInfo $info) => $info->write($out));
	}
}
