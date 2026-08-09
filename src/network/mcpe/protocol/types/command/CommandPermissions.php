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

namespace pocketmine\network\mcpe\protocol\types\command;

use function array_flip;

final class CommandPermissions{
	private function __construct(){
		//NOOP
	}

	public const NORMAL = 0;
	public const OPERATOR = 1;
	public const AUTOMATION = 2; //command blocks
	public const HOST = 3; //hosting player on LAN multiplayer
	public const OWNER = 4; //server terminal on BDS
	public const INTERNAL = 5;

	private const PERMISSION_NAMES = [
		self::NORMAL => "any",
		self::OPERATOR => "gamedirectors",
		self::AUTOMATION => "admin",
		self::HOST => "host",
		self::OWNER => "owner",
		self::INTERNAL => "internal",
	];

	public static function toName(int $value) : string{
		return self::PERMISSION_NAMES[$value] ?? throw new \InvalidArgumentException("Invalid raw value \"$value\" for CommandPermission");
	}

	public static function fromName(string $name) : int{
		static $cache = null;
		if($cache === null){
			$cache = array_flip(self::PERMISSION_NAMES);
		}

		return $cache[$name] ?? throw new \InvalidArgumentException("Invalid raw value \"$name\" for CommandPermission");
	}
}
