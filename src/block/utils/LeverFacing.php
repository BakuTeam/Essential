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

use pocketmine\math\Facing;
use pocketmine\utils\LegacyEnumShimTrait;

/**
 * TODO: These tags need to be removed once we get rid of LegacyEnumShimTrait (PM6)
 *  These are retained for backwards compatibility only.
 *
 * @method static LeverFacing DOWN_AXIS_X()
 * @method static LeverFacing DOWN_AXIS_Z()
 * @method static LeverFacing EAST()
 * @method static LeverFacing NORTH()
 * @method static LeverFacing SOUTH()
 * @method static LeverFacing UP_AXIS_X()
 * @method static LeverFacing UP_AXIS_Z()
 * @method static LeverFacing WEST()
 */
enum LeverFacing{
	use LegacyEnumShimTrait;

	case UP_AXIS_X;
	case UP_AXIS_Z;
	case DOWN_AXIS_X;
	case DOWN_AXIS_Z;
	case NORTH;
	case EAST;
	case SOUTH;
	case WEST;

	public function getFacing() : int{
		return match($this){
			self::UP_AXIS_X, self::UP_AXIS_Z => Facing::UP,
			self::DOWN_AXIS_X, self::DOWN_AXIS_Z => Facing::DOWN,
			self::NORTH => Facing::NORTH,
			self::EAST => Facing::EAST,
			self::SOUTH => Facing::SOUTH,
			self::WEST => Facing::WEST,
		};
	}
}
