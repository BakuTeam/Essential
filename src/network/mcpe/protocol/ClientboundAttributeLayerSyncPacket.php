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
use function strlen;

class ClientboundAttributeLayerSyncPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_ATTRIBUTE_LAYER_SYNC_PACKET;

	private string $payload = "";

	/**
	 * @generate-create-func
	 */
	public static function create(string $payload = "") : self{
		$result = new self();
		$result->payload = $payload;
		return $result;
	}

	public function getPayload() : string{ return $this->payload; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->payload = $in->getOffset() >= strlen($in->getBuffer()) ? "" : $in->getRemaining();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->put($this->payload);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundAttributeLayerSync($this);
	}
}
