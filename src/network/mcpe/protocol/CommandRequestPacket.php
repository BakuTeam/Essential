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
use pocketmine\network\mcpe\protocol\types\command\CommandOriginData;

class CommandRequestPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_REQUEST_PACKET;

	public string $command;
	public CommandOriginData $originData;
	public bool $isInternal;
	public int $version;
	public string $versionString = "";

	/**
	 * @generate-create-func
	 */
	public static function create(string $command, CommandOriginData $originData, bool $isInternal, int $version) : self{
		$result = new self();
		$result->command = $command;
		$result->originData = $originData;
		$result->isInternal = $isInternal;
		$result->version = $version;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->command = $in->getString();
		$this->originData = $in->getCommandOriginData($in->getProtocolId());
		$this->isInternal = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_60){
			if ($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130) {
				$this->versionString = $in->getString();
				$this->version = 0;
			} else {
				$this->version = $in->getVarInt();
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->command);
		$out->putCommandOriginData($this->originData);
		$out->putBool($this->isInternal);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_60){
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
				$out->putString($this->versionString !== "" ? $this->versionString : (string) $this->version);
			}else{
				$out->putVarInt($this->version);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCommandRequest($this);
	}
}
