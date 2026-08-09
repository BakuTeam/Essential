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

namespace pocketmine\event;

/**
 * This interface is implemented by an Event subclass if and only if it can be cancelled.
 *
 * The cancellation of an event directly affects whether downstream event handlers
 * without `@handleCancelled` will be called with this event.
 * Implementations may provide a direct setter for cancellation (typically by using `CancellableTrait`)
 * or implement an alternative logic (such as a function on another data field) for `isCancelled()`.
 */
interface Cancellable{
	/**
	 * Returns whether this instance of the event is currently cancelled.
	 *
	 * If it is cancelled, only downstream handlers that declare `@handleCancelled` will be called with this event.
	 */
	public function isCancelled() : bool;
}
