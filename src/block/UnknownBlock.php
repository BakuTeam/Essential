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

use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;

/**
 * Represents a block which is unrecognized or not implemented.
 */
class UnknownBlock extends Transparent{

	private int $stateData;

	public function __construct(BlockIdentifier $idInfo, BlockTypeInfo $typeInfo, int $stateData){
		$this->stateData = $stateData;
		parent::__construct($idInfo, "Unknown", $typeInfo);
	}

	public function describeBlockItemState(RuntimeDataDescriber $w) : void{
		//use type instead of state, so we don't lose any information like colour
		//this might be an improperly registered plugin block
		$w->int(Block::INTERNAL_STATE_DATA_BITS, $this->stateData);
	}

	public function canBePlaced() : bool{
		return false;
	}

	public function getDrops(Item $item) : array{
		return [];
	}
}
