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

final class CameraProgressOption{
	/** @see CameraSetInstructionEaseType */
	private string $easeType;

	public function __construct(
		private float $value,
		private float $time,
		int|string $easeType
	){
		$this->easeType = is_int($easeType) ? (CameraSetInstructionEaseType::toString($easeType) ?? "") : $easeType;
	}

	public function getValue() : float{ return $this->value; }

	public function getTime() : float{ return $this->time; }

	public function getEaseType() : string{ return $this->easeType; }

	public static function read(PacketSerializer $in) : self{
		$value = $in->getLFloat();
		$time = $in->getLFloat();
		$easeType = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0 ? $in->getString() : "";
		return new self($value, $time, $easeType);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->value);
		$out->putLFloat($this->time);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			$out->putString($this->easeType);
		}
	}
}
