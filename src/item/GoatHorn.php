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

use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\GoatHornSound;

class GoatHorn extends Item implements Releasable{

	private GoatHornType $goatHornType = GoatHornType::PONDER;

	protected function describeState(RuntimeDataDescriber $w) : void{
		$w->enum($this->goatHornType);
	}

	public function getHornType() : GoatHornType{ return $this->goatHornType; }

	/**
	 * @return $this
	 */
	public function setHornType(GoatHornType $type) : self{
		$this->goatHornType = $type;
		return $this;
	}

	public function getMaxStackSize() : int{
		return 1;
	}

	public function getCooldownTicks() : int{
		return 140;
	}

	public function getCooldownTag() : ?string{
		return ItemCooldownTags::GOAT_HORN;
	}

	public function canStartUsingItem(Player $player) : bool{
		return true;
	}

	public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult{
		$position = $player->getPosition();
		$position->getWorld()->addSound($position, new GoatHornSound($this->goatHornType));

		return ItemUseResult::SUCCESS;
	}
}
