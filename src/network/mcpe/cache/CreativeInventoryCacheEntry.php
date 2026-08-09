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

namespace pocketmine\network\mcpe\cache;

use pocketmine\inventory\CreativeCategory;
use pocketmine\inventory\CreativeGroup;
use pocketmine\network\mcpe\protocol\types\inventory\CreativeItemEntry;

final class CreativeInventoryCacheEntry{

	/**
	 * @param CreativeCategory[]     $categories
	 * @param CreativeGroup[]|null[] $groups
	 * @param CreativeItemEntry[]    $items
	 *
	 * @phpstan-param list<CreativeCategory>   $categories
	 * @phpstan-param list<CreativeGroup|null> $groups
	 * @phpstan-param list<CreativeItemEntry>  $items
	 */
	public function __construct(
		public readonly array $categories,
		public readonly array $groups,
		public readonly array $items,
	){
		//NOOP
	}
}
