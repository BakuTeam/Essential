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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function is_int;

final class CameraRotationOption{
	/** @see CameraSetInstructionEaseType */
	private string $easeType;

	public function __construct(
		private Vector3 $value,
		private float $time,
		int|string $easeType
	){
		$this->easeType = is_int($easeType) ? (CameraSetInstructionEaseType::toString($easeType) ?? "") : $easeType;
	}

	public function getValue() : Vector3{ return $this->value; }

	public function getTime() : float{ return $this->time; }

	public function getEaseType() : string{ return $this->easeType; }

	public static function read(PacketSerializer $in) : self{
		$value = $in->getVector3();
		$time = $in->getLFloat();
		$easeType = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0 ? $in->getString() : "";
		return new self($value, $time, $easeType);
	}

	public function write(PacketSerializer $out) : void{
		$out->putVector3($this->value);
		$out->putLFloat($this->time);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			$out->putString($this->easeType);
		}
	}
}
