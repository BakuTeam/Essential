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

class DisconnectPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::DISCONNECT_PACKET;

	public int $reason; //TODO: add constants / enum
	public ?string $message;
	public ?string $filteredMessage;

	/**
	 * @generate-create-func
	 */
	public static function create(int $reason, ?string $message, ?string $filteredMessage) : self{
		$result = new self();
		$result->reason = $reason;
		$result->message = $message;
		$result->filteredMessage = $filteredMessage;
		return $result;
	}

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_40){
			$this->reason = $in->getVarInt();
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$type = $in->getUnsignedVarInt();
		}else{
			$type = $in->getBool() ? 1 : 0;
		}

		$this->message = $type === 0 ? $in->getString() : null;
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->filteredMessage = $type === 0 ? $in->getString() : null;
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_40){
			$out->putVarInt($this->reason);
		}
		$skipMessage = $this->message === null && $this->filteredMessage === null;
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->putUnsignedVarInt($skipMessage ? 1 : 0);
		}else{
			$out->putBool($skipMessage);
		}

		if(!$skipMessage){
			$out->putString($this->message ?? "");
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
				$out->putString($this->filteredMessage ?? "");
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleDisconnect($this);
	}
}
