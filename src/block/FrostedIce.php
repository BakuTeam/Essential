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

use pocketmine\block\utils\AgeableTrait;
use pocketmine\block\utils\BlockEventHelper;
use function mt_rand;

class FrostedIce extends Ice{
	use AgeableTrait;

	public const MAX_AGE = 3;

	public function onNearbyBlockChange() : void{
		$this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, mt_rand(20, 40));
	}

	public function onRandomTick() : void{
		$world = $this->position->getWorld();
		if((!$this->checkAdjacentBlocks(4) || mt_rand(0, 2) === 0) &&
			$world->getHighestAdjacentFullLightAt($this->position->x, $this->position->y, $this->position->z) >= 12 - $this->age){
			if($this->tryMelt()){
				foreach($this->getAllSides() as $block){
					if($block instanceof FrostedIce){
						$block->tryMelt();
					}
				}
			}
		}else{
			$world->scheduleDelayedBlockUpdate($this->position, mt_rand(20, 40));
		}
	}

	public function onScheduledUpdate() : void{
		$this->onRandomTick();
	}

	private function checkAdjacentBlocks(int $requirement) : bool{
		$found = 0;
		for($x = -1; $x <= 1; ++$x){
			for($z = -1; $z <= 1; ++$z){
				if($x === 0 && $z === 0){
					continue;
				}
				if(
					$this->position->getWorld()->getBlockAt($this->position->x + $x, $this->position->y, $this->position->z + $z) instanceof FrostedIce &&
					++$found >= $requirement
				){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Updates the age of the ice, destroying it if appropriate.
	 *
	 * @return bool Whether the ice was destroyed.
	 */
	private function tryMelt() : bool{
		$world = $this->position->getWorld();
		if($this->age >= self::MAX_AGE){
			BlockEventHelper::melt($this, VanillaBlocks::WATER());
			return true;
		}

		$this->age++;
		$world->setBlock($this->position, $this);
		$world->scheduleDelayedBlockUpdate($this->position, mt_rand(20, 40));
		return false;
	}
}
