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
 * @method static SupportType CENTER()
 * @method static SupportType EDGE()
 * @method static SupportType FULL()
 * @method static SupportType NONE()
 */
enum SupportType{
	use LegacyEnumShimTrait;

	case FULL;
	case CENTER;
	case EDGE;
	case NONE;

	public function hasEdgeSupport() : bool{
		return $this === self::EDGE || $this === self::FULL;
	}

	public function hasCenterSupport() : bool{
		return $this === self::CENTER || $this === self::FULL;
	}
}
