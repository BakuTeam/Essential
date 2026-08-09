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

namespace pocketmine\item\enchantment;

final class EnchantmentTransfer{

	private const RARITY_TO_MULTIPLIER = [
		Rarity::COMMON => 1,
		Rarity::UNCOMMON => 1,
		Rarity::RARE => 2,
		Rarity::MYTHIC => 4,
	];
	private const SOURCE_RARITY_TO_MULTIPLIER = [
		Rarity::COMMON => 1,
		Rarity::UNCOMMON => 2,
		Rarity::RARE => 2,
		Rarity::MYTHIC => 2,
	];

	private function __construct(){
		//NOOP
	}

	public static function getCost(Enchantment $type, int $levelDifference, bool $transferFromItem) : int{
		$rarity = $type->getRarity();
		$cost = self::RARITY_TO_MULTIPLIER[$rarity] * $levelDifference;
		if($transferFromItem){
			$cost *= self::SOURCE_RARITY_TO_MULTIPLIER[$rarity];
		}
		return $cost;
	}
}
