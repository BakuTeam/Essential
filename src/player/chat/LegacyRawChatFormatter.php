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

namespace pocketmine\player\chat;

use function str_replace;

/**
 * Legacy raw string chat formatter with the same behaviour as the old PlayerChatEvent::setFormat() API.
 * The format string should contain the placeholders {%0} and {%1} for the username and message respectively.
 */
final class LegacyRawChatFormatter implements ChatFormatter{

	public function __construct(
		private string $format
	){}

	public function format(string $username, string $message) : string{
		return str_replace(["{%0}", "{%1}"], [$username, $message], $this->format);
	}
}
