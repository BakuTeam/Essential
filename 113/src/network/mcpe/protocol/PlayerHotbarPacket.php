<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use function count;

class PlayerHotbarPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_HOTBAR_PACKET;

	public int $selectedHotbarSlot;
	public int $windowId = ContainerIds::INVENTORY;
	/** @var int[] */
	public array $slots = [];
	public bool $selectHotbarSlot = true;

	/**
	 * @generate-create-func
	 */
	public static function create(int $selectedHotbarSlot, int $windowId, bool $selectHotbarSlot) : self{
		$result = new self;
		$result->selectedHotbarSlot = $selectedHotbarSlot;
		$result->windowId = $windowId;
		$result->selectHotbarSlot = $selectHotbarSlot;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->selectedHotbarSlot = $in->getUnsignedVarInt();
		$this->windowId = $in->getByte();
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			for($slot = 0, $slots = $in->getUnsignedVarInt(); $slot < $slots; ++$slot){
				$this->slots[$slot] = $in->getUnsignedVarInt();
			}
			return;
		}
		$this->selectHotbarSlot = $in->getBool();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->selectedHotbarSlot);
		$out->putByte($this->windowId);
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$out->putUnsignedVarInt(count($this->slots));
			foreach($this->slots as $slot){
				$out->putUnsignedVarInt($slot);
			}
			return;
		}
		$out->putBool($this->selectHotbarSlot);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerHotbar($this);
	}
}
