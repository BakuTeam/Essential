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

class AutomationClientConnectPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::AUTOMATION_CLIENT_CONNECT_PACKET;

	public string $serverUri;

	/**
	 * @generate-create-func
	 */
	public static function create(string $serverUri) : self{
		$result = new self();
		$result->serverUri = $serverUri;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->serverUri = $in->getString();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->serverUri);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleAutomationClientConnect($this);
	}
}
