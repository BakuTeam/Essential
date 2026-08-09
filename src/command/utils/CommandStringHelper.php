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

namespace pocketmine\command\utils;

use pocketmine\utils\AssumptionFailedError;
use function preg_last_error_msg;
use function preg_match_all;
use function preg_replace;

final class CommandStringHelper{

	private function __construct(){
		//NOOP
	}

	/**
	 * Parses a command string into its component parts. Parts of the string which are inside unescaped quotes are
	 * considered as one argument.
	 *
	 * Examples:
	 * - `give "steve jobs" apple` -> ['give', 'steve jobs', 'apple']
	 * - `say "This is a \"string containing quotes\""` -> ['say', 'This is a "string containing quotes"']
	 *
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public static function parseQuoteAware(string $commandLine) : array{
		$args = [];
		preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $commandLine, $matches);
		foreach($matches[0] as $k => $_){
			for($i = 1; $i <= 2; ++$i){
				if($matches[$i][$k] !== ""){
					$match = $matches[$i][$k];
					$args[] = preg_replace('/\\\\([\\\\"])/u', '$1', $match) ?? throw new AssumptionFailedError(preg_last_error_msg());
					break;
				}
			}
		}

		return $args;
	}
}
