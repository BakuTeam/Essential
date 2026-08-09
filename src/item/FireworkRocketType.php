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

namespace pocketmine\item;

use pocketmine\utils\LegacyEnumShimTrait;
use pocketmine\world\sound\FireworkExplosionSound;
use pocketmine\world\sound\FireworkLargeExplosionSound;
use pocketmine\world\sound\Sound;
use function spl_object_id;

enum FireworkRocketType{
	use LegacyEnumShimTrait;

	case SMALL_BALL;
	case LARGE_BALL;
	case STAR;
	case CREEPER;
	case BURST;

	public function getSound() : Sound{
		/** @phpstan-var array<int, Sound> $cache */
		static $cache = [];

		return $cache[spl_object_id($this)] ??= match($this){
			self::SMALL_BALL => new FireworkExplosionSound(),
			self::LARGE_BALL => new FireworkLargeExplosionSound(),
			self::STAR => new FireworkExplosionSound(),
			self::CREEPER => new FireworkExplosionSound(),
			self::BURST => new FireworkExplosionSound(),
		};
	}
}
