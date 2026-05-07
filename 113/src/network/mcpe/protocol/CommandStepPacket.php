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
use function json_decode;
use function json_encode;
use function strlen;

final class CommandStepPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_STEP_PACKET;

	public string $command;
	public string $overload;
	public int $uvarint1;
	public int $currentStep;
	public bool $done;
	public int $clientId;
	public mixed $inputJson = null;
	public mixed $outputJson = null;

	protected function decodePayload(PacketSerializer $in) : void{
		$this->command = $in->getString();
		$this->overload = $in->getString();
		$this->uvarint1 = $in->getUnsignedVarInt();
		$this->currentStep = $in->getUnsignedVarInt();
		$this->done = $in->getBool();
		$this->clientId = $in->getUnsignedVarLong();
		$this->inputJson = json_decode($in->getString(), true);
		$this->outputJson = json_decode($in->getString(), true);

		$remaining = strlen($in->getBuffer()) - $in->getOffset();
		if($remaining > 0){
			$in->get($remaining);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->command);
		$out->putString($this->overload);
		$out->putUnsignedVarInt($this->uvarint1);
		$out->putUnsignedVarInt($this->currentStep);
		$out->putBool($this->done);
		$out->putUnsignedVarLong($this->clientId);
		$out->putString(json_encode($this->inputJson) ?: "null");
		$out->putString(json_encode($this->outputJson) ?: "null");
		$out->put("\x00\x00\x00");
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCommandStep($this);
	}
}
