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

use pmmp\encoding\Byte;
use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

/**
 * @see PlayerArmorDamagePacket
 */
final class ArmorSlotAndDamagePair{

	public function __construct(
		private ArmorSlot $slot,
		private int $damage
	){}

	public function getSlot() : ArmorSlot{ return $this->slot; }

	public function getDamage() : int{ return $this->damage; }

	public static function read(PacketSerializer $in) : self{
		$slot = ArmorSlot::fromPacket($in->getByte());
		$damage = $in->getShort();

		return new self(
			$slot,
			$damage
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putByte($this->slot->value);
		$out->putShort($this->damage);
	}
}