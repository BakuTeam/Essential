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

class ClientboundDataDrivenUICloseScreenPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_DATA_DRIVEN_UI_CLOSE_SCREEN_PACKET;

	private ?int $formId = null;

	/**
	 * @generate-create-func
	 */
	public static function create(?int $formId = null) : self{
		$result = new self();
		$result->formId = $formId;
		return $result;
	}

	public function getFormId() : ?int{ return $this->formId; }

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
			$this->formId = $in->readOptional(fn() => $in->getLInt());
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
			$out->writeOptional($this->formId, fn(int $v) => $out->putLInt($v));
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundDataDrivenUICloseScreen($this);
	}
}
