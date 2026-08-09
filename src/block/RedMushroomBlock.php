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

namespace pocketmine\block;

use pocketmine\block\utils\MushroomBlockType;
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use function mt_rand;

class RedMushroomBlock extends Opaque{
	protected MushroomBlockType $mushroomBlockType = MushroomBlockType::ALL_CAP;

	public function describeBlockItemState(RuntimeDataDescriber $w) : void{
		//these blocks always drop as all-cap, but may exist in other forms in the inventory (particularly creative),
		//so this information needs to be kept in the type info
		$w->enum($this->mushroomBlockType);
	}

	public function getMushroomBlockType() : MushroomBlockType{ return $this->mushroomBlockType; }

	/** @return $this */
	public function setMushroomBlockType(MushroomBlockType $mushroomBlockType) : self{
		$this->mushroomBlockType = $mushroomBlockType;
		return $this;
	}

	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			VanillaBlocks::RED_MUSHROOM()->asItem()->setCount(mt_rand(0, 2))
		];
	}

	public function isAffectedBySilkTouch() : bool{
		return true;
	}

	public function getSilkTouchDrops(Item $item) : array{
		return [(clone $this)->setMushroomBlockType(MushroomBlockType::ALL_CAP)->asItem()];
	}

	public function getPickedItem(bool $addUserData = false) : Item{
		return (clone $this)->setMushroomBlockType(MushroomBlockType::ALL_CAP)->asItem();
	}
}
