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

class ClientboundDataDrivenUIShowScreenPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_DATA_DRIVEN_UI_SHOW_SCREEN_PACKET;

	private string $screenId;
	private int $formId = 0;
	private ?int $dataInstanceId = null;

	/**
	 * @generate-create-func
	 */
	public static function create(string $screenId, int $formId = 0, ?int $dataInstanceId = null) : self{
		$result = new self();
		$result->screenId = $screenId;
		$result->formId = $formId;
		$result->dataInstanceId = $dataInstanceId;
		return $result;
	}

	public function getScreenId() : string{ return $this->screenId; }

	public function getFormId() : int{ return $this->formId; }

	public function getDataInstanceId() : ?int{ return $this->dataInstanceId; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->screenId = $in->getString();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
			$this->formId = $in->getLInt();
			$this->dataInstanceId = $in->readOptional(fn() => $in->getLInt());
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->screenId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
			$out->putLInt($this->formId);
			$out->writeOptional($this->dataInstanceId, fn(int $v) => $out->putLInt($v));
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundDataDrivenUIShowScreen($this);
	}
}
