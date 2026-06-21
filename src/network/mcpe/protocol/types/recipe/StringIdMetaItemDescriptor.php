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

namespace pocketmine\network\mcpe\protocol\types\recipe;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;

final class StringIdMetaItemDescriptor implements ItemDescriptor{
	use GetTypeIdFromConstTrait;

	public const ID = ItemDescriptorType::STRING_ID_META;

	public function __construct(
		private string $id,
		private int $meta
	){
		if($meta < 0){
			throw new \InvalidArgumentException("Meta cannot be negative");
		}
	}

	public function getId() : string{ return $this->id; }

	public function getMeta() : int{ return $this->meta; }

	public static function read(PacketSerializer $in) : self{
		$stringId = $in->getString();
		$meta = $in->getLShort();

		return new self($stringId, $meta);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->id);
		$out->putLShort($this->meta);
	}
}
