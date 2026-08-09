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
use pocketmine\network\mcpe\protocol\types\BlockPosition;

class CommandBlockUpdatePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET;

	public bool $isBlock;

	public BlockPosition $blockPosition;
	public int $commandBlockMode;
	public bool $isRedstoneMode;
	public bool $isConditional;

	public int $minecartActorRuntimeId;

	public string $command;
	public string $lastOutput;
	public string $name;
	public string $filteredName;
	public bool $shouldTrackOutput;
	public int $tickDelay;
	public bool $executeOnFirstTick;

	protected function decodePayload(PacketSerializer $in) : void{
		$this->isBlock = $in->getBool();

		if($this->isBlock){
		$this->blockPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
			$this->commandBlockMode = $in->getUnsignedVarInt();
			$this->isRedstoneMode = $in->getBool();
			$this->isConditional = $in->getBool();
		}else{
			//Minecart with command block
			$this->minecartActorRuntimeId = $in->getActorRuntimeId();
		}

		$this->command = $in->getString();
		$this->lastOutput = $in->getString();
		$this->name = $in->getString();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_60){
			$this->filteredName = $in->getString();
		}
		$this->shouldTrackOutput = $in->getBool();
		$this->tickDelay = $in->getLInt();
		$this->executeOnFirstTick = $in->getBool();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBool($this->isBlock);

		if($this->isBlock){
			$out->putBlockPosition($this->blockPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
			$out->putUnsignedVarInt($this->commandBlockMode);
			$out->putBool($this->isRedstoneMode);
			$out->putBool($this->isConditional);
		}else{
			$out->putActorRuntimeId($this->minecartActorRuntimeId);
		}

		$out->putString($this->command);
		$out->putString($this->lastOutput);
		$out->putString($this->name);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_60){
			$out->putString($this->filteredName);
		}
		$out->putBool($this->shouldTrackOutput);
		$out->putLInt($this->tickDelay);
		$out->putBool($this->executeOnFirstTick);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCommandBlockUpdate($this);
	}
}
