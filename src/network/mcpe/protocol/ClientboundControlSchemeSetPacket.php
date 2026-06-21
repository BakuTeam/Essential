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
use pocketmine\network\mcpe\protocol\types\ControlScheme;

class ClientboundControlSchemeSetPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_CONTROL_SCHEME_SET_PACKET;

	private ControlScheme $scheme;

	/**
	 * @generate-create-func
	 */
	public static function create(ControlScheme $scheme) : self{
		$result = new self();
		$result->scheme = $scheme;
		return $result;
	}

	public function getScheme() : ControlScheme{ return $this->scheme; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->scheme = ControlScheme::fromPacket($in->getByte());
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->scheme->value);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundControlSchemeSet($this);
	}
}
