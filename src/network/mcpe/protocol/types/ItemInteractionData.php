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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\inventory\InventoryTransactionChangedSlotsHack;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use function count;

final class ItemInteractionData{
	/**
	 * @param InventoryTransactionChangedSlotsHack[] $requestChangedSlots
	 */
	public function __construct(
		private int $requestId,
		private ?array $requestChangedSlots,
		private UseItemTransactionData $transactionData
	){}

	public function getRequestId() : int{
		return $this->requestId;
	}

	/**
	 * @return InventoryTransactionChangedSlotsHack[]
	 */
	public function getRequestChangedSlots() : ?array{
		return $this->requestChangedSlots;
	}

	public function getTransactionData() : UseItemTransactionData{
		return $this->transactionData;
	}

	public static function read(PacketSerializer $in) : self{
		$requestId = $in->getVarInt();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$requestChangedSlots = $in->readOptional(function() use ($in) : array{
				$result = [];
				for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
					$result[] = InventoryTransactionChangedSlotsHack::read($in);
				}
				return $result;
			});
			$in->readDummyOptional();
			$in->readDummyOptional();
			$transactionData = new UseItemTransactionData();
			$transactionData->decode($in);
			return new self($requestId, $requestChangedSlots, $transactionData);
		}
		$requestChangedSlots = [];
		if($requestId !== 0){
			$len = $in->getUnsignedVarInt();
			for($i = 0; $i < $len; ++$i){
				$requestChangedSlots[] = InventoryTransactionChangedSlotsHack::read($in);
			}
		}
		$transactionData = new UseItemTransactionData();
		$transactionData->decodeAuthInput($in);
		return new ItemInteractionData($requestId, $requestChangedSlots, $transactionData);
	}

	public function write(PacketSerializer $out) : void{
		$out->putVarInt($this->requestId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$out->writeOptional($this->requestChangedSlots, function(array $slots) use ($out) : void{
				$out->putUnsignedVarInt(count($slots));
				foreach($slots as $slot){
					$slot->write($out);
				}
			});
			$out->writeDummyOptional();
			$out->writeDummyOptional();
			$this->transactionData->encode($out);
			return;
		}
		if($this->requestId !== 0){
			$out->putUnsignedVarInt(count($this->requestChangedSlots));
			foreach($this->requestChangedSlots as $changedSlot){
				$changedSlot->write($out);
			}
		}
		$this->transactionData->encodeAuthInput($out);
	}
}
