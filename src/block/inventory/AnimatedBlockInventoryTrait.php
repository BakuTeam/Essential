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

namespace pocketmine\block\inventory;

use pocketmine\player\Player;
use pocketmine\world\sound\Sound;
use function count;

trait AnimatedBlockInventoryTrait{
	use BlockInventoryTrait;

	public function getViewerCount() : int{
		return count($this->getViewers());
	}

	/**
	 * @return Player[]
	 * @phpstan-return array<int, Player>
	 */
	abstract public function getViewers() : array;

	abstract protected function getOpenSound() : Sound;

	abstract protected function getCloseSound() : Sound;

	public function onOpen(Player $who) : void{
		parent::onOpen($who);

		if($this->holder->isValid() && $this->getViewerCount() === 1){
			//TODO: this crap really shouldn't be managed by the inventory
			$this->animateBlock(true);
			$this->holder->getWorld()->addSound($this->holder->add(0.5, 0.5, 0.5), $this->getOpenSound());
		}
	}

	abstract protected function animateBlock(bool $isOpen) : void;

	public function onClose(Player $who) : void{
		if($this->holder->isValid() && $this->getViewerCount() === 1){
			//TODO: this crap really shouldn't be managed by the inventory
			$this->animateBlock(false);
			$this->holder->getWorld()->addSound($this->holder->add(0.5, 0.5, 0.5), $this->getCloseSound());
		}
		parent::onClose($who);
	}
}
