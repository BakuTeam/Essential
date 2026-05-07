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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

final class UseItemPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::USE_ITEM_PACKET;

	public BlockPosition $blockPosition;
	public int $blockRuntimeId;
	public int $face;
	public Vector3 $clickPosition;
	public Vector3 $playerPosition;
	public int $hotbarSlot;
	public ItemStackWrapper $itemInHand;

	protected function decodePayload(PacketSerializer $in) : void{
		$this->blockPosition = $in->getBlockPosition(false);
		$this->blockRuntimeId = $in->getUnsignedVarInt();
		$this->face = $in->getVarInt();
		$this->clickPosition = $in->getVector3();
		$this->playerPosition = $in->getVector3();
		$this->hotbarSlot = $in->getVarInt();
		$this->itemInHand = ItemStackWrapper::legacy($in->getItemStackWithoutStackId());
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBlockPosition($this->blockPosition, false);
		$out->putUnsignedVarInt($this->blockRuntimeId);
		$out->putVarInt($this->face);
		$out->putVector3($this->clickPosition);
		$out->putVector3($this->playerPosition);
		$out->putVarInt($this->hotbarSlot);
		$out->putItemStackWithoutStackId($this->itemInHand->getItemStack());
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleUseItem($this);
	}
}
