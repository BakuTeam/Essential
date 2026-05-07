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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class IntGameRule extends GameRule{
	use GetTypeIdFromConstTrait;

	public const ID = GameRuleType::INT;

	private int $value;

	public function __construct(int $value, bool $isPlayerModifiable){
		parent::__construct($isPlayerModifiable);
		$this->value = $value;
	}

	public function getValue() : int{
		return $this->value;
	}

	public function encode(PacketSerializer $out, bool $isStartGame = false) : void{
		if($isStartGame || $out->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_111){
			$out->putUnsignedVarInt($this->value);
		}else{
			$out->putLInt($this->value);
		}
	}

	public static function decode(PacketSerializer $in, bool $isPlayerModifiable, bool $isStartGame = false) : self{
		return new self(($isStartGame || $in->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_111) ? $in->getUnsignedVarInt() : $in->getLInt(), $isPlayerModifiable);
	}
}
