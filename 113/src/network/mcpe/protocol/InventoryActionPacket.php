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
use function strlen;
use function substr;

final class InventoryActionPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_ACTION_PACKET;

	public string $payload = "";

	protected function decodePayload(PacketSerializer $in) : void{
		$this->payload = substr($in->getBuffer(), $in->getOffset());
		if($this->payload !== ""){
			$in->get(strlen($this->payload));
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->put($this->payload);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInventoryAction($this);
	}
}
