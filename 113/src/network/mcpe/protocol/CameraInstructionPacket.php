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

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\camera\CameraFadeInstruction;
use pocketmine\network\mcpe\protocol\types\camera\CameraFovInstruction;
use pocketmine\network\mcpe\protocol\types\camera\CameraSetInstruction;
use pocketmine\network\mcpe\protocol\types\camera\CameraTargetInstruction;

class CameraInstructionPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_INSTRUCTION_PACKET;

	private ?CameraSetInstruction $set;
	private ?bool $clear;
	private ?CameraFadeInstruction $fade;
	private ?CameraTargetInstruction $target;
	private ?bool $removeTarget;
	private ?CameraFovInstruction $fieldOfView;

	/**
	 * @generate-create-func
	 */
	public static function create(?CameraSetInstruction $set, ?bool $clear, ?CameraFadeInstruction $fade, ?CameraTargetInstruction $target, ?bool $removeTarget, ?CameraFovInstruction $fieldOfView) : self{
		$result = new self;
		$result->set = $set;
		$result->clear = $clear;
		$result->fade = $fade;
		$result->target = $target;
		$result->removeTarget = $removeTarget;
		$result->fieldOfView = $fieldOfView;
		return $result;
	}

	public function getSet() : ?CameraSetInstruction{ return $this->set; }

	public function getClear() : ?bool{ return $this->clear; }

	public function getFade() : ?CameraFadeInstruction{ return $this->fade; }

	public function getTarget() : ?CameraTargetInstruction{ return $this->target; }

	public function getRemoveTarget() : ?bool{ return $this->removeTarget; }

	public function getFieldOfView() : ?CameraFovInstruction{ return $this->fieldOfView; }

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30){
			$this->set = $in->readOptional(fn() => CameraSetInstruction::read($in));
			$this->clear = $in->readOptional($in->getBool(...));
			$this->fade = $in->readOptional(fn() => CameraFadeInstruction::read($in));
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
				$this->target = $in->readOptional(fn() => CameraTargetInstruction::read($in));
				$this->removeTarget = $in->readOptional($in->getBool(...));
				if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_100){
					$this->fieldOfView = $in->readOptional(fn() => CameraFovInstruction::read($in));
				}
			}
		}else{
			$this->fromNBT($in->getNbtCompoundRoot());
		}
	}

	protected function fromNBT(CompoundTag $nbt) : void{
		$setTag = $nbt->getCompoundTag("set");
		$this->set = $setTag === null ? null : CameraSetInstruction::fromNBT($setTag);

		$this->clear = $nbt->getTag("clear") === null ? null : $nbt->getByte("clear") !== 0;

		$fadeTag = $nbt->getCompoundTag("fade");
		$this->fade = $fadeTag === null ? null : CameraFadeInstruction::fromNBT($fadeTag);
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30){
			$out->writeOptional($this->set, fn(CameraSetInstruction $v) => $v->write($out));
			$out->writeOptional($this->clear, $out->putBool(...));
			$out->writeOptional($this->fade, fn(CameraFadeInstruction $v) => $v->write($out));
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
				$out->writeOptional($this->target, fn(CameraTargetInstruction $v) => $v->write($out));
				$out->writeOptional($this->removeTarget, $out->putBool(...));
				if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_100){
					$out->writeOptional($this->fieldOfView, fn(CameraFovInstruction $v) => $v->write($out));
				}
			}
			if ($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_120) {
				$out->putByte(0);
				$out->putByte(0);
				$out->putByte(0);
			}
		}else{
			$data = new CacheableNbt($this->toNBT());
			$out->put($data->getEncodedNbt());
		}
	}

	protected function toNBT() : CompoundTag{
		$nbt = CompoundTag::create();

		if($this->set !== null){
			$nbt->setTag("set", $this->set->toNBT());
		}
		if($this->clear !== null){
			$nbt->setByte("clear", (int) $this->clear);
		}
		if($this->fade !== null){
			$nbt->setTag("fade", $this->fade->toNBT());
		}

		return $nbt;
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCameraInstruction($this);
	}
}
