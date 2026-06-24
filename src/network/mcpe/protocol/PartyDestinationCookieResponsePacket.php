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

class PartyDestinationCookieResponsePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::PARTY_DESTINATION_COOKIE_RESPONSE_PACKET;

	private string $cookie;
	private bool $accepted;

	/**
	 * @generate-create-func
	 */
	public static function create(string $cookie, bool $accepted) : self{
		$result = new self();
		$result->cookie = $cookie;
		$result->accepted = $accepted;
		return $result;
	}

	public function getCookie() : string{ return $this->cookie; }

	public function isAccepted() : bool{ return $this->accepted; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->cookie = $in->getString();
		$this->accepted = $in->getBool();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->cookie);
		$out->putBool($this->accepted);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePartyDestinationCookieResponse($this);
	}
}
