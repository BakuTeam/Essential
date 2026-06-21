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

class PlayerStartItemCooldownPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_START_ITEM_COOLDOWN_PACKET;

	private string $itemCategory;
	private int $cooldownTicks;

	/**
	 * @generate-create-func
	 */
	public static function create(string $itemCategory, int $cooldownTicks) : self{
		$result = new self();
		$result->itemCategory = $itemCategory;
		$result->cooldownTicks = $cooldownTicks;
		return $result;
	}

	public function getItemCategory() : string{ return $this->itemCategory; }

	public function getCooldownTicks() : int{ return $this->cooldownTicks; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->itemCategory = $in->getString();
		$this->cooldownTicks = $in->getVarInt();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->itemCategory);
		$out->putVarInt($this->cooldownTicks);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerStartItemCooldown($this);
	}
}
