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

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\network\mcpe\protocol\types\InteractionMode;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequest;
use pocketmine\network\mcpe\protocol\types\ItemInteractionData;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\network\mcpe\protocol\types\PlayerBlockAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionStopBreak;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionWithBlockInfo;
use pocketmine\network\mcpe\protocol\types\PlayMode;
use function assert;
use function count;

class PlayerAuthInputPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_AUTH_INPUT_PACKET;

	private Vector3 $position;
	private float $pitch;
	private float $yaw;
	private float $headYaw;
	private float $moveVecX;
	private float $moveVecZ;
	private int $inputFlags;
	private int $inputMode;
	private int $playMode;
	private int $interactionMode;
	private ?Vector3 $vrGazeDirection = null;
	private Vector2 $interactRotation;
	private int $tick;
	private Vector3 $delta;
	public ?ItemInteractionData $itemInteractionData = null;
	private ?ItemStackRequest $itemStackRequest = null;
	/** @var PlayerBlockAction[]|null */
	private ?array $blockActions = null;
	private ?int $clientPredictedVehicleActorUniqueId = null;
	private float $analogMoveVecX;
	private float $analogMoveVecZ;
	private float $vehicleVecX;
	private float $vehicleVecZ;
	private Vector3 $cameraOrientation;
	private Vector2 $rawMoveVector;

        /**
	 * @generate-create-func
	 * @param PlayerBlockAction[] $blockActions
	 */
	private static function internalCreate(
		Vector3 $position,
		float $pitch,
		float $yaw,
		float $headYaw,
		float $moveVecX,
		float $moveVecZ,
		int $inputFlags,
		int $inputMode,
		int $playMode,
		int $interactionMode,
		?Vector3 $vrGazeDirection,
		Vector2 $interactRotation,
		int $tick,
		Vector3 $delta,
		?ItemInteractionData $itemInteractionData,
		?ItemStackRequest $itemStackRequest,
		?array $blockActions,
		?PlayerAuthInputVehicleInfo $vehicleInfo,
		float $analogMoveVecX,
		float $analogMoveVecZ,
		Vector3 $cameraOrientation,
		Vector2 $rawMoveVector,
	) : self{
		$result = new self;
		$result->position = $position;
		$result->pitch = $pitch;
		$result->yaw = $yaw;
		$result->headYaw = $headYaw;
		$result->moveVecX = $moveVecX;
		$result->moveVecZ = $moveVecZ;
		$result->inputFlags = $inputFlags;
		$result->inputMode = $inputMode;
		$result->playMode = $playMode;
		$result->interactionMode = $interactionMode;
		$result->vrGazeDirection = $vrGazeDirection;
		$result->interactRotation = $interactRotation;
		$result->tick = $tick;
		$result->delta = $delta;
		$result->itemInteractionData = $itemInteractionData;
		$result->itemStackRequest = $itemStackRequest;
		$result->blockActions = $blockActions;
		$result->vehicleInfo = $vehicleInfo;
		$result->analogMoveVecX = $analogMoveVecX;
		$result->analogMoveVecZ = $analogMoveVecZ;
		$result->cameraOrientation = $cameraOrientation;
		$result->rawMoveVector = $rawMoveVector;
		return $result;
	}

	/**
	 * @param int                      $inputFlags @see PlayerAuthInputFlags
	 * @param int                      $inputMode @see InputMode
	 * @param int                      $playMode @see PlayMode
	 * @param int                      $interactionMode @see InteractionMode
	 * @param Vector3|null             $vrGazeDirection only used when PlayMode::VR
	 * @param PlayerBlockAction[]|null $blockActions Blocks that the client has interacted with
	 */
	public static function create(
		Vector3 $position,
		float $pitch,
		float $yaw,
		float $headYaw,
		float $moveVecX,
		float $moveVecZ,
		int $inputFlags,
		int $inputMode,
		int $playMode,
		int $interactionMode,
		?Vector3 $vrGazeDirection,
		Vector2 $interactRotation,
		int $tick,
		Vector3 $delta,
		?ItemInteractionData $itemInteractionData,
		?ItemStackRequest $itemStackRequest,
		?array $blockActions,
		?PlayerAuthInputVehicleInfo $vehicleInfo,
		float $analogMoveVecX,
		float $analogMoveVecZ,
		Vector3 $cameraOrientation,
		Vector2 $rawMoveVector,
	) : self{

		if($playMode === PlayMode::VR and $vrGazeDirection === null){
			//yuck, can we get a properly written packet just once? ...
			throw new \InvalidArgumentException("Gaze direction must be provided for VR play mode");
		}

		$realInputFlags = $inputFlags & ~((1 << PlayerAuthInputFlags::PERFORM_ITEM_STACK_REQUEST) | (1 << PlayerAuthInputFlags::PERFORM_ITEM_INTERACTION) | (1 << PlayerAuthInputFlags::PERFORM_BLOCK_ACTIONS));
		if($itemStackRequest !== null){
			$realInputFlags |= 1 << PlayerAuthInputFlags::PERFORM_ITEM_STACK_REQUEST;
		}
		if($itemInteractionData !== null){
			$realInputFlags |= 1 << PlayerAuthInputFlags::PERFORM_ITEM_INTERACTION;
		}
		if($blockActions !== null){
			$realInputFlags |= 1 << PlayerAuthInputFlags::PERFORM_BLOCK_ACTIONS;
		}

		return self::internalCreate(
			$position,
			$pitch,
			$yaw,
			$headYaw,
			$moveVecX,
			$moveVecZ,
			$realInputFlags,
			$inputMode,
			$playMode,
			$interactionMode,
			$vrGazeDirection?->asVector3(),
			$interactRotation,
			$tick,
			$delta,
			$itemInteractionData,
			$itemStackRequest,
			$blockActions,
			$vehicleInfo,
			$analogMoveVecX,
			$analogMoveVecZ,
			$cameraOrientation,
			$rawMoveVector,
		);
	}

	public function getPosition() : Vector3{
		return $this->position;
	}

	public function getPitch() : float{
		return $this->pitch;
	}

	public function getYaw() : float{
		return $this->yaw;
	}

	public function getHeadYaw() : float{
		return $this->headYaw;
	}

	public function getMoveVecX() : float{
		return $this->moveVecX;
	}

	public function getMoveVecZ() : float{
		return $this->moveVecZ;
	}

	public function getVehicleVecX(): float {
		return $this->vehicleVecX;
	}

	public function getVehicleVecZ(): float {
		return $this->vehicleVecZ;
	}

	/**
	 * @see PlayerAuthInputFlags
	 */
	public function getInputFlags(): int{
		$flags = $this->inputFlags & ~(
			(1 << PlayerAuthInputFlags::PERFORM_ITEM_STACK_REQUEST) |
			(1 << PlayerAuthInputFlags::PERFORM_ITEM_INTERACTION) |
			(1 << PlayerAuthInputFlags::PERFORM_BLOCK_ACTIONS)
		);

		if($this->itemStackRequest !== null){
			$flags |= 1 << PlayerAuthInputFlags::PERFORM_ITEM_STACK_REQUEST;
		}
		if($this->itemInteractionData !== null){
			$flags |= 1 << PlayerAuthInputFlags::PERFORM_ITEM_INTERACTION;
		}
		if($this->blockActions !== null){
			$flags |= 1 << PlayerAuthInputFlags::PERFORM_BLOCK_ACTIONS;
		}
		if($this->clientPredictedVehicleActorUniqueId !== null){
			$flags |= 1 << PlayerAuthInputFlags::IN_CLIENT_PREDICTED_VEHICLE;
		}

		return $flags;
	}

	/**
	 * @see InputMode
	 */
	public function getInputMode() : int{
		return $this->inputMode;
	}

	/**
	 * @see PlayMode
	 */
	public function getPlayMode() : int{
		return $this->playMode;
	}

	/**
	 * @see InteractionMode
	 */
	public function getInteractionMode() : int{
		return $this->interactionMode;
	}

	public function getVrGazeDirection() : ?Vector3{
		return $this->vrGazeDirection;
	}


	public function getInteractRotation() : Vector2{ 
		return $this->interactRotation; 
	}

	public function getTick() : int{
		return $this->tick;
	}

	public function getDelta() : Vector3{
		return $this->delta;
	}

	public function getItemInteractionData() : ?ItemInteractionData{
		return $this->itemInteractionData;
	}

	public function getItemStackRequest() : ?ItemStackRequest{
		return $this->itemStackRequest;
	}

	public function getRawMove() : Vector2{ return $this->rawMove; }

	/**
	 * @return PlayerBlockAction[]|null
	 */
	public function getBlockActions() : ?array{
		return $this->blockActions;
	}

	public function getClientPredictedVehicleActorUniqueId() : ?int{ return $this->clientPredictedVehicleActorUniqueId; }

	public function getAnalogMoveVecX() : float{ return $this->analogMoveVecX; }

	public function getAnalogMoveVecZ() : float{ return $this->analogMoveVecZ; }

	public function getCameraOrientation() : Vector3{ return $this->cameraOrientation; }

	public function hasFlag(int $flag) : bool{
		return ($this->inputFlags & (1 << $flag)) !== 0;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->pitch = $in->getLFloat();
		$this->yaw = $in->getLFloat();
		$this->position = $in->getVector3();
		$this->moveVecX = $in->getLFloat();
		$this->moveVecZ = $in->getLFloat();
		$this->headYaw = $in->getLFloat();
		$this->inputFlags = $in->getUnsignedVarLong();
		$this->inputMode = $in->getUnsignedVarInt();
		$this->playMode = $in->getUnsignedVarInt();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_0){
			$this->interactionMode = $in->getUnsignedVarInt();
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
			$this->interactRotation = $in->getVector2();
		}elseif($this->playMode === PlayMode::VR){
			$this->vrGazeDirection = $in->getVector3();
		}
		
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
			$this->tick = $in->getUnsignedVarLong();
			$this->delta = $in->getVector3();
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_210){
			if($this->hasFlag(PlayerAuthInputFlags::PERFORM_ITEM_INTERACTION)){
				$this->itemInteractionData = ItemInteractionData::read($in);
			}
			if($this->hasFlag(PlayerAuthInputFlags::PERFORM_ITEM_STACK_REQUEST)){
				$this->itemStackRequest = ItemStackRequest::read($in);
			}
			if($this->hasFlag(PlayerAuthInputFlags::PERFORM_BLOCK_ACTIONS)){
				$this->blockActions = [];
				$max = $in->getVarInt();
				for($i = 0; $i < $max; ++$i){
					$actionType = $in->getVarInt();
					$this->blockActions[] = match(true){
						PlayerBlockActionWithBlockInfo::isValidActionType($actionType) => PlayerBlockActionWithBlockInfo::read($in, $actionType),
						$actionType === PlayerAction::STOP_BREAK => new PlayerBlockActionStopBreak(),
						default => throw new PacketDecodeException("Unexpected block action type $actionType")
					};
				}
			}
		}
		if($this->hasFlag(PlayerAuthInputFlags::IN_CLIENT_PREDICTED_VEHICLE)){

			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_70){
				$this->vehicleVecX = $in->getLFloat();
				$this->vehicleVecZ = $in->getLFloat();
			}
			
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
			    $this->clientPredictedVehicleActorUniqueId = $in->getActorUniqueId();
			}
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_70){
			$this->analogMoveVecX = $in->getLFloat();
			$this->analogMoveVecZ = $in->getLFloat();

			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				$this->cameraOrientation = $in->getVector3();

				if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_50){
					$this->rawMoveVector = $in->getVector2();
				}
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{

		$inputFlags = $this->inputFlags;

		if($this->clientPredictedVehicleActorUniqueId !== null && $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
			$inputFlags |= 1 << PlayerAuthInputFlags::IN_CLIENT_PREDICTED_VEHICLE;
		}

		$out->putLFloat($this->pitch);
		$out->putLFloat($this->yaw);
		$out->putVector3($this->position);
		$out->putLFloat($this->moveVecX);
		$out->putLFloat($this->moveVecZ);
		$out->putLFloat($this->headYaw);
		$out->putUnsignedVarLong($inputFlags);
		$out->putUnsignedVarInt($this->inputMode);
		$out->putUnsignedVarInt($this->playMode);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_0){
			$out->putUnsignedVarInt($this->interactionMode);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
			$out->putVector2($this->interactRotation);
		}elseif($this->playMode === PlayMode::VR){
			assert($this->vrGazeDirection !== null);
			$out->putVector3($this->vrGazeDirection);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
			$out->putUnsignedVarLong($this->tick);
			$out->putVector3($this->delta);
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_210){
			if($this->itemInteractionData !== null){
				$this->itemInteractionData->write($out);
			}
			if($this->itemStackRequest !== null){
				$this->itemStackRequest->write($out);
			}
			if($this->blockActions !== null){
				$out->putVarInt(count($this->blockActions));
				foreach($this->blockActions as $blockAction){
					$out->putVarInt($blockAction->getActionType());
					$blockAction->write($out);
				}
			}
		}

		if($this->clientPredictedVehicleActorUniqueId !== null && $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
			$out->putActorUniqueId($this->clientPredictedVehicleActorUniqueId);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_70){
			$out->putLFloat($this->analogMoveVecX);
			$out->putLFloat($this->analogMoveVecZ);
			
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
				$out->putVector3($this->cameraOrientation);

				if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_50){
					$out->putVector2($this->rawMoveVector);
				}
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerAuthInput($this);
	}
}
