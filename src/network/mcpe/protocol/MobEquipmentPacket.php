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
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

class MobEquipmentPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_EQUIPMENT_PACKET;

	public int $actorRuntimeId;
	public ItemStackWrapper $item;
	public int $inventorySlot;
	public int $hotbarSlot;
	public int $windowId = 0;

	/**
	 * @generate-create-func
	 */
	public static function create(int $actorRuntimeId, ItemStackWrapper $item, int $inventorySlot, int $hotbarSlot, int $windowId) : self{
		$result = new self();
		$result->actorRuntimeId = $actorRuntimeId;
		$result->item = $item;
		$result->inventorySlot = $inventorySlot;
		$result->hotbarSlot = $hotbarSlot;
		$result->windowId = $windowId;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->actorRuntimeId = $in->getActorRuntimeId();

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$this->item = $in->getNetworkItemStackDescriptor(false);
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220){
			$this->item = ItemStackWrapper::read($in, decodeExtraData: false);
		}else{
			$this->item = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
		}

		$this->inventorySlot = $in->getByte();
		$this->hotbarSlot = $in->getByte();
		$this->windowId = $in->getByte();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorRuntimeId($this->actorRuntimeId);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->putNetworkItemStackDescriptor($this->item);
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220){
			$this->item->write($out);
		}else{
			$out->putItemStackWithoutStackId($this->item->getItemStack());
		}

		$out->putByte($this->inventorySlot);
		$out->putByte($this->hotbarSlot);
		$out->putByte($this->windowId);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleMobEquipment($this);
	}
}
