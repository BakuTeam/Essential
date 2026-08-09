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
use pocketmine\network\mcpe\protocol\types\GraphicsMode;

class UpdateClientOptionsPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_CLIENT_OPTIONS_PACKET;

	private ?GraphicsMode $graphicsMode;
	private ?bool $filterProfanityChange = null;

	/**
	 * @generate-create-func
	 */
	public static function create(?GraphicsMode $graphicsMode, ?bool $filterProfanityChange = null) : self{
		$result = new self();
		$result->graphicsMode = $graphicsMode;
		$result->filterProfanityChange = $filterProfanityChange;
		return $result;
	}

	public function getGraphicsMode() : ?GraphicsMode{ return $this->graphicsMode; }

	public function getFilterProfanityChange() : ?bool{ return $this->filterProfanityChange; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->graphicsMode = $in->readOptional(fn() => GraphicsMode::fromPacket($in->getByte()));
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$this->filterProfanityChange = $in->readOptional($in->getBool(...));
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->writeOptional($this->graphicsMode, fn(GraphicsMode $v) => $out->putByte($v->value));
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->writeOptional($this->filterProfanityChange, $out->putBool(...));
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleUpdateClientOptions($this);
	}
}
