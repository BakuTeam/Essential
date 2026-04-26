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

use pmmp\encoding\ByteBufferReader;
use pmmp\encoding\ByteBufferWriter;
use pmmp\encoding\LE;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class FloatPackSetting extends PackSetting{
	public const ID = PackSettingType::FLOAT;

	private float $value;

	public function __construct(string $name, float $value){
		parent::__construct($name);
		$this->value = $value;
	}

	public function getValue() : float{
		return $this->value;
	}

	public function getTypeId() : PackSettingType{
		return self::ID;
	}

	public function write(PacketSerializer $out) : void{
		$out->putFloat($this->value);
	}

	public static function read(PacketSerializer $in, string $name) : self{
		return new self($name, $in->getFloat());
	}
}