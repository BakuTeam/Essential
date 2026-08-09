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

class AwardAchievementPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::AWARD_ACHIEVEMENT_PACKET;

	private int $achievementId;

	/**
	 * @generate-create-func
	 */
	public static function create(int $achievementId) : self{
		$result = new self();
		$result->achievementId = $achievementId;
		return $result;
	}

	public function getAchievementId() : int{ return $this->achievementId; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->achievementId = $in->getLInt();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putLInt($this->achievementId);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleAwardAchievement($this);
	}
}
