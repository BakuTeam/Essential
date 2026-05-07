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
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

final class DropItemPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::DROP_ITEM_PACKET;

	public int $type;
	public ItemStackWrapper $item;

	protected function decodePayload(PacketSerializer $in) : void{
		$this->type = $in->getByte();
		$this->item = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->type);
		$out->putItemStackWithoutStackId($this->item->getItemStack());
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleDropItem($this);
	}
}
