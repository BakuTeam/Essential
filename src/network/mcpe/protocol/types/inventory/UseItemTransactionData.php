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

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;

class UseItemTransactionData extends TransactionData{
	use GetTypeIdFromConstTrait;

	public const ID = InventoryTransactionPacket::TYPE_USE_ITEM;

	public const ACTION_CLICK_BLOCK = 0;
	public const ACTION_CLICK_AIR = 1;
	public const ACTION_BREAK_BLOCK = 2;
	public const ACTION_USE_AS_ATTACK = 3;

	private int $actionType;
	private TriggerType $triggerType;
	private BlockPosition $blockPosition;
	private int $face;
	private int $hotbarSlot;
	private ItemStackWrapper $itemInHand;
	private Vector3 $playerPosition;
	private Vector3 $clickPosition;
	private int $blockRuntimeId;
	private PredictedResult $clientInteractPrediction;
	private int $clientCooldownState = 0;

	public function getActionType() : int{
		return $this->actionType;
	}

	public function getTriggerType() : TriggerType{ return $this->triggerType; }

	public function getBlockPosition() : BlockPosition{
		return $this->blockPosition;
	}

	public function getFace() : int{
		return $this->face;
	}

	public function getHotbarSlot() : int{
		return $this->hotbarSlot;
	}

	public function getItemInHand() : ItemStackWrapper{
		return $this->itemInHand;
	}

	public function getPlayerPosition() : Vector3{
		return $this->playerPosition;
	}

	public function getClickPosition() : Vector3{
		return $this->clickPosition;
	}

	public function getBlockRuntimeId() : int{
		return $this->blockRuntimeId;
	}

	public function getClientInteractPrediction() : PredictedResult{ return $this->clientInteractPrediction; }

	public function getClientCooldownState() : int{ return $this->clientCooldownState; }

	protected function decodeData(PacketSerializer $stream) : void{
		$is2630 = $stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30;
		$this->actionType = $stream->getUnsignedVarInt();
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->triggerType = TriggerType::fromPacket($is2630 ? $stream->getByte() : $stream->getUnsignedVarInt());
		}
		$this->blockPosition = $stream->getBlockPosition($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$this->face = $is2630 ? $stream->getByte() : $stream->getVarInt();
		$this->hotbarSlot = $stream->getVarInt();
		$this->itemInHand = $is2630
			? $stream->getNetworkItemStackDescriptor(decodeExtraData: false)
			: ItemStackWrapper::read($stream, decodeExtraData: false);
		$this->playerPosition = $stream->getVector3();
		$this->clickPosition = $stream->getVector3();
		$this->blockRuntimeId = $stream->getUnsignedVarInt();
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->clientInteractPrediction = PredictedResult::fromPacket($is2630 ? $stream->getByte() : $stream->getUnsignedVarInt());
			if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
				$this->clientCooldownState = $stream->getByte();
			}
		}
	}

	protected function encodeData(PacketSerializer $stream) : void{
		$is2630 = $stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30;
		$stream->putUnsignedVarInt($this->actionType);
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			if($is2630){
				$stream->putByte($this->triggerType->value);
			}else{
				$stream->putUnsignedVarInt($this->triggerType->value);
			}
		}
		$stream->putBlockPosition($this->blockPosition, $stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		if($is2630){
			$stream->putByte($this->face);
		}else{
			$stream->putVarInt($this->face);
		}
		$stream->putVarInt($this->hotbarSlot);
		if($is2630){
			$stream->putNetworkItemStackDescriptor($this->itemInHand);
		}else{
			$this->itemInHand->write($stream);
		}
		$stream->putVector3($this->playerPosition);
		$stream->putVector3($this->clickPosition);
		$stream->putUnsignedVarInt($this->blockRuntimeId);
		if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			if($is2630){
				$stream->putByte($this->clientInteractPrediction->value);
			}else{
				$stream->putUnsignedVarInt($this->clientInteractPrediction->value);
			}
			if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
				$stream->putByte($this->clientCooldownState);
			}
		}
	}

	/**
	 * @param NetworkInventoryAction[] $actions
	 */
	public static function new(array $actions, int $actionType, TriggerType $triggerType, BlockPosition $blockPosition, int $face, int $hotbarSlot, ItemStackWrapper $itemInHand, Vector3 $playerPosition, Vector3 $clickPosition, int $blockRuntimeId, PredictedResult $clientInteractPrediction, int $clientCooldownState = 0) : self{
		$result = new self();
		$result->actions = $actions;
		$result->actionType = $actionType;
		$result->triggerType = $triggerType;
		$result->blockPosition = $blockPosition;
		$result->face = $face;
		$result->hotbarSlot = $hotbarSlot;
		$result->itemInHand = $itemInHand;
		$result->playerPosition = $playerPosition;
		$result->clickPosition = $clickPosition;
		$result->blockRuntimeId = $blockRuntimeId;
		$result->clientInteractPrediction = $clientInteractPrediction;
		$result->clientCooldownState = $clientCooldownState;
		return $result;
	}
}
