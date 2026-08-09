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
use pocketmine\network\mcpe\protocol\types\inventory\InventoryTransactionChangedSlotsHack;
use pocketmine\network\mcpe\protocol\types\inventory\MismatchTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\NormalTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\ReleaseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\TransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use function count;

/**
 * This packet effectively crams multiple packets into one.
 */
class InventoryTransactionPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::INVENTORY_TRANSACTION_PACKET;

	public const TYPE_NORMAL = 0;
	public const TYPE_MISMATCH = 1;
	public const TYPE_USE_ITEM = 2;
	public const TYPE_USE_ITEM_ON_ENTITY = 3;
	public const TYPE_RELEASE_ITEM = 4;

	public int $requestId;
	/** @var InventoryTransactionChangedSlotsHack[] */
	public array $requestChangedSlots;
	/** @var bool */
	public $hasItemStackIds = true;
	public ?TransactionData $trData = null;

	/**
	 * @generate-create-func
	 * @param InventoryTransactionChangedSlotsHack[] $requestChangedSlots
	 */
	public static function create(int $requestId, array $requestChangedSlots, ?TransactionData $trData) : self{
		$result = new self();
		$result->requestId = $requestId;
		$result->requestChangedSlots = $requestChangedSlots;
		$result->trData = $trData;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$is2630 = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30;
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$this->requestId = $in->readLegacyItemStackRequestId();
			$this->requestChangedSlots = [];
			$hasChangedSlots = $is2630 ? $in->getBool() : $this->requestId !== 0;
			if($hasChangedSlots){
				for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
					$this->requestChangedSlots[] = InventoryTransactionChangedSlotsHack::read($in);
				}
			}
		}

		$transactionType = $is2630 ? $in->readOptional(fn() => $in->getUnsignedVarInt()) : $in->getUnsignedVarInt();

		// if($in->getProtocolId() < ProtocolInfo::PROTOCOL_1_16_220){
		// 	$this->hasItemStackIds = $in->getBool();
		// }

		$this->trData = match($transactionType){
			null => null,
			NormalTransactionData::ID => new NormalTransactionData(),
			MismatchTransactionData::ID => new MismatchTransactionData(),
			UseItemTransactionData::ID => new UseItemTransactionData(),
			UseItemOnEntityTransactionData::ID => new UseItemOnEntityTransactionData(),
			ReleaseItemTransactionData::ID => new ReleaseItemTransactionData(),
			default => throw new PacketDecodeException("Unknown transaction type $transactionType"),
		};

		$this->trData?->decode($in);
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$is2630 = $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30;
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$out->writeLegacyItemStackRequestId($this->requestId);
			$hasChangedSlots = $this->requestId !== 0;
			if($is2630){
				$out->putBool($hasChangedSlots);
			}
			if($hasChangedSlots){
				$out->putUnsignedVarInt(count($this->requestChangedSlots));
				foreach($this->requestChangedSlots as $changedSlots){
					$changedSlots->write($out);
				}
			}
		}

		if($is2630){
			$out->writeOptional($this->trData?->getTypeId(), fn(int $typeId) => $out->putUnsignedVarInt($typeId));
		}else{
			$out->putUnsignedVarInt($this->trData->getTypeId());
		}

		// if($out->getProtocolId() < ProtocolInfo::PROTOCOL_1_16_220){
		// 	$out->putBool($this->hasItemStackIds);
		// }

		$this->trData?->encode($out);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleInventoryTransaction($this);
	}
}
