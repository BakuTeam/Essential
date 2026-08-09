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

namespace pocketmine\event\entity;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

/**
 * Called when an item entity tries to merge into another item entity.
 *
 * @phpstan-extends EntityEvent<ItemEntity>
 */
class ItemMergeEvent extends EntityEvent implements Cancellable{
	use CancellableTrait;

	public function __construct(
		ItemEntity $entity,
		protected ItemEntity $target
	){
		$this->entity = $entity;
	}

	/**
	 * Returns the merge destination.
	 */
	public function getTarget() : ItemEntity{
		return $this->target;
	}

}
