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

class TickSyncPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::TICK_SYNC_PACKET;

	private int $clientSendTime;
	private int $serverReceiveTime;

	/**
	 * @generate-create-func
	 */
	private static function create(int $clientSendTime, int $serverReceiveTime) : self{
		$result = new self();
		$result->clientSendTime = $clientSendTime;
		$result->serverReceiveTime = $serverReceiveTime;
		return $result;
	}

	public static function request(int $clientTime) : self{
		return self::create($clientTime, 0 /* useless, but always written anyway */);
	}

	public static function response(int $clientSendTime, int $serverReceiveTime) : self{
		return self::create($clientSendTime, $serverReceiveTime);
	}

	public function getClientSendTime() : int{
		return $this->clientSendTime;
	}

	public function getServerReceiveTime() : int{
		return $this->serverReceiveTime;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->clientSendTime = $in->getLLong();
		$this->serverReceiveTime = $in->getLLong();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putLLong($this->clientSendTime);
		$out->putLLong($this->serverReceiveTime);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleTickSync($this);
	}
}
