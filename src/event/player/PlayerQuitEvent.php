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

namespace pocketmine\event\player;

use pocketmine\lang\Translatable;
use pocketmine\player\Player;

/**
 * Called when a player disconnects from the server for any reason.
 *
 * Some possible reasons include:
 * - being kicked by an operator
 * - disconnecting from the game
 * - timeout due to network connectivity issues
 *
 * @see PlayerKickEvent
 */
class PlayerQuitEvent extends PlayerEvent{
	public function __construct(
		Player $player,
		protected Translatable|string $quitMessage,
		protected Translatable|string $quitReason
	){
		$this->player = $player;
	}

	/**
	 * Sets the quit message broadcasted to other players.
	 */
	public function setQuitMessage(Translatable|string $quitMessage) : void{
		$this->quitMessage = $quitMessage;
	}

	/**
	 * Returns the quit message broadcasted to other players, e.g. "Steve left the game".
	 */
	public function getQuitMessage() : Translatable|string{
		return $this->quitMessage;
	}

	/**
	 * Returns the disconnect reason shown in the server log and on the console.
	 */
	public function getQuitReason() : Translatable|string{
		return $this->quitReason;
	}
}
