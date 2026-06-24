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
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

class MobArmorEquipmentPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET;

	public int $actorRuntimeId;

	//this intentionally doesn't use an array because we don't want any implicit dependencies on internal order
	public ItemStackWrapper $head;
	public ItemStackWrapper $chest;
	public ItemStackWrapper $legs;
	public ItemStackWrapper $feet;
	public ItemStackWrapper $body;

	/**
	 * @generate-create-func
	 */
	public static function create(int $actorRuntimeId, ItemStackWrapper $head, ItemStackWrapper $chest, ItemStackWrapper $legs, ItemStackWrapper $feet, ItemStackWrapper $body) : self{
		$result = new self();
		$result->actorRuntimeId = $actorRuntimeId;
		$result->head = $head;
		$result->chest = $chest;
		$result->legs = $legs;
		$result->feet = $feet;
		$result->body = $body;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->actorRuntimeId = $in->getActorRuntimeId();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$this->head = $in->getNetworkItemStackDescriptor(decodeExtraData: false);
			$this->chest = $in->getNetworkItemStackDescriptor(decodeExtraData: false);
			$this->legs = $in->getNetworkItemStackDescriptor(decodeExtraData: false);
			$this->feet = $in->getNetworkItemStackDescriptor(decodeExtraData: false);
			$this->body = $in->getNetworkItemStackDescriptor(decodeExtraData: false);
			return;
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220){
			$this->head = ItemStackWrapper::read($in, decodeExtraData: false);
			$this->chest = ItemStackWrapper::read($in, decodeExtraData: false);
			$this->legs = ItemStackWrapper::read($in, decodeExtraData: false);
			$this->feet = ItemStackWrapper::read($in, decodeExtraData: false);
		}else{
			$this->head = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
			$this->chest = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
			$this->legs = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
			$this->feet = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->body = ItemStackWrapper::read($in, decodeExtraData: false);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorRuntimeId($this->actorRuntimeId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$out->putNetworkItemStackDescriptor($this->head);
			$out->putNetworkItemStackDescriptor($this->chest);
			$out->putNetworkItemStackDescriptor($this->legs);
			$out->putNetworkItemStackDescriptor($this->feet);
			$out->putNetworkItemStackDescriptor($this->body);
			return;
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220){
			$this->head->write($out);
			$this->chest->write($out);
			$this->legs->write($out);
			$this->feet->write($out);
		}else{
			$out->putItemStackWithoutStackId($this->head->getItemStack());
			$out->putItemStackWithoutStackId($this->chest->getItemStack());
			$out->putItemStackWithoutStackId($this->legs->getItemStack());
			$out->putItemStackWithoutStackId($this->feet->getItemStack());
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->body->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleMobArmorEquipment($this);
	}
}
