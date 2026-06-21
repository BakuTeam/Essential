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
use function count;

/**
 * Sets the message shown on the death screen underneath "You died!"
 */
class DeathInfoPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::DEATH_INFO_PACKET;

	private string $messageTranslationKey;
	/** @var string[] */
	private array $messageParameters;

	/**
	 * @generate-create-func
	 * @param string[] $messageParameters
	 */
	public static function create(string $messageTranslationKey, array $messageParameters) : self{
		$result = new self();
		$result->messageTranslationKey = $messageTranslationKey;
		$result->messageParameters = $messageParameters;
		return $result;
	}

	public function getMessageTranslationKey() : string{ return $this->messageTranslationKey; }

	/** @return string[] */
	public function getMessageParameters() : array{ return $this->messageParameters; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->messageTranslationKey = $in->getString();

		$this->messageParameters = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; $i++){
			$this->messageParameters[] = $in->getString();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->messageTranslationKey);

		$out->putUnsignedVarInt(count($this->messageParameters));
		foreach($this->messageParameters as $parameter){
			$out->putString($parameter);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleDeathInfo($this);
	}
}
