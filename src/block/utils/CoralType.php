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

namespace pocketmine\block\utils;

use pocketmine\utils\LegacyEnumShimTrait;

/**
 * TODO: These tags need to be removed once we get rid of LegacyEnumShimTrait (PM6)
 *  These are retained for backwards compatibility only.
 *
 * @method static CoralType BRAIN()
 * @method static CoralType BUBBLE()
 * @method static CoralType FIRE()
 * @method static CoralType HORN()
 * @method static CoralType TUBE()
 */
enum CoralType{
	use LegacyEnumShimTrait;

	case TUBE;
	case BRAIN;
	case BUBBLE;
	case FIRE;
	case HORN;

	public function getDisplayName() : string{
		return match($this){
			self::TUBE => "Tube",
			self::BRAIN => "Brain",
			self::BUBBLE => "Bubble",
			self::FIRE => "Fire",
			self::HORN => "Horn",
		};
	}
}
