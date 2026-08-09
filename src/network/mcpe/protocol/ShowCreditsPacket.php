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

class ShowCreditsPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_CREDITS_PACKET;

	public const STATUS_START_CREDITS = 0;
	public const STATUS_END_CREDITS = 1;

	public int $playerActorRuntimeId;
	public int $status;

	/**
	 * @generate-create-func
	 */
	public static function create(int $playerActorRuntimeId, int $status) : self{
		$result = new self();
		$result->playerActorRuntimeId = $playerActorRuntimeId;
		$result->status = $status;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->playerActorRuntimeId = $in->getActorRuntimeId();
		$this->status = $in->getVarInt();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorRuntimeId($this->playerActorRuntimeId);
		$out->putVarInt($this->status);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleShowCredits($this);
	}
}
