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

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\utils\BinaryDataException;
use function count;

abstract class TransactionData{
	/** @var NetworkInventoryAction[] */
	protected array $actions = [];

	/**
	 * @return NetworkInventoryAction[]
	 */
	final public function getActions() : array{
		return $this->actions;
	}

	abstract public function getTypeId() : int;

	/**
	 * @throws BinaryDataException
	 * @throws PacketDecodeException
	 */
	final public function decode(PacketSerializer $stream) : void{

		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$hasValue = $stream->getBool();
			if($hasValue){
				$actionCount = $stream->getUnsignedVarInt();
				for($i = 0; $i < $actionCount; ++$i){
					$this->actions[] = (new NetworkInventoryAction())->read($stream, false);
				}
				$this->decodeData($stream);
			}
			return;
		}

		$hasItemStackId = false;
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0 && $stream->getProtocolId() < ProtocolInfo::PROTOCOL_1_16_220){
			$hasItemStackId = $stream->getBool();
		}

		$actionCount = $stream->getUnsignedVarInt();
		for($i = 0; $i < $actionCount; ++$i){
			$this->actions[] = (new NetworkInventoryAction())->read($stream, $hasItemStackId);
		}
		$this->decodeData($stream);
	}

	/**
	 * Decodes the transaction as embedded in PlayerAuthInputPacket's item interaction data.
	 *
	 * Since 1.26.30 this is NOT the same wire format as {@see self::decode()}: the auth-input
	 * variant carries only the inventory actions (in the legacy format) and none of the use-item
	 * payload (action type, block position, item in hand, etc.), which now travels exclusively via
	 * the standalone InventoryTransactionPacket.
	 *
	 * @throws BinaryDataException
	 * @throws PacketDecodeException
	 */
	final public function decodeAuthInput(PacketSerializer $stream) : void{
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$actionCount = $stream->getUnsignedVarInt();
			for($i = 0; $i < $actionCount; ++$i){
				$this->actions[] = (new NetworkInventoryAction())->readAuthInput($stream);
			}
			return;
		}

		//older protocols use the same format for both auth-input and standalone transactions
		$this->decode($stream);
	}

	final public function encodeAuthInput(PacketSerializer $stream) : void{
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$stream->putUnsignedVarInt(count($this->actions));
			foreach($this->actions as $action){
				$action->writeAuthInput($stream);
			}
			return;
		}

		$this->encode($stream);
	}

	/**
	 * @throws BinaryDataException
	 * @throws PacketDecodeException
	 */
	abstract protected function decodeData(PacketSerializer $stream) : void;

	final public function encode(PacketSerializer $stream) : void{

		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			//the dummy optional bool for trData is always present (1) in the standalone transaction format
			$stream->putBool(true);
			$stream->putUnsignedVarInt(count($this->actions));
			foreach($this->actions as $action){
				$action->write($stream, false);
			}
			$this->encodeData($stream);
			return;
		}

		$hasItemStackId = false;
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0 && $stream->getProtocolId() < ProtocolInfo::PROTOCOL_1_16_220){
			$stream->putBool($hasItemStackId);
		}

		$stream->putUnsignedVarInt(count($this->actions));
		foreach($this->actions as $action){
			$action->write($stream, $hasItemStackId);
		}
		$this->encodeData($stream);
	}

	abstract protected function encodeData(PacketSerializer $stream) : void;
}
