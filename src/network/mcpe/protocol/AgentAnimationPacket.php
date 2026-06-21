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

class AgentAnimationPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::AGENT_ANIMATION_PACKET;

	public const TYPE_ARM_SWING = 0;
	public const TYPE_SHRUG = 1;

	private int $animationType;
	private int $actorRuntimeId;

	/**
	 * @generate-create-func
	 */
	public static function create(int $animationType, int $actorRuntimeId) : self{
		$result = new self();
		$result->animationType = $animationType;
		$result->actorRuntimeId = $actorRuntimeId;
		return $result;
	}

	public function getAnimationType() : int{ return $this->animationType; }

	public function getActorRuntimeId() : int{ return $this->actorRuntimeId; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->animationType = $in->getByte();
		$this->actorRuntimeId = $in->getActorRuntimeId();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->animationType);
		$out->putActorRuntimeId($this->actorRuntimeId);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleAgentAnimation($this);
	}
}
