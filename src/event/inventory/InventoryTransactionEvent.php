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

namespace pocketmine\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\inventory\transaction\InventoryTransaction;

/**
 * Called when a player performs actions involving items in inventories.
 *
 * This may involve multiple inventories, and may include actions such as:
 * - moving items from one slot to another
 * - splitting itemstacks
 * - dragging itemstacks across inventory slots (slot painting)
 * - dropping an item on the ground
 * - taking an item from the creative inventory menu
 * - destroying (trashing) an item
 *
 * @see https://doc.pmmp.io/en/rtfd/developer-reference/inventory-transactions.html for more information on inventory transactions
 */
class InventoryTransactionEvent extends Event implements Cancellable{
	use CancellableTrait;

	public function __construct(private InventoryTransaction $transaction){}

	public function getTransaction() : InventoryTransaction{
		return $this->transaction;
	}
}
