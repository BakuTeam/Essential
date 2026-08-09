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
use pocketmine\network\mcpe\protocol\types\inventory\FullContainerName;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

class InventorySlotPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_SLOT_PACKET;

	public int $windowId;
	public int $inventorySlot;
	public ?FullContainerName $containerName = null;
	public int $dynamicContainerSize;
	public ?ItemStackWrapper $storage = null;
	public ItemStackWrapper $item;

	/**
	 * @generate-create-func
	 */
	public static function create(int $windowId, int $inventorySlot, ?FullContainerName $containerName, int $dynamicContainerSize, ?ItemStackWrapper $storage, ItemStackWrapper $item) : self{
		$result = new self();
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
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$this->containerName = $in->readOptional(fn() => FullContainerName::read($in));
			$this->storage = $in->readOptional(fn() => $in->getNetworkItemStackDescriptor());
			$this->item = $in->getNetworkItemStackDescriptor();
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$this->containerName = FullContainerName::read($in);
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				$this->storage = ItemStackWrapper::read($in);
			}else{
				$this->dynamicContainerSize = $in->getUnsignedVarInt();
			}
			$this->item = ItemStackWrapper::read($in, true);
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->containerName = new FullContainerName(0, $in->getUnsignedVarInt());
			$this->item = ItemStackWrapper::read($in, true);
		}else{
			$this->item = ItemStackWrapper::read($in, true);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->windowId);
		$out->putUnsignedVarInt($this->inventorySlot);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->writeOptional($this->containerName, fn(FullContainerName $v) => $v->write($out));
			$out->writeOptional($this->storage, fn(ItemStackWrapper $v) => $out->putNetworkItemStackDescriptor($v));
			$out->putNetworkItemStackDescriptor($this->item);
		}else{
			if($this->containerName === null && $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
				throw new \InvalidArgumentException("ContainerName must be set for protocol " . $out->getProtocolId());
			}
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
				$this->containerName->write($out);
				if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
					($this->storage ?? new ItemStackWrapper(0, ItemStack::null()))->write($out);
				}else{
					$out->putUnsignedVarInt($this->dynamicContainerSize);
				}
			}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
				$out->putUnsignedVarInt($this->containerName->getDynamicId() ?? 0);
			}
			$this->item->write($out, true);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInventorySlot($this);
	}
}
