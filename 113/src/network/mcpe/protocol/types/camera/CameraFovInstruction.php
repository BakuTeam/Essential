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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function is_int;

final class CameraFovInstruction{

	/**
	 * @see CameraSetInstructionEaseType
	 */
	private string $easeType;

	public function __construct(
		private float $fieldOfView,
		private float $easeTime,
		int|string $easeType,
		private bool $clear,
	){
		$this->easeType = is_int($easeType) ? CameraSetInstructionEaseType::toString($easeType) ?? throw new \InvalidArgumentException("Invalid ease type $easeType") : $easeType;
	}

	public function getFieldOfView() : float{ return $this->fieldOfView; }

	public function getEaseTime() : float{ return $this->easeTime; }

	/**
	 * @see CameraSetInstructionEaseType
	 */
	public function getEaseType() : string{ return $this->easeType; }

	public function getClear() : bool{ return $this->clear; }

	public static function read(PacketSerializer $in) : self{
		$fieldOfView = $in->getLFloat();
		$easeTime = $in->getLFloat();
		$easeType = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10 ? $in->getString() : CameraSetInstructionEaseType::toString($in->getByte());
		$clear = $in->getBool();
		return new self(
			$fieldOfView,
			$easeTime,
			$easeType ?? throw new \InvalidArgumentException("Invalid ease type"),
			$clear
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->fieldOfView);
		$out->putLFloat($this->easeTime);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
			$out->putString($this->easeType);
		}else{
			$out->putByte(CameraSetInstructionEaseType::fromString($this->easeType) ?? throw new \InvalidArgumentException("Invalid ease type " . $this->easeType));
		}
		$out->putBool($this->clear);
	}
}
