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

namespace pocketmine\plugin;

final class PluginDescriptionCommandEntry{

	/**
	 * @param string[] $aliases
	 * @phpstan-param list<string> $aliases
	 */
	public function __construct(
		private ?string $description,
		private ?string $usageMessage,
		private array $aliases,
		private string $permission,
		private ?string $permissionDeniedMessage,
	){}

	public function getDescription() : ?string{ return $this->description; }

	public function getUsageMessage() : ?string{ return $this->usageMessage; }

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public function getAliases() : array{ return $this->aliases; }

	public function getPermission() : string{ return $this->permission; }

	public function getPermissionDeniedMessage() : ?string{ return $this->permissionDeniedMessage; }
}
