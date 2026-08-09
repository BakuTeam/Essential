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
