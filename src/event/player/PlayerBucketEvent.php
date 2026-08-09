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

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;

/**
 * @allowHandle
 */
abstract class PlayerBucketEvent extends PlayerEvent implements Cancellable{
	use CancellableTrait;

	public function __construct(
		Player $who,
		private Block $blockClicked,
		private int $blockFace,
		private Item $bucket,
		private Item $itemInHand
	){
		$this->player = $who;
	}

	/**
	 * Returns the bucket used in this event
	 */
	public function getBucket() : Item{
		return $this->bucket;
	}

	/**
	 * Returns the item in hand after the event
	 */
	public function getItem() : Item{
		return $this->itemInHand;
	}

	public function setItem(Item $item) : void{
		$this->itemInHand = $item;
	}

	public function getBlockClicked() : Block{
		return $this->blockClicked;
	}

	public function getBlockFace() : int{
		return $this->blockFace;
	}
}
