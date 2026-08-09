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

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\world\sound\BucketEmptyWaterSound;
use pocketmine\world\sound\BucketFillWaterSound;
use pocketmine\world\sound\Sound;

class Water extends Liquid{

	public function getLightFilter() : int{
		return 2;
	}

	public function getBucketFillSound() : Sound{
		return new BucketFillWaterSound();
	}

	public function getBucketEmptySound() : Sound{
		return new BucketEmptyWaterSound();
	}

	public function tickRate() : int{
		return 5;
	}

	public function getMinAdjacentSourcesToFormSource() : ?int{
		return 2;
	}

	public function onEntityInside(Entity $entity) : bool{
		$entity->resetFallDistance();
		if($entity->isOnFire()){
			$entity->extinguish();
		}
		return true;
	}
}
