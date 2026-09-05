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

class HurtArmorPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::HURT_ARMOR_PACKET;

	public int $cause;
	public int $health;
	public int $armorSlotFlags;

	/**
	 * @generate-create-func
	 */
	public static function create(int $cause, int $health, int $armorSlotFlags) : self{
		$result = new self();
		$result->cause = $cause;
		$result->health = $health;
		$result->armorSlotFlags = $armorSlotFlags;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$this->cause = $in->getVarInt();
		}
		$this->health = $in->getVarInt();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
			$this->armorSlotFlags = $in->getUnsignedVarLong();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$out->putVarInt($this->cause);
		}
		$out->putVarInt($this->health);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
			$out->putUnsignedVarLong($this->armorSlotFlags);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleHurtArmor($this);
	}
}
