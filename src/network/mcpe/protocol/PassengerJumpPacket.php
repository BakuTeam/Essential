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

class PassengerJumpPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::PASSENGER_JUMP_PACKET;

	public int $jumpStrength; //percentage

	/**
	 * @generate-create-func
	 */
	public static function create(int $jumpStrength) : self{
		$result = new self();
		$result->jumpStrength = $jumpStrength;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->jumpStrength = $in->getVarInt();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putVarInt($this->jumpStrength);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePassengerJump($this);
	}
}
