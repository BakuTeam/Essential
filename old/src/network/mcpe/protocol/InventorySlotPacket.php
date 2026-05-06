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
use pocketmine\network\mcpe\protocol\types\inventory\FullContainerName;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

class InventorySlotPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_SLOT_PACKET;

	public int $windowId;
	public int $inventorySlot;
	public FullContainerName $containerName;
	public int $dynamicContainerSize;
	public ItemStackWrapper $storage;
	public ItemStackWrapper $item;

	/**
	 * @generate-create-func
	 */
	public static function create(int $windowId, int $inventorySlot, FullContainerName $containerName, int $dynamicContainerSize, ItemStackWrapper $storage, ItemStackWrapper $item) : self{
		$result = new self;
		$result->windowId = $windowId;
		$result->inventorySlot = $inventorySlot;
		$result->containerName = $containerName;
		$result->dynamicContainerSize = $dynamicContainerSize;
		$result->storage = $storage;
		$result->item = $item;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->windowId = $in->getUnsignedVarInt();
		$this->inventorySlot = $in->getUnsignedVarInt();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$this->containerName = FullContainerName::read($in);
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				$this->storage = ItemStackWrapper::read($in);
			}else{
				$this->dynamicContainerSize = $in->getUnsignedVarInt();
			}
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->containerName = new FullContainerName(0, $in->getUnsignedVarInt());
		}
		$this->item = ItemStackWrapper::read($in, true);
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->windowId);
		$out->putUnsignedVarInt($this->inventorySlot);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$this->containerName->write($out);
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				$this->storage->write($out);
			}else{
				$out->putUnsignedVarInt($this->dynamicContainerSize);
			}
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$out->putUnsignedVarInt($this->containerName->getDynamicId() ?? 0);
		}
		$this->item->write($out, true);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInventorySlot($this);
	}
}
