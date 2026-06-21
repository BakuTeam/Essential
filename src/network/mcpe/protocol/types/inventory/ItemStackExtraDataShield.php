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

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

/**
 * Extension of ItemStackExtraData for shield items, which have an additional field for the blocking tick.
 */
final class ItemStackExtraDataShield extends ItemStackExtraData{

	/**
	 * @param string[] $canPlaceOn
	 * @param string[] $canDestroy
	 */
	public function __construct(
		?CompoundTag $nbt,
		array $canPlaceOn,
		array $canDestroy,
		private int $blockingTick
	){
		parent::__construct($nbt, $canPlaceOn, $canDestroy);
	}

	public function getBlockingTick() : int{ return $this->blockingTick; }

	public static function read(PacketSerializer $in) : self{
		$base = parent::read($in);
		$blockingTick = $in->getLLong();

		return new self($base->getNbt(), $base->getCanPlaceOn(), $base->getCanDestroy(), $blockingTick);
	}

	public function write(PacketSerializer $out) : void{
		parent::write($out);
		$out->putLLong($this->blockingTick);
	}
}
