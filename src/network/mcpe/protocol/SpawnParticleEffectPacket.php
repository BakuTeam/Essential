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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\DimensionIds;

class SpawnParticleEffectPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SPAWN_PARTICLE_EFFECT_PACKET;

	public int $dimensionId = DimensionIds::OVERWORLD; //wtf mojang
	public int $actorUniqueId = -1; //default none
	public Vector3 $position;
	public string $particleName;
	public ?string $molangVariablesJson = null;

	/**
	 * @generate-create-func
	 */
	public static function create(int $dimensionId, int $actorUniqueId, Vector3 $position, string $particleName, ?string $molangVariablesJson) : self{
		$result = new self();
		$result->dimensionId = $dimensionId;
		$result->actorUniqueId = $actorUniqueId;
		$result->position = $position;
		$result->particleName = $particleName;
		$result->molangVariablesJson = $molangVariablesJson;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->dimensionId = $in->getByte();
		$this->actorUniqueId = $in->getActorUniqueId();
		$this->position = $in->getVector3();
		$this->particleName = $in->getString();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_30){
			$this->molangVariablesJson = $in->getBool() ? $in->getString() : null;
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->dimensionId);
		$out->putActorUniqueId($this->actorUniqueId);
		$out->putVector3($this->position);
		$out->putString($this->particleName);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_30){
			$out->putBool($this->molangVariablesJson !== null);
			if($this->molangVariablesJson !== null){
				$out->putString($this->molangVariablesJson);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSpawnParticleEffect($this);
	}
}
