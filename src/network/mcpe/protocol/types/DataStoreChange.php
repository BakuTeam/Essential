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

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class DataStoreChange extends DataStore{
	public const ID = DataStoreType::CHANGE;

	public function __construct(
		private string $name,
		private string $property,
		private int $updateCount,
		private DataStoreValue $data
	){}

	public function getTypeId() : int{ return self::ID; }

	public function getName() : string{ return $this->name; }

	public function getProperty() : string{ return $this->property; }

	public function getUpdateCount() : int{ return $this->updateCount; }

	public function getData() : DataStoreValue{ return $this->data; }

	public static function read(PacketSerializer $in) : self{
		$name = $in->getString();
		$property = $in->getString();
		$updateCount = $in->getUnsignedVarInt();
		$data = match($in->getUnsignedVarInt()){
			DataStoreValueType::DOUBLE => DoubleDataStoreValue::read($in),
			DataStoreValueType::BOOL => BoolDataStoreValue::read($in),
			DataStoreValueType::STRING => StringDataStoreValue::read($in),
			default => throw new PacketDecodeException("Unknown DataStoreValueType")
		};

		return new self($name, $property, $updateCount, $data);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->name);
		$out->putString($this->property);
		$out->putUnsignedVarInt($this->updateCount);
		$out->putUnsignedVarInt($this->data->getTypeId());
		$this->data->write($out);
	}
}
