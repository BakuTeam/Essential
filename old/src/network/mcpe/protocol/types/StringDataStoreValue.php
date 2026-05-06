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

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class StringDataStoreValue extends DataStoreValue{
	public const ID = DataStoreValueType::STRING;

	public function __construct(private string $value){}

	public function getValue() : string{ return $this->value; }

	public function getTypeId() : int{ return self::ID; }

	public function write(PacketSerializer $out) : void{
		$out->putString($this->value);
	}

	public static function read(PacketSerializer $in) : self{
		return new self($in->getString());
	}
}
