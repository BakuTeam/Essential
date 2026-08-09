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

namespace pocketmine\player;

use pocketmine\nbt\tag\CompoundTag;

/**
 * Handles storage of player data. Implementations must treat player names in a case-insensitive manner.
 */
interface PlayerDataProvider{

	/**
	 * Returns whether there are any data associated with the given player name.
	 */
	public function hasData(string $name) : bool;

	/**
	 * Returns the data associated with the given player name, or null if there is no data.
	 * TODO: we need an async version of this
	 *
	 * @throws PlayerDataLoadException
	 */
	public function loadData(string $name) : ?CompoundTag;

	/**
	 * Saves data for the give player name.
	 *
	 * @throws PlayerDataSaveException
	 */
	public function saveData(string $name, CompoundTag $data) : void;
}
