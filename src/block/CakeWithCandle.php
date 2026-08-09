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

use pocketmine\block\utils\CandleTrait;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class CakeWithCandle extends BaseCake{
	use CandleTrait {
		onInteract as onInteractCandle;
	}

	protected function recalculateCollisionBoxes() : array{
		return [
			AxisAlignedBB::one()
				->contract(1 / 16, 0, 1 / 16)
				->trim(Facing::UP, 0.5) //TODO: not sure if the candle affects height
		];
	}

	public function getCandle() : Candle{
		return VanillaBlocks::CANDLE();
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		if($this->lit && $face !== Facing::UP){
			return true;
		}
		if($this->onInteractCandle($item, $face, $clickVector, $player, $returnedItems)){
			return true;
		}

		return parent::onInteract($item, $face, $clickVector, $player, $returnedItems);
	}

	public function getDropsForCompatibleTool(Item $item) : array{
		return [$this->getCandle()->asItem()];
	}

	public function getPickedItem(bool $addUserData = false) : Item{
		return VanillaBlocks::CAKE()->asItem();
	}

	public function getResidue() : Block{
		return VanillaBlocks::CAKE()->setBites(1);
	}

	public function onConsume(Living $consumer) : void{
		parent::onConsume($consumer);
		$this->position->getWorld()->dropItem($this->position->add(0.5, 0.5, 0.5), $this->getCandle()->asItem());
	}
}
