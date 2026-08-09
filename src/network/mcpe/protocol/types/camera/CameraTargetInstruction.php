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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class CameraTargetInstruction{

	public function __construct(
		private ?Vector3 $targetCenterOffset,
		private int $actorUniqueId
	){}

	public function getTargetCenterOffset() : ?Vector3{ return $this->targetCenterOffset; }

	public function getActorUniqueId() : int{ return $this->actorUniqueId; }

	public static function read(PacketSerializer $in) : self{
		$targetCenterOffset = $in->readOptional(fn() => $in->getVector3());
		$actorUniqueId = $in->getLLong(); //why be consistent mojang ?????
		return new self(
			$targetCenterOffset,
			$actorUniqueId
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->writeOptional($this->targetCenterOffset, fn(Vector3 $v) => $out->putVector3($v));
		$out->putLLong($this->actorUniqueId);
	}
}
