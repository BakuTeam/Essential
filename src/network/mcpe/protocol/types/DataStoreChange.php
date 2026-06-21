<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
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
