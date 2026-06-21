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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class LevelSoundEventPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_SOUND_EVENT_PACKET;

	/** @see LevelSoundEvent */
	public int $sound;
	public Vector3 $position;
	public int $extraData = -1;
	public string $entityType = ":"; //???
	public bool $isBabyMob = false; //...
	public bool $disableRelativeVolume = false;
	public int $actorUniqueId = -1;
	public ?Vector3 $firePosition = null;

	/**
	 * @generate-create-func
	 */
	public static function create(
		int $sound,
		Vector3 $position,
		int $extraData,
		string $entityType,
		bool $isBabyMob,
		bool $disableRelativeVolume,
		int $actorUniqueId,
		?Vector3 $firePosition = null,
	) : self{
		$result = new self();
		$result->sound = $sound;
		$result->position = $position;
		$result->extraData = $extraData;
		$result->entityType = $entityType;
		$result->isBabyMob = $isBabyMob;
		$result->disableRelativeVolume = $disableRelativeVolume;
		$result->actorUniqueId = $actorUniqueId;
		$result->firePosition = $firePosition;
		return $result;
	}

	public static function nonActorSound(int $sound, Vector3 $position, bool $disableRelativeVolume, int $extraData = -1) : self{
		return self::create($sound, $position, $extraData, ":", false, $disableRelativeVolume, -1, null);
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->sound = $in->getUnsignedVarInt();
		$this->position = $in->getVector3();
		$this->extraData = $in->getVarInt();
		$this->entityType = $in->getString();
		$this->isBabyMob = $in->getBool();
		$this->disableRelativeVolume = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_70){
			$this->actorUniqueId = $in->getLLong(); //WHY IS THIS NON-STANDARD?
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
				$this->firePosition = $in->readOptional($in->getVector3(...));
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->sound);
		$out->putVector3($this->position);
		$out->putVarInt($this->extraData);
		$out->putString($this->entityType);
		$out->putBool($this->isBabyMob);
		$out->putBool($this->disableRelativeVolume);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_70){
			$out->putLLong($this->actorUniqueId);
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
				$out->writeOptional($this->firePosition, $out->putVector3(...));
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleLevelSoundEvent($this);
	}
}
