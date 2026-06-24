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
use pocketmine\network\mcpe\protocol\types\inventory\FullContainerName;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use function count;

class InventoryContentPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_CONTENT_PACKET;

	public int $windowId;
	/** @var ItemStackWrapper[] */
	public array $items = [];
	public FullContainerName $containerName;
	public int $dynamicContainerSize;
	public ItemStackWrapper $storage;

	/**
	 * @generate-create-func
	 * @param ItemStackWrapper[] $items
	 */
	public static function create(int $windowId, array $items, FullContainerName $containerName, int $dynamicContainerSize, ItemStackWrapper $storage) : self{
		$result = new self();
		$result->windowId = $windowId;
		$result->items = $items;
		$result->containerName = $containerName;
		$result->dynamicContainerSize = $dynamicContainerSize;
		$result->storage = $storage;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->windowId = $in->getUnsignedVarInt();
		$count = $in->getUnsignedVarInt();
		$useDescriptor = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30;
		for($i = 0; $i < $count; ++$i){
			$this->items[] = $useDescriptor ? $in->getNetworkItemStackDescriptor() : ItemStackWrapper::read($in, true);
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$this->containerName = FullContainerName::read($in);
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				$this->storage = $useDescriptor ? $in->getNetworkItemStackDescriptor() : ItemStackWrapper::read($in);
			}else{
				$this->dynamicContainerSize = $in->getUnsignedVarInt();
			}
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->containerName = new FullContainerName(0, $in->getUnsignedVarInt());
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->windowId);
		$out->putUnsignedVarInt(count($this->items));
		$useDescriptor = $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30;
		foreach($this->items as $item){
			if($useDescriptor){
				$out->putNetworkItemStackDescriptor($item);
			}else{
				$item->write($out, true);
			}
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$this->containerName->write($out);
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				if($useDescriptor){
					$out->putNetworkItemStackDescriptor($this->storage);
				}else{
					$this->storage->write($out);
				}
			}else{
				$out->putUnsignedVarInt($this->dynamicContainerSize);
			}
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$out->putUnsignedVarInt($this->containerName->getDynamicId() ?? 0);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInventoryContent($this);
	}
}
